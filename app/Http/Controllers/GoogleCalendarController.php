<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GoogleCalendarController extends Controller
{
    public function __construct(
        private readonly GoogleCalendarService $googleCalendarService
    ) {}

    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirect(): RedirectResponse
    {
        $authUrl = $this->googleCalendarService->getAuthUrl();
        return redirect()->away($authUrl);
    }

    /**
     * Handle the Google OAuth callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        $code = $request->query('code');
        $error = $request->query('error');

        if ($error || !$code) {
            $message = $error
                ? 'Autorisation Google refusée.'
                : 'Paramètre d\'autorisation manquant.';

            return redirect()->route('settings.index')
                ->with('error', $message);
        }

        $success = $this->googleCalendarService->handleCallback($code, Auth::id());

        if ($success) {
            return redirect()->route('settings.index')
                ->with('success', '✅ Google Calendar connecté avec succès ! Tes humeurs apparaîtront dans ton agenda.');
        }

        return redirect()->route('settings.index')
            ->with('error', '❌ Échec de la connexion Google Calendar. Vérifie les logs.');
    }

    /**
     * Disconnect Google Calendar.
     */
    public function disconnect(): RedirectResponse
    {
        $success = $this->googleCalendarService->disconnect(Auth::id());

        if ($success) {
            return redirect()->route('settings.index')
                ->with('success', '🔌 Google Calendar déconnecté.');
        }

        return redirect()->route('settings.index')
            ->with('error', '❌ Erreur lors de la déconnexion.');
    }
}
