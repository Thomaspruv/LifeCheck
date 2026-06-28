<?php

use Illuminate\Support\Facades\Route;

Route::post('/api/telegram/webhook', [\App\Http\Controllers\TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');
