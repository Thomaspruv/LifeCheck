<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class CheckTelegramWebhook extends Command
{
    protected $signature = 'telegram:check-webhook';
    protected $description = 'Check the current Telegram bot webhook status';

    public function handle(TelegramService $telegram): int
    {
        $info = $telegram->call('getWebhookInfo');

        if ($info) {
            $this->line("URL:       " . ($info['url'] ?? 'not set'));
            $this->line("Pending:   " . ($info['pending_update_count'] ?? 0));
            $this->line("Errors:    " . ($info['last_error_date'] ?? 'none'));

            if (!empty($info['url'])) {
                $this->info("✅ Webhook is active");
            } else {
                $this->warn("⚠️  No webhook set. Run `php artisan telegram:set-webhook`");
            }

            return Command::SUCCESS;
        }

        $this->error("❌ Could not get webhook info. Check your TELEGRAM_BOT_TOKEN.");
        return Command::FAILURE;
    }
}
