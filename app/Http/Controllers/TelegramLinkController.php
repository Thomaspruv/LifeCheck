<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramLinkController extends Controller
{
    /**
     * Generate a one-time token for linking Telegram to LifeCheck.
     */
    public function generateToken(): RedirectResponse
    {
        $user = Auth::user();

        $token = TelegramService::generateToken();
        $user->update(['telegram_token' => $token]);

        return redirect()->route('settings.index')
            ->with('success', '🔗 Jeton généré ! Envoie-le au bot Telegram :')
            ->with('telegram_token', $token);
    }

    /**
     * Revoke the Telegram link.
     */
    public function revoke(): RedirectResponse
    {
        $user = Auth::user();

        $user->update([
            'telegram_chat_id' => null,
            'telegram_token' => null,
        ]);

        return redirect()->route('settings.index')
            ->with('success', '✅ Liaison Telegram révoquée.');
    }
}
