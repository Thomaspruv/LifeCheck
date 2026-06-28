<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Switch the application language.
     */
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:fr,en,es'],
        ]);

        if (Auth::check()) {
            UserSetting::updateOrCreate(
                ['user_id' => Auth::id()],
                ['locale' => $validated['locale']]
            );

            App::setLocale($validated['locale']);
        }

        return redirect()->back();
    }
}
