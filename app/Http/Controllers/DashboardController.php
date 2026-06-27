<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        // Check if user has completed onboarding
        $template = Template::where('user_id', $user->id)
            ->with('items')
            ->first();

        $hasTemplate = $template !== null;

        // Count total check-ins (placeholder — will work once CheckIn exists)
        $totalCheckins = 0;
        $todayDone = false;

        return view('dashboard', [
            'hasTemplate' => $hasTemplate,
            'totalCheckins' => $totalCheckins,
            'todayDone' => $todayDone,
            'template' => $template,
        ]);
    }
}
