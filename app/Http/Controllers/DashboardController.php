<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\EmotionTag;
use App\Models\PersonalityTrait;
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

        // Badges
        $badges = $this->streakService->getBadges($user->id);

        // Last 7 days of check-ins for mood chart
        $last7Days = CheckIn::where('user_id', $user->id)
            ->whereDate('date', '>=', now()->subDays(6)->toDateString())
            ->with('items.templateItem')
            ->orderBy('date', 'asc')
            ->get();

        // Build mood chart data (slider values only)
        $moodChartData = [];
        $moodChartLabels = [];
        foreach ($last7Days as $checkin) {
            $moodChartLabels[] = $checkin->date->format('D d');
            $dayMoods = [];
            foreach ($checkin->items as $item) {
                if ($item->templateItem->input_type === 'slider') {
                    $val = (int) $item->value;
                    if ($val >= 1 && $val <= 10) {
                        $dayMoods[] = $val;
                    }
                }
            }
            $moodChartData[] = count($dayMoods) > 0
                ? round(array_sum($dayMoods) / count($dayMoods), 1)
                : null;
        }

        // Recent check-in count for "this week"
        $weekStart = now()->startOfWeek();
        $thisWeekCheckins = CheckIn::where('user_id', $user->id)
            ->whereDate('date', '>=', $weekStart->toDateString())
            ->whereDate('date', '<=', now()->toDateString())
            ->count();

        // Compute average mood from recent slider data
        $recentMoods = [];
        $recentEmotions = [];
        $recentCheckins = CheckIn::where('user_id', $user->id)
            ->whereDate('date', '>=', now()->subDays(7)->toDateString())
            ->with('items.templateItem')
            ->orderBy('date', 'desc')
            ->get();

        foreach ($recentCheckins as $checkin) {
            foreach ($checkin->items as $item) {
                if ($item->templateItem->input_type === 'slider') {
                    $val = (int) $item->value;
                    if ($val >= 1 && $val <= 10) {
                        $recentMoods[] = $val;
                    }
                } elseif ($item->templateItem->input_type === 'emoji') {
                    $recentEmotions[] = $item->value;
                }
            }
        }

        $avgMood = count($recentMoods) > 0
            ? round(array_sum($recentMoods) / count($recentMoods), 1)
            : null;

        $dominantEmotion = '😐';
        if (count($recentEmotions) > 0) {
            $counts = array_count_values($recentEmotions);
            arsort($counts);
            $dominantEmotion = array_key_first($counts);
        }

        // Today's check-in with emotion tags
        $todayCheckin = CheckIn::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->with('emotionTags')
            ->first();

        // All user tags
        $allTags = EmotionTag::where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        // Personality traits
        $personality = PersonalityTrait::where('user_id', $user->id)->first();

        return view('dashboard', [
            'hasTemplate' => $hasTemplate,
            'totalCheckins' => $totalCheckins,
            'todayDone' => $todayDone,
            'template' => $template,
            'streak' => $streakData['current'],
            'bestStreak' => $streakData['best'],
            'badges' => $badges,
            'moodChartLabels' => $moodChartLabels,
            'moodChartData' => $moodChartData,
            'thisWeekCheckins' => $thisWeekCheckins,
            'weekDayCount' => now()->startOfWeek()->diffInDays(now()) + 1,
            'avgMood' => $avgMood,
            'dominantEmotion' => $dominantEmotion,
            'todayCheckin' => $todayCheckin,
            'allTags' => $allTags,
            'personality' => $personality,
        ]);
    }
}
