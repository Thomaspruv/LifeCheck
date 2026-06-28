<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\EmotionTag;
use Illuminate\Support\Facades\Auth;

class TimelineController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $checkins = CheckIn::where('user_id', $user->id)
            ->with('items.templateItem', 'emotionTags')
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy(fn($c) => $c->date->format('Y-m'));

        // Compute mood value for each check-in (average of slider values)
        $timelineData = [];
        foreach ($checkins as $monthKey => $monthCheckins) {
            $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->translatedFormat('F Y');
            $entries = [];

            foreach ($monthCheckins as $checkin) {
                $moodValues = [];
                $moodEmoji = null;

                foreach ($checkin->items as $item) {
                    if ($item->templateItem->input_type === 'slider') {
                        $val = (int) $item->value;
                        if ($val >= 1 && $val <= 10) {
                            $moodValues[] = $val;
                        }
                    } elseif ($item->templateItem->input_type === 'emoji') {
                        $moodEmoji = $item->value;
                    }
                }

                $avgMood = count($moodValues) > 0
                    ? round(array_sum($moodValues) / count($moodValues), 1)
                    : null;

                $entries[] = [
                    'id' => $checkin->id,
                    'date' => $checkin->date,
                    'notes' => $checkin->notes,
                    'avg_mood' => $avgMood,
                    'mood_emoji' => $moodEmoji,
                    'item_count' => $checkin->items->count(),
                    'emotion_tags' => $checkin->emotionTags,
                ];
            }

            $timelineData[] = [
                'month_key' => $monthKey,
                'month_label' => $monthLabel,
                'entries' => $entries,
            ];
        }

        // Calculate streak
        $streak = 0;
        $allDates = CheckIn::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn($d) => $d->toDateString())
            ->toArray();

        if (count($allDates) > 0) {
            $today = now()->toDateString();
            $checkDate = \Carbon\Carbon::parse($allDates[0]);

            // If most recent check-in is today or yesterday, start counting
            if ($checkDate->diffInDays(now()) <= 1) {
                $streak = 1;
                $expectedDate = $checkDate->copy()->subDay();

                for ($i = 1; $i < count($allDates); $i++) {
                    $date = \Carbon\Carbon::parse($allDates[$i]);
                    if ($date->toDateString() === $expectedDate->toDateString()) {
                        $streak++;
                        $expectedDate->subDay();
                    } else {
                        break;
                    }
                }
            }
        }

        // All user emotion tags for filtering
        $allTags = EmotionTag::where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        return view('timeline.index', [
            'timelineData' => $timelineData,
            'streak' => $streak,
            'totalCheckins' => CheckIn::where('user_id', $user->id)->count(),
            'allTags' => $allTags,
        ]);
    }
}
