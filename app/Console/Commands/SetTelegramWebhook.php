<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {url? : The full URL to the webhook endpoint}';
    protected $description = 'Set the Telegram bot webhook URL';

    public function handle(TelegramService $telegram): int
    {
        $url = $this->argument('url') ?? url('/api/telegram/webhook');

        $result = $telegram->call('setWebhook', [
            'url' => $url,
            'allowed_updates' => json_encode([
                'message',
                'inline_query',
                'callback_query',
            ]),
        ]);

        if ($result) {
            $this->info("✅ Webhook set successfully to: {$url}");
            return Command::SUCCESS;
        }

        $this->error("❌ Failed to set webhook. Check your TELEGRAM_BOT_TOKEN in .env");
        return Command::FAILURE;
    }
}
