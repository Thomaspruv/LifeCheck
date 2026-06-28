<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\UserSetting;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleCalendarEvent;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private const CALENDAR_SUMMARY = 'LifeCheck — Humeur';
    private const CALENDAR_DESCRIPTION = 'Humeur quotidienne synchronisée depuis LifeCheck.';

    private ?GoogleClient $client = null;

    /**
     * Get or initialize the Google Client.
     */
    public function getClient(?int $userId = null): GoogleClient
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(route('google-calendar.callback'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setApprovalPrompt('force');
        $client->addScope(GoogleCalendar::CALENDAR_EVENTS);
        $client->setIncludeGrantedScopes(true);

        // If user ID is provided and they have stored tokens, set them
        if ($userId !== null) {
            $settings = UserSetting::where('user_id', $userId)->first();
            if ($settings && $settings->google_access_token) {
                $client->setAccessToken(json_decode($settings->google_access_token, true));

                // Refresh token if expired
                if ($client->isAccessTokenExpired()) {
                    $refreshToken = $settings->google_refresh_token;
                    if ($refreshToken) {
                        try {
                            $client->fetchAccessTokenWithRefreshToken($refreshToken);
                            $this->storeAccessToken($userId, $client->getAccessToken());
                        } catch (\Exception $e) {
                            Log::warning('Google token refresh failed: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        $this->client = $client;
        return $client;
    }

    /**
     * Generate the Google OAuth URL for the user.
     */
    public function getAuthUrl(): string
    {
        $client = $this->getClient();
        return $client->createAuthUrl();
    }

    /**
     * Handle the OAuth callback — exchange authorization code for tokens.
     */
    public function handleCallback(string $authorizationCode, int $userId): bool
    {
        try {
            $client = $this->getClient();
            $accessToken = $client->fetchAccessTokenWithAuthCode($authorizationCode);

            if (isset($accessToken['error'])) {
                Log::error('Google OAuth error: ' . ($accessToken['error_description'] ?? $accessToken['error']));
                return false;
            }

            $client->setAccessToken($accessToken);

            // Create or find the LifeCheck calendar
            $calendarId = $this->ensureCalendarExists($client);

            $settings = UserSetting::firstOrCreate(
                ['user_id' => $userId],
                []
            );

            $settings->update([
                'google_access_token' => json_encode($client->getAccessToken()),
                'google_refresh_token' => $client->getRefreshToken() ?? $settings->google_refresh_token,
                'google_token_expires_at' => now()->addSeconds($client->getAccessToken()['expires_in'] ?? 3600),
                'google_calendar_id' => $calendarId,
                'google_calendar_sync_enabled' => true,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar callback error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Disconnect Google Calendar for a user.
     */
    public function disconnect(int $userId): bool
    {
        try {
            $settings = UserSetting::where('user_id', $userId)->first();
            if ($settings && $settings->google_access_token) {
                $client = $this->getClient();
                $client->setAccessToken(json_decode($settings->google_access_token, true));
                try {
                    $client->revokeToken();
                } catch (\Exception $e) {
                    Log::warning('Google token revocation failed (may already be revoked): ' . $e->getMessage());
                }
            }

            UserSetting::where('user_id', $userId)->update([
                'google_access_token' => null,
                'google_refresh_token' => null,
                'google_token_expires_at' => null,
                'google_calendar_id' => null,
                'google_calendar_sync_enabled' => false,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar disconnect error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync a check-in to Google Calendar.
     */
    public function syncCheckIn(CheckIn $checkin): bool
    {
        $userId = $checkin->user_id;
        $settings = UserSetting::where('user_id', $userId)->first();

        if (!$settings || !$settings->isGoogleCalendarConnected()) {
            return false;
        }

        try {
            $client = $this->getClient($userId);
            $service = new GoogleCalendar($client);
            $calendarId = $settings->google_calendar_id;

            // Build mood description from check-in items
            $moodValue = null;
            $emojis = [];
            $notes = [];

            foreach ($checkin->items as $item) {
                $label = $item->templateItem->label ?? 'Item';
                $value = $item->value;

                if ($item->templateItem->input_type === 'slider') {
                    $moodValue = (int) $value;
                } elseif ($item->templateItem->input_type === 'emoji') {
                    $emojis[] = $value;
                } elseif ($item->templateItem->input_type === 'text') {
                    if (!empty(trim($value))) {
                        $notes[] = $label . ': ' . $value;
                    }
                }
            }

            // Build event title
            $weatherEmoji = $this->getMoodEmoji($moodValue);
            $emojiPart = !empty($emojis) ? ' ' . implode(' ', $emojis) : '';
            $title = $weatherEmoji . ' Humeur du ' . $checkin->date->format('d/m/Y') . $emojiPart;

            // Build description
            $descriptionParts = ["LifeCheck — Check-in du " . $checkin->date->format('d/m/Y')];
            if ($moodValue !== null) {
                $descriptionParts[] = "Humeur : {$moodValue}/10";
            }
            if (!empty($emojis)) {
                $descriptionParts[] = "Émotions : " . implode(' ', $emojis);
            }
            if ($checkin->notes) {
                $descriptionParts[] = "Notes : " . $checkin->notes;
            }
            if (!empty($notes)) {
                $descriptionParts[] = "Détails :";
                foreach ($notes as $note) {
                    $descriptionParts[] = "  • " . $note;
                }
            }

            // Tags
            if ($checkin->emotionTags->count() > 0) {
                $tagNames = $checkin->emotionTags->pluck('name')->implode(', ');
                $descriptionParts[] = "Tags : " . $tagNames;
            }

            $description = implode("\n", $descriptionParts);

            // Set event time (full day event)
            $dateStr = $checkin->date->format('Y-m-d');

            // Check if event already exists for this date
            $existingEventId = $this->findEventByDate($service, $calendarId, $dateStr);

            $event = new GoogleCalendarEvent();
            $event->setSummary($title);
            $event->setDescription($description);

            // Color based on mood
            $colorId = $this->getCalendarColorId($moodValue);
            $event->setColorId($colorId);

            $startDate = new EventDateTime();
            $startDate->setDate($dateStr);
            $startDate->setTimeZone('UTC');
            $event->setStart($startDate);

            $endDate = new EventDateTime();
            $endDate->setDate($dateStr);
            $endDate->setTimeZone('UTC');
            $event->setEnd($endDate);

            if ($existingEventId) {
                // Update existing event
                $service->events->update($calendarId, $existingEventId, $event);
            } else {
                // Create new event
                $service->events->insert($calendarId, $event);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar sync error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find an existing event by date.
     */
    private function findEventByDate(GoogleCalendar $service, string $calendarId, string $dateStr): ?string
    {
        try {
            $optParams = [
                'timeMin' => $dateStr . 'T00:00:00Z',
                'timeMax' => $dateStr . 'T23:59:59Z',
                'q' => 'LifeCheck',
            ];
            $events = $service->events->listEvents($calendarId, $optParams);

            foreach ($events->getItems() as $event) {
                if (str_contains($event->getSummary(), 'LifeCheck')
                    || str_contains($event->getDescription() ?? '', 'LifeCheck')) {
                    return $event->getId();
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error finding existing event: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Ensure the LifeCheck calendar exists. Create it if needed.
     */
    private function ensureCalendarExists(GoogleClient $client): string
    {
        $service = new GoogleCalendar($client);
        $calendarList = $service->calendarList->listCalendarList();

        foreach ($calendarList->getItems() as $calendar) {
            if ($calendar->getSummary() === self::CALENDAR_SUMMARY) {
                return $calendar->getId();
            }
        }

        // Create a new calendar
        $calendar = new \Google\Service\Calendar\Calendar();
        $calendar->setSummary(self::CALENDAR_SUMMARY);
        $calendar->setDescription(self::CALENDAR_DESCRIPTION);
        $calendar->setTimeZone(config('app.timezone', 'UTC'));

        $createdCalendar = $service->calendars->insert($calendar);
        return $createdCalendar->getId();
    }

    /**
     * Store the updated access token.
     */
    private function storeAccessToken(int $userId, array $accessToken): void
    {
        UserSetting::where('user_id', $userId)->update([
            'google_access_token' => json_encode($accessToken),
            'google_token_expires_at' => now()->addSeconds($accessToken['expires_in'] ?? 3600),
        ]);
    }

    /**
     * Get emoji for mood value.
     */
    private function getMoodEmoji(?int $moodValue): string
    {
        return match (true) {
            $moodValue >= 8 => '🌟',
            $moodValue >= 6 => '☀️',
            $moodValue >= 4 => '⛅',
            $moodValue >= 2 => '🌧️',
            $moodValue >= 1 => '🌩️',
            default => '📝',
        };
    }

    /**
     * Get Google Calendar color ID based on mood.
     * 1=Lavender, 2=Sage, 3=Grape, 4=Flamingo, 5=Banana, 6=Tangerine,
     * 7=Peacock, 8=Graphite, 9=Blueberry, 10=Basil, 11=Tomato
     */
    private function getCalendarColorId(?int $moodValue): string
    {
        return match (true) {
            $moodValue >= 8 => '10', // Basil (green — excellent)
            $moodValue >= 6 => '7',  // Peacock (teal — good)
            $moodValue >= 4 => '5',  // Banana (yellow — neutral)
            $moodValue >= 2 => '4',  // Flamingo (pink — low)
            default => '11',         // Tomato (red — very low)
        };
    }
}
