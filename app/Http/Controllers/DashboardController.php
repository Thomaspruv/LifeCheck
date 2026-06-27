<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
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

        // Streak calculation
        $streak = 0;
        $bestStreak = 0;

        $dates = CheckIn::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn($d) => $d instanceof \Carbon\Carbon ? $d->toDateString() : $d);

        if ($dates->isNotEmpty()) {
            $current = 0;
            $best = 0;
            $cursor = now()->toDateString();

            foreach ($dates as $date) {
                if ($date === $cursor) {
                    $current++;
                    $cursor = now()->subDays($current)->toDateString();
                } else {
                    $best = max($best, $current);
                    $current = 1;
                    $cursor = now()->subDay()->toDateString();
                    if ($date !== $cursor) {
                        $current = 0;
                    }
                }
            }
            $streak = $current;
            $bestStreak = max($best, $current);
        }

        return view('dashboard', [
            'hasTemplate' => $hasTemplate,
            'totalCheckins' => $totalCheckins,
            'todayDone' => $todayDone,
            'template' => $template,
            'streak' => $streak,
            'bestStreak' => $bestStreak,
        ]);
    }
}
