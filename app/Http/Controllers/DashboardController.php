<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Template;
use App\Services\StreakService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private readonly StreakService $streakService
    ) {}

    public function __invoke()
    {
        $user = Auth::user();

        $template = Template::where('user_id', $user->id)
            ->with('items')
            ->first();

        $hasTemplate = $template !== null;

        $totalCheckins = CheckIn::where('user_id', $user->id)->count();
        $todayDone = CheckIn::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->exists();

        // Streak calculation via service
        $streakData = $this->streakService->calculateStreaks($user->id);

        return view('dashboard', [
            'hasTemplate' => $hasTemplate,
            'totalCheckins' => $totalCheckins,
            'todayDone' => $todayDone,
            'template' => $template,
            'streak' => $streakData['current'],
            'bestStreak' => $streakData['best'],
        ]);
    }
}
