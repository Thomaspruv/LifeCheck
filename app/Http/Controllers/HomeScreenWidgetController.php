<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\EmotionTag;
use App\Models\Template;
use App\Services\StreakService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HomeScreenWidgetController extends Controller
{
    public function __construct(
        private readonly StreakService $streakService
    ) {}

    /**
     * Display a lightweight widget-friendly view for home screen display.
     * Shows today's mood at a glance.
     */
    public function widget()
    {
        $user = Auth::user();

        $todayCheckin = CheckIn::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->with(['items.templateItem', 'emotionTags'])
            ->first();

        $hasTemplate = Template::where('user_id', $user->id)->exists();

        $streakData = $this->streakService->calculateStreaks($user->id);

        $currentMood = null;
        $moodValue = null;
        $recentEmotions = [];

        if ($todayCheckin) {
            foreach ($todayCheckin->items as $item) {
                if ($item->templateItem->input_type === 'slider') {
                    $moodValue = (int) $item->value;
                    $currentMood = $this->moodLabel($moodValue);
                }
                if ($item->templateItem->input_type === 'emoji') {
                    $recentEmotions[] = $item->value;
                }
            }
        }

        // Fallback: use most recent check-in if none today
        $lastCheckin = null;
        if (!$todayCheckin) {
            $lastCheckin = CheckIn::where('user_id', $user->id)
                ->whereDate('date', '<', now()->toDateString())
                ->with('items.templateItem')
                ->orderBy('date', 'desc')
                ->first();

            if ($lastCheckin) {
                foreach ($lastCheckin->items as $item) {
                    if ($item->templateItem->input_type === 'slider') {
                        $moodValue = (int) $item->value;
                        $currentMood = $this->moodLabel($moodValue);
                    }
                    if ($item->templateItem->input_type === 'emoji') {
                        $recentEmotions[] = $item->value;
                    }
                }
            }
        }

        $dominantEmotion = null;
        if (count($recentEmotions) > 0) {
            $counts = array_count_values($recentEmotions);
            arsort($counts);
            $dominantEmotion = array_key_first($counts);
        }

        return view('homescreen-widget', [
            'todayDone' => $todayCheckin !== null,
            'todayCheckin' => $todayCheckin,
            'lastCheckin' => $lastCheckin,
            'streak' => $streakData['current'],
            'moodValue' => $moodValue,
            'moodLabel' => $currentMood,
            'dominantEmotion' => $dominantEmotion,
            'hasTemplate' => $hasTemplate,
        ]);
    }

    /**
     * JSON API endpoint for the home screen widget data.
     * Returns mood data that a native widget could consume.
     */
    public function apiData(): JsonResponse
    {
        $user = Auth::user();

        $todayCheckin = CheckIn::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->with(['items.templateItem', 'emotionTags'])
            ->first();

        $streakData = $this->streakService->calculateStreaks($user->id);

        $moodValue = null;
        $items = [];

        $checkin = $todayCheckin;
        if (!$checkin) {
            $checkin = CheckIn::where('user_id', $user->id)
                ->with('items.templateItem')
                ->orderBy('date', 'desc')
                ->first();
        }

        if ($checkin) {
            foreach ($checkin->items as $item) {
                $items[] = [
                    'label' => $item->templateItem->label,
                    'type'  => $item->templateItem->input_type,
                    'value' => $item->value,
                ];
                if ($item->templateItem->input_type === 'slider') {
                    $moodValue = (int) $item->value;
                }
            }
        }

        $emotions = [];
        if ($checkin) {
            foreach ($checkin->emotionTags as $tag) {
                $emotions[] = [
                    'name'  => $tag->name,
                    'color' => $tag->color,
                    'icon'  => $tag->icon,
                ];
            }
        }

        return response()->json([
            'checked_in_today' => $todayCheckin !== null,
            'date'             => $checkin ? $checkin->date->toDateString() : null,
            'mood_value'       => $moodValue,
            'mood_label'       => $moodValue ? $this->moodLabel($moodValue) : null,
            'streak'           => $streakData['current'],
            'best_streak'      => $streakData['best'],
            'items'            => $items,
            'emotions'         => $emotions,
        ]);
    }

    /**
     * Convert a 1-10 mood slider value to a human-readable label.
     */
    private function moodLabel(int $value): string
    {
        return match (true) {
            $value >= 9 => '🌟 Excellent',
            $value >= 7 => '😊 Bien',
            $value >= 5 => '😐 Neutre',
            $value >= 3 => '😕 Pas top',
            default     => '😞 Difficile',
        };
    }
}
