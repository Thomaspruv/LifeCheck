<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\WeeklyInsight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsightsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Current week: Monday to Sunday
        $now = Carbon::today();
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $now->copy()->endOfWeek(Carbon::SUNDAY);
        $previousWeekStart = $weekStart->copy()->subWeek();
        $previousWeekEnd = $weekEnd->copy()->subWeek();

        // --- Current week stats ---
        $currentCheckins = CheckIn::where('user_id', $user->id)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->with('items.templateItem')
            ->orderBy('date')
            ->get();

        $currentStats = $this->computeStats($currentCheckins);

        // --- Previous week stats (for trend) ---
        $previousCheckins = CheckIn::where('user_id', $user->id)
            ->whereBetween('date', [$previousWeekStart, $previousWeekEnd])
            ->with('items.templateItem')
            ->orderBy('date')
            ->get();

        $previousStats = $this->computeStats($previousCheckins);

        // --- Determine trend ---
        $trend = 'stable';
        if ($currentStats['avg_mood'] !== null && $previousStats['avg_mood'] !== null) {
            $diff = $currentStats['avg_mood'] - $previousStats['avg_mood'];
            if ($diff > 0.3) {
                $trend = 'up';
            } elseif ($diff < -0.3) {
                $trend = 'down';
            }
        } elseif ($currentStats['avg_mood'] !== null) {
            $trend = 'up'; // First week with data
        }

        // --- Build summary text ---
        $summary = $this->buildSummary($currentStats, $trend);

        // --- Save or update the weekly insight ---
        WeeklyInsight::updateOrCreate(
            [
                'user_id' => $user->id,
                'week_start' => $weekStart->toDateString(),
            ],
            [
                'week_end' => $weekEnd->toDateString(),
                'avg_mood' => $currentStats['avg_mood'] ?? 0,
                'dominant_emotion' => $currentStats['dominant_emotion'] ?? '😐',
                'checkin_count' => $currentStats['checkin_count'],
                'total_days' => 7,
                'trend' => $trend,
                'summary' => $summary,
            ]
        );

        return view('insights.index', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'avgMood' => $currentStats['avg_mood'],
            'dominantEmotion' => $currentStats['dominant_emotion'],
            'checkinCount' => $currentStats['checkin_count'],
            'totalDays' => 7,
            'trend' => $trend,
            'summary' => $summary,
        ]);
    }

    public function history()
    {
        $user = Auth::user();

        $insights = WeeklyInsight::where('user_id', $user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        return view('insights.history', ['insights' => $insights]);
    }

    /**
     * Compute average mood, dominant emotion, and check-in count from a collection of check-ins.
     */
    private function computeStats($checkins)
    {
        $moods = [];
        $emotions = [];
        $checkedDates = [];

        foreach ($checkins as $checkin) {
            $checkedDates[$checkin->date->toDateString()] = true;

            foreach ($checkin->items as $item) {
                $type = $item->templateItem->input_type ?? 'text';

                if ($type === 'slider') {
                    $val = (int) $item->value;
                    if ($val >= 1 && $val <= 10) {
                        $moods[] = $val;
                    }
                } elseif ($type === 'emoji') {
                    $emotions[] = $item->value;
                }
            }
        }

        $avgMood = count($moods) > 0 ? round(array_sum($moods) / count($moods), 1) : null;

        // Dominant emotion
        $dominantEmotion = '😐';
        if (count($emotions) > 0) {
            $counts = array_count_values($emotions);
            arsort($counts);
            $dominantEmotion = array_key_first($counts);
        }

        return [
            'avg_mood' => $avgMood,
            'dominant_emotion' => $dominantEmotion,
            'checkin_count' => count($checkedDates),
        ];
    }

    /**
     * Generate a simple text summary based on stats.
     */
    private function buildSummary(array $stats, string $trend): string
    {
        $days = $stats['checkin_count'];
        $mood = $stats['avg_mood'];
        $emoji = $stats['dominant_emotion'];

        $parts = [];

        if ($days === 0) {
            return "Aucun check-in cette semaine. Commence à noter ton humeur pour obtenir des insights personnalisés.";
        }

        if ($mood !== null) {
            $parts[] = "Moyenne d'humeur : {$mood}/10.";
        }

        if ($emoji) {
            $name = match ($emoji) {
                '😢' => 'triste',
                '😟' => 'inquiet',
                '😐' => 'neutre',
                '🙂' => 'content',
                '😊' => 'joyeux',
                '😄' => 'très joyeux',
                '😁' => 'radieux',
                '🥳' => 'en fête',
                default => $emoji,
            };
            $parts[] = "Émotion dominante : {$emoji} ({$name}).";
        }

        $parts[] = "{$days} jour(s) checké(s) sur 7.";

        $trendText = match ($trend) {
            'up' => '⬆️ Tendance à la hausse par rapport à la semaine précédente.',
            'down' => '⬇️ Tendance à la baisse par rapport à la semaine précédente.',
            default => '➡️ Tendance stable par rapport à la semaine précédente.',
        };
        $parts[] = $trendText;

        return implode(' ', $parts);
    }
}
