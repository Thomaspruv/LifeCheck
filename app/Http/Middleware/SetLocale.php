<?php

namespace App\Http\Middleware;

use App\Models\UserSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $locale = UserSetting::where('user_id', Auth::id())->value('locale');

            if ($locale && in_array($locale, ['fr', 'en', 'es'])) {
                App::setLocale($locale);
            }
        }

        return $next($request);
    }
}
