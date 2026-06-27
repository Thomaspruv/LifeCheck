<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display the settings form.
     */
    public function index(): View
    {
        $settings = UserSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'checkin_reminder_time' => null,
                'reminder_enabled' => false,
                'week_start' => 'monday',
                'theme' => 'light',
                'timezone' => 'UTC',
            ]
        );

        return view('settings.index', compact('settings'));
    }

    /**
     * Update the user settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'checkin_reminder_time' => ['nullable', 'date_format:H:i'],
            'reminder_enabled' => ['boolean'],
            'week_start' => ['required', 'in:monday,sunday'],
            'theme' => ['required', 'in:light,dark'],
            'timezone' => ['required', 'string', 'max:64', 'timezone'],
        ]);

        $settings = UserSetting::updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return redirect()->route('settings.index')
            ->with('success', __('Paramètres mis à jour avec succès.'));
    }
}
