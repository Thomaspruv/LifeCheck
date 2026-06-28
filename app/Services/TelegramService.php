<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private string $apiUrl;

    public function __construct()
    {
        $token = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$token}";
    }

    /**
     * Send a message to a Telegram chat.
     */
    public function sendMessage(int|string $chatId, string $text, array $extra = []): ?array
    {
        return $this->call('sendMessage', array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $extra));
    }

    /**
     * Answer an inline query with check-in results.
     */
    public function answerInlineQuery(string $inlineQueryId, array $results): ?array
    {
        return $this->call('answerInlineQuery', [
            'inline_query_id' => $inlineQueryId,
            'results' => $results,
            'cache_time' => 0,
            'is_personal' => true,
        ]);
    }

    /**
     * Answer a callback query.
     */
    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null, bool $showAlert = false): ?array
    {
        return $this->call('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ]);
    }

    /**
     * Edit a message's text (for inline keyboard updates).
     */
    public function editMessageText(string|int $chatId, int $messageId, string $text, array $extra = []): ?array
    {
        return $this->call('editMessageText', array_merge([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $extra));
    }

    /**
     * Make a raw HTTP call to the Telegram Bot API.
     */
    public function call(string $method, array $params = []): ?array
    {
        try {
            $response = Http::timeout(10)->post("{$this->apiUrl}/{$method}", $params);

            if ($response->failed()) {
                Log::warning("Telegram API error [{$method}]: {$response->body()}");
                return null;
            }

            $data = $response->json();

            if (!($data['ok'] ?? false)) {
                Log::warning("Telegram API not-ok [{$method}]: " . json_encode($data));
                return null;
            }

            return $data['result'] ?? null;
        } catch (\Throwable $e) {
            Log::error("Telegram API exception [{$method}]: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Generate a link token for connecting Telegram to a LifeCheck account.
     */
    public static function generateToken(): string
    {
        return str()->random(32);
    }

    /**
     * Build the bot username link for inline usage.
     */
    public static function inlineLink(string $botUsername): string
    {
        return "https://t.me/{$botUsername}?start=inline";
    }
}
