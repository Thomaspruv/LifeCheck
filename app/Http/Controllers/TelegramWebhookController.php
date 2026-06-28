<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\EmotionTag;
use App\Models\Template;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly TelegramService $telegram
    ) {}

    /**
     * Handle incoming Telegram updates (webhook).
     */
    public function handle(Request $request)
    {
        $update = $request->all();

        // Inline query: @botname in any chat
        if (isset($update['inline_query'])) {
            return $this->handleInlineQuery($update['inline_query']);
        }

        // Callback query: user tapped an inline button
        if (isset($update['callback_query'])) {
            return $this->handleCallbackQuery($update['callback_query']);
        }

        // Message: /start, /link, etc.
        if (isset($update['message'])) {
            return $this->handleMessage($update['message']);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Handle @botname inline queries — show quick check-in options.
     */
    private function handleInlineQuery(array $inlineQuery): \Illuminate\Http\JsonResponse
    {
        $userId = $inlineQuery['from']['id'] ?? null;
        $queryId = $inlineQuery['id'];
        $query = $inlineQuery['query'] ?? '';

        // Find the user by Telegram chat ID
        $user = \App\Models\User::where('telegram_chat_id', $userId)->first();

        if (!$user) {
            // User not linked — show a link instruction
            $this->telegram->answerInlineQuery($queryId, [
                [
                    'type' => 'article',
                    'id' => 'link_first',
                    'title' => '🔗 Connecte ton compte LifeCheck',
                    'description' => 'Utilise /start sur le bot pour associer ton compte',
                    'input_message_content' => [
                        'message_text' => "🤖 Pour utiliser le check-in inline LifeCheck :\n1. Ouvre @lifecheck_bot\n2. Envoie /start\n3. Suis les instructions pour lier ton compte",
                    ],
                ],
            ]);
            return response()->json(['ok' => true]);
        }

        // User linked — show check-in options
        $template = Template::where('user_id', $user->id)
            ->where('is_default', true)
            ->with('items')
            ->first();

        $today = now()->toDateString();
        $alreadyCheckedIn = CheckIn::where('user_id', $user->id)
            ->where('date', $today)
            ->exists();

        if ($alreadyCheckedIn) {
            $this->telegram->answerInlineQuery($queryId, [
                [
                    'type' => 'article',
                    'id' => 'already_done',
                    'title' => '✅ Check-in déjà fait aujourd\'hui !',
                    'description' => 'Reviens demain pour ton prochain check-in',
                    'input_message_content' => [
                        'message_text' => "✅ Check-in déjà fait aujourd'hui !\n📅 {$today}\n\nÀ demain pour le prochain ! 🙌",
                    ],
                ],
            ]);
            return response()->json(['ok' => true]);
        }

        // Build inline results based on template items
        $results = [];
        $resultId = 0;

        // Quick mood entry (first slider item if exists)
        $sliderItem = $template->items->firstWhere('input_type', 'slider');
        if ($sliderItem) {
            $results[] = [
                'type' => 'article',
                'id' => 'mood_' . ($resultId++),
                'title' => '😊 Humeur du jour',
                'description' => "Note ton humeur (1-10) — {$sliderItem->label}",
                'input_message_content' => [
                    'message_text' => "📝 Check-in rapide en cours...\nChoisis ta humeur ci-dessous 👇",
                ],
                'reply_markup' => [
                    'inline_keyboard' => $this->buildMoodKeyboard($sliderItem->id, $template->id),
                ],
            ];
        }

        // Quick emoji entry (first emoji item if exists)
        $emojiItem = $template->items->firstWhere('input_type', 'emoji');
        if ($emojiItem) {
            $results[] = [
                'type' => 'article',
                'id' => 'emoji_' . ($resultId++),
                'title' => '😄 Emoji rapide',
                'description' => "Choisis l'emoji qui correspond à ton humeur",
                'input_message_content' => [
                    'message_text' => "📝 Check-in rapide — choisis ton emoji :",
                ],
                'reply_markup' => [
                    'inline_keyboard' => $this->buildEmojiKeyboard($emojiItem->id, $template->id),
                ],
            ];
        }

        // Full check-in (opens the web app)
        if ($template->items->count() > 2) {
            $results[] = [
                'type' => 'article',
                'id' => 'full_' . ($resultId++),
                'title' => '📋 Check-in complet',
                'description' => 'Fais un check-in complet sur LifeCheck',
                'input_message_content' => [
                    'message_text' => "📋 Ouvre LifeCheck pour un check-in complet :\n🔗 " . url('/checkin'),
                ],
                'url' => url('/checkin'),
                'hide_url' => false,
            ];
        }

        // If no template items, show generic
        if (empty($results)) {
            $results[] = [
                'type' => 'article',
                'id' => 'no_template',
                'title' => '📝 Configure ton template',
                'description' => 'Crée d\'abord un template de check-in sur LifeCheck',
                'input_message_content' => [
                    'message_text' => "📝 Tu n'as pas encore de template de check-in.\n👉 Ouvre LifeCheck et crée-en un : " . url('/templates'),
                ],
            ];
        }

        // Filter by query if user typed something
        if (!empty($query)) {
            $results = array_filter($results, function ($r) use ($query) {
                return str_contains(mb_strtolower($r['title'] . ' ' . ($r['description'] ?? '')), mb_strtolower($query));
            });
            $results = array_values($results);
        }

        $this->telegram->answerInlineQuery($queryId, $results);
        return response()->json(['ok' => true]);
    }

    /**
     * Handle inline button presses (callback queries).
     */
    private function handleCallbackQuery(array $callbackQuery): \Illuminate\Http\JsonResponse
    {
        $data = $callbackQuery['data'] ?? '';
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;
        $messageId = $callbackQuery['message']['message_id'] ?? null;
        $callbackId = $callbackQuery['id'];
        $userId = $callbackQuery['from']['id'] ?? null;

        $user = \App\Models\User::where('telegram_chat_id', $userId)->first();
        if (!$user || !$chatId || !$messageId) {
            $this->telegram->answerCallbackQuery($callbackId, '❌ Compte non lié', true);
            return response()->json(['ok' => true]);
        }

        // Parse callback data: action|templateId|itemId|value
        $parts = explode('|', $data);
        $action = $parts[0] ?? '';

        switch ($action) {
            case 'mood':
                // mood|templateId|itemId|value
                $templateId = $parts[1] ?? null;
                $itemId = $parts[2] ?? null;
                $value = $parts[3] ?? null;

                if ($templateId && $itemId && $value) {
                    $this->processQuickCheckin($user, $chatId, $messageId, $templateId, $itemId, $value);
                    $this->telegram->answerCallbackQuery($callbackId, "✅ Humeur enregistrée : {$value}/10");
                }
                break;

            case 'emoji':
                // emoji|templateId|itemId|emoji
                $templateId = $parts[1] ?? null;
                $itemId = $parts[2] ?? null;
                $emoji = $parts[3] ?? null;

                if ($templateId && $itemId && $emoji) {
                    $this->processQuickCheckin($user, $chatId, $messageId, $templateId, $itemId, $emoji);
                    $this->telegram->answerCallbackQuery($callbackId, "✅ Émotion enregistrée : {$emoji}");
                }
                break;

            default:
                $this->telegram->answerCallbackQuery($callbackId, '❌ Action inconnue');
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Handle direct messages (commands).
     */
    private function handleMessage(array $message): \Illuminate\Http\JsonResponse
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $fromId = $message['from']['id'] ?? null;

        // /start command — link Telegram account
        if (str_starts_with($text, '/start')) {
            $params = explode(' ', $text);
            $token = $params[1] ?? null;

            if ($token && $token !== 'inline') {
                // Link via token
                $user = \App\Models\User::where('telegram_token', $token)->first();
                if ($user) {
                    $user->update([
                        'telegram_chat_id' => $fromId,
                        'telegram_token' => null, // one-time use
                    ]);
                    $this->telegram->sendMessage($chatId,
                        "✅ <b>Compte LifeCheck lié avec succès !</b>\n\n"
                        . "Tu peux maintenant faire ton check-in sans quitter la discussion :\n"
                        . "👉 Tape <b>@lifecheck_bot</b> dans n'importe quel chat\n\n"
                        . "📊 Consulte tes stats et défis sur " . url('/dashboard')
                    );
                } else {
                    $this->telegram->sendMessage($chatId,
                        "❌ Jeton invalide ou expiré.\n"
                        . "Génère un nouveau lien depuis tes paramètres LifeCheck :\n"
                        . "🔗 " . url('/settings')
                    );
                }
            } else {
                // Generic /start without token
                $user = \App\Models\User::where('telegram_chat_id', $fromId)->first();
                if ($user) {
                    $this->sendLinkedWelcome($chatId, $user);
                } else {
                    $this->telegram->sendMessage($chatId,
                        "👋 <b>Bienvenue sur LifeCheck !</b>\n\n"
                        . "Je te permets de faire ton check-in quotidien sans quitter Telegram.\n\n"
                        . "Pour commencer :\n"
                        . "1️⃣ Connecte-toi sur LifeCheck : " . url('/login') . "\n"
                        . "2️⃣ Va dans tes <b>Paramètres → Telegram</b>\n"
                        . "3️⃣ Génére un jeton de liaison\n"
                        . "4️⃣ Envoie-le moi ici avec /start <jeton>\n\n"
                        . "💡 Astuce : Une fois lié, tape <b>@lifecheck_bot</b> dans n'importe quel chat pour un check-in rapide !"
                    );
                }
            }
        }

        // /checkin command — quick status
        elseif ($text === '/checkin') {
            $user = \App\Models\User::where('telegram_chat_id', $fromId)->first();
            if (!$user) {
                $this->telegram->sendMessage($chatId,
                    "❌ Tu n'as pas encore lié ton compte.\n"
                    . "Génère un jeton depuis " . url('/settings') . " et utilise /start <jeton>"
                );
                return response()->json(['ok' => true]);
            }

            $today = now()->toDateString();
            $existing = CheckIn::where('user_id', $user->id)->where('date', $today)->first();

            if ($existing) {
                $this->telegram->sendMessage($chatId,
                    "✅ Check-in déjà fait aujourd'hui !\n📅 {$today}\n\n🙌 Bonne journée !"
                );
            } else {
                $this->telegram->sendMessage($chatId,
                    "📝 Tu n'as pas encore fait ton check-in aujourd'hui.\n"
                    . "👉 Tape <b>@lifecheck_bot</b> dans n'importe quel chat pour le faire rapidement !\n"
                    . "Ou clique ici : " . url('/checkin')
                );
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Process a quick check-in from inline widget.
     */
    private function processQuickCheckin(\App\Models\User $user, string|int $chatId, int $messageId, int $templateId, int $itemId, string $value): void
    {
        $today = now()->toDateString();

        // Check duplicate
        $existing = CheckIn::where('user_id', $user->id)->where('date', $today)->first();

        if ($existing) {
            // Update existing check-in — add this item value
            $existing->items()->updateOrCreate(
                ['template_item_id' => $itemId],
                ['value' => $value]
            );

            $this->telegram->editMessageText($chatId, $messageId,
                "✅ <b>Check-in mis à jour !</b>\n📅 {$today}\n\nValeur ajoutée au check-in existant."
            );
            return;
        }

        // Get template
        $template = Template::find($templateId);
        if (!$template) {
            $this->telegram->editMessageText($chatId, $messageId, "❌ Template introuvable.");
            return;
        }

        // Create check-in with this single item
        $checkin = CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'date' => $today,
            'notes' => null,
        ]);

        $checkin->items()->create([
            'template_item_id' => $itemId,
            'value' => $value,
        ]);

        // Update the inline message
        $displayValue = $value;
        $this->telegram->editMessageText($chatId, $messageId,
            "✅ <b>Check-in enregistré !</b> 🎉\n📅 {$today}\n\n"
            . "Continue ton suivi sur LifeCheck :\n"
            . "📊 " . url('/dashboard')
        );
    }

    /**
     * Send a welcome message to a user who already linked their account.
     */
    private function sendLinkedWelcome(string|int $chatId, \App\Models\User $user): void
    {
        $this->telegram->sendMessage($chatId,
            "👋 <b>Ravis de te revoir, {$user->name} !</b>\n\n"
            . "✅ Compte déjà lié.\n\n"
            . "👉 Tape <b>@lifecheck_bot</b> dans n'importe quel chat pour un check-in rapide\n"
            . "📊 Consulte tes stats : " . url('/dashboard')
        );
    }

    /**
     * Build an inline keyboard for mood slider (1-10).
     */
    private function buildMoodKeyboard(int $itemId, int $templateId): array
    {
        $emojis = [1 => '😢', 2 => '😟', 3 => '😐', 4 => '🙂', 5 => '😊', 6 => '😄', 7 => '😁', 8 => '🥳', 9 => '🤩', 10 => '💖'];
        $keyboard = [];
        $row = [];

        foreach (range(1, 10) as $i) {
            $row[] = [
                'text' => $emojis[$i],
                'callback_data' => "mood|{$templateId}|{$itemId}|{$i}",
            ];
            if ($i % 5 === 0) {
                $keyboard[] = $row;
                $row = [];
            }
        }

        return $keyboard;
    }

    /**
     * Build an inline keyboard for emoji selection.
     */
    private function buildEmojiKeyboard(int $itemId, int $templateId): array
    {
        $emojis = ['😢', '😟', '😐', '🙂', '😊', '😄', '😁', '🥳'];
        $keyboard = [];
        $row = [];

        foreach ($emojis as $i => $emoji) {
            $row[] = [
                'text' => $emoji,
                'callback_data' => "emoji|{$templateId}|{$itemId}|{$emoji}",
            ];
            if ($i % 4 === 3) {
                $keyboard[] = $row;
                $row = [];
            }
        }

        if (!empty($row)) {
            $keyboard[] = $row;
        }

        return $keyboard;
    }
}
