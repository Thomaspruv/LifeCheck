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

    public function j7Comparison(Request $request)
    {
        $user = Auth::user();

        // Support sliding window via ?from=YYYY-MM-DD
        $fromDate = $request->query('from');
        if ($fromDate) {
            $thisPeriodEnd = Carbon::parse($fromDate);
            $thisPeriodStart = $thisPeriodEnd->copy()->subDays(6);
        } else {
            // Default: last 7 days ending today
            $today = Carbon::today();
            $thisPeriodStart = $today->copy()->subDays(6);
            $thisPeriodEnd = $today;
        }

        // Previous 7 days (same day-of-week, one week before)
        $prevPeriodStart = $thisPeriodStart->copy()->subDays(7);
        $prevPeriodEnd = $thisPeriodEnd->copy()->subDays(7);

        // Fetch check-ins for both periods
        $thisCheckins = CheckIn::where('user_id', $user->id)
            ->whereBetween('date', [$thisPeriodStart, $thisPeriodEnd])
            ->with('items.templateItem')
            ->orderBy('date')
            ->get();

        $prevCheckins = CheckIn::where('user_id', $user->id)
            ->whereBetween('date', [$prevPeriodStart, $prevPeriodEnd])
            ->with('items.templateItem')
            ->orderBy('date')
            ->get();

        // Group by date for quick lookup
        $thisByDate = $thisCheckins->keyBy(fn($c) => $c->date->toDateString());
        $prevByDate = $prevCheckins->keyBy(fn($c) => $c->date->toDateString());

        // Build sliding comparison days
        $comparisonDays = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->subDays(6 - $i);
            $prevDate = $date->copy()->subDays(7);

            $dayLabel = $date->format('D d/m');
            $dayName = ucfirst($date->locale('fr')->dayName);
            $dateStr = $date->toDateString();
            $prevDateStr = $prevDate->toDateString();

            $thisCheckin = $thisByDate->get($dateStr);
            $prevCheckin = $prevByDate->get($prevDateStr);

            $dayData = [
                'date' => $dateStr,
                'day_label' => $dayLabel,
                'day_name' => $dayName,
                'prev_date' => $prevDateStr,
                'this_checkin' => $thisCheckin ? true : false,
                'prev_checkin' => $prevCheckin ? true : false,
                'dimensions' => [],
            ];

            // Collect slider dimensions from all template items
            $sliderLabels = [];

            if ($thisCheckin) {
                foreach ($thisCheckin->items as $item) {
                    if ($item->templateItem->input_type === 'slider') {
                        $label = $item->templateItem->label;
                        $sliderLabels[$label] = true;
                        if (!isset($dayData['dimensions'][$label])) {
                            $dayData['dimensions'][$label] = [
                                'this_val' => (int) $item->value,
                                'prev_val' => null,
                                'diff' => null,
                                'diff_label' => '—',
                            ];
                        } else {
                            $dayData['dimensions'][$label]['this_val'] = (int) $item->value;
                        }
                    }
                }
            }

            // Fill previous week values
            if ($prevCheckin) {
                foreach ($prevCheckin->items as $item) {
                    if ($item->templateItem->input_type === 'slider') {
                        $label = $item->templateItem->label;
                        $sliderLabels[$label] = true;
                        $prevVal = (int) $item->value;
                        if (isset($dayData['dimensions'][$label])) {
                            $dayData['dimensions'][$label]['prev_val'] = $prevVal;
                            $thisVal = $dayData['dimensions'][$label]['this_val'];
                            $diff = $thisVal - $prevVal;
                            $dayData['dimensions'][$label]['diff'] = $diff;
                            $dayData['dimensions'][$label]['diff_label'] = ($diff > 0 ? '+' : '') . $diff;
                        } else {
                            $dayData['dimensions'][$label] = [
                                'this_val' => null,
                                'prev_val' => $prevVal,
                                'diff' => null,
                                'diff_label' => '—',
                            ];
                        }
                    }
                }
            }

            // Ensure all slider dimensions exist even if no data
            if (empty($sliderLabels)) {
                // Fallback: get all slider template items from user's template
                $template = \App\Models\Template::where('user_id', $user->id)
                    ->with(['items' => fn($q) => $q->where('input_type', 'slider')])
                    ->first();
                if ($template) {
                    foreach ($template->items as $item) {
                        $label = $item->label;
                        if (!isset($dayData['dimensions'][$label])) {
                            $dayData['dimensions'][$label] = [
                                'this_val' => null,
                                'prev_val' => null,
                                'diff' => null,
                                'diff_label' => '—',
                            ];
                        }
                    }
                }
            }

            $comparisonDays[] = $dayData;
        }

        // Compute aggregate stats for the comparison table
        $overallStats = [];
        $dimNames = [];
        if (!empty($comparisonDays) && !empty($comparisonDays[0]['dimensions'])) {
            $dimNames = array_keys($comparisonDays[0]['dimensions']);
        }

        foreach ($dimNames as $label) {
            $thisVals = [];
            $prevVals = [];
            foreach ($comparisonDays as $day) {
                if (isset($day['dimensions'][$label])) {
                    if ($day['dimensions'][$label]['this_val'] !== null) {
                        $thisVals[] = $day['dimensions'][$label]['this_val'];
                    }
                    if ($day['dimensions'][$label]['prev_val'] !== null) {
                        $prevVals[] = $day['dimensions'][$label]['prev_val'];
                    }
                }
            }

            $avgThis = count($thisVals) > 0 ? round(array_sum($thisVals) / count($thisVals), 1) : null;
            $avgPrev = count($prevVals) > 0 ? round(array_sum($prevVals) / count($prevVals), 1) : null;
            $overallDiff = ($avgThis !== null && $avgPrev !== null) ? round($avgThis - $avgPrev, 1) : null;
            $overallTrend = $overallDiff !== null ? ($overallDiff > 0 ? 'up' : ($overallDiff < 0 ? 'down' : 'stable')) : null;

            $overallStats[$label] = [
                'avg_this' => $avgThis,
                'avg_prev' => $avgPrev,
                'diff' => $overallDiff,
                'trend' => $overallTrend,
                'days_this' => count($thisVals),
                'days_prev' => count($prevVals),
            ];
        }

        // Navigation: previous sliding window
        $prevStart = $thisPeriodStart->copy()->subDay();
        $nextStart = $thisPeriodStart->copy()->addDay();

        return view('insights.j7-comparison', [
            'comparisonDays' => $comparisonDays,
            'overallStats' => $overallStats,
            'thisPeriodStart' => $thisPeriodStart,
            'thisPeriodEnd' => $thisPeriodEnd,
            'prevPeriodStart' => $prevPeriodStart,
            'prevPeriodEnd' => $prevPeriodEnd,
            'prevStart' => $prevStart,
            'nextStart' => $nextStart,
            'today' => $today,
        ]);
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
