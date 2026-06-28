<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'checkin_reminder_time',
        'reminder_enabled',
        'week_start',
        'theme',
        'timezone',
        'locale',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
        'google_calendar_id',
        'google_calendar_sync_enabled',
    ];

    protected function casts(): array
    {
        return [
            'checkin_reminder_time' => 'string',
            'reminder_enabled' => 'boolean',
            'google_token_expires_at' => 'datetime',
            'google_calendar_sync_enabled' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the Google token is expired.
     */
    public function isGoogleTokenExpired(): bool
    {
        return $this->google_token_expires_at === null
            || $this->google_token_expires_at->isPast();
    }

    /**
     * Check if Google Calendar is connected and sync is enabled.
     */
    public function isGoogleCalendarConnected(): bool
    {
        return $this->google_access_token !== null
            && $this->google_refresh_token !== null
            && $this->google_calendar_sync_enabled;
    }
}
