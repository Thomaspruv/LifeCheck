<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\StreakBadge;
use Illuminate\Support\Facades\DB;

class StreakService
{
    /**
     * Calculate current streak, best streak, and streak history for a user.
     *
     * @param int $userId
     * @return array{current: int, best: int, history: array<array{date: string, streak: int}>}
     */
    public function calculateStreaks(int $userId): array
    {
        $dates = CheckIn::where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->pluck('date')
            ->map(fn ($d) => $d instanceof \Carbon\Carbon ? $d->toDateString() : (string) $d);

        if ($dates->isEmpty()) {
            return [
                'current' => 0,
                'best' => 0,
                'history' => [],
            ];
        }

        $currentStreak = 0;
        $bestStreak = 0;
        $history = [];

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $runStreak = 0;
        $prevDate = null;

        foreach ($dates as $date) {
            if ($prevDate === null) {
                $runStreak = 1;
            } else {
                $prevCarbon = \Carbon\Carbon::parse($prevDate);
                $currentCarbon = \Carbon\Carbon::parse($date);

                if ($prevCarbon->diffInDays($currentCarbon) === 1) {
                    $runStreak++;
                } elseif ($prevCarbon->diffInDays($currentCarbon) === 0) {
                    // same day, skip
                    continue;
                } else {
                    $runStreak = 1;
                }
            }

            $history[] = [
                'date' => $date,
                'streak' => $runStreak,
            ];

            $bestStreak = max($bestStreak, $runStreak);
            $prevDate = $date;
        }

        // Current streak: check from today backwards
        $currentStreak = 0;
        $cursor = $today;

        $dateSet = $dates->flip();

        while (isset($dateSet[$cursor])) {
            $currentStreak++;
            $cursor = now()->subDays($currentStreak)->toDateString();
        }

        // If not checked in today but checked in yesterday, streak continues
        if ($currentStreak === 0 && isset($dateSet[$yesterday])) {
            $currentStreak = 1;
            $cursor = now()->subDays(2)->toDateString();
            while (isset($dateSet[$cursor])) {
                $currentStreak++;
                $cursor = now()->subDays($currentStreak + 1)->toDateString();
            }
        }

        return [
            'current' => $currentStreak,
            'best' => $bestStreak,
            'history' => $history,
        ];
    }

    /**
     * Check and award milestone badges for the user.
     *
     * @param int $userId
     * @return array<int, array{badge_type: string, badge_name: string}> Newly awarded badges
     */
    public function checkAndAwardBadges(int $userId): array
    {
        $streaks = $this->calculateStreaks($userId);
        $current = $streaks['current'];

        $milestones = [
            7 => ['type' => '7_day', 'name' => '🔥 7 Days'],
            30 => ['type' => '30_day', 'name' => '💪 30 Days'],
            100 => ['type' => '100_day', 'name' => '👑 100 Days'],
            365 => ['type' => '365_day', 'name' => '🏆 365 Days'],
        ];

        $awarded = [];

        foreach ($milestones as $days => $badge) {
            if ($current >= $days) {
                $exists = StreakBadge::where('user_id', $userId)
                    ->where('badge_type', $badge['type'])
                    ->exists();

                if (!$exists) {
                    StreakBadge::create([
                        'user_id' => $userId,
                        'badge_type' => $badge['type'],
                        'badge_name' => $badge['name'],
                        'earned_at' => now(),
                    ]);

                    $awarded[] = $badge;
                }
            }
        }

        return $awarded;
    }

    /**
     * Get the checked dates for a user in a given month/year.
     *
     * @param int $userId
     * @param int $year
     * @param int $month
     * @return array<string> Array of date strings (Y-m-d) that have check-ins
     */
    public function getCheckedDatesInMonth(int $userId, int $year, int $month): array
    {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = \Carbon\Carbon::parse($start)->endOfMonth()->toDateString();

        return CheckIn::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->pluck('date')
            ->map(fn ($d) => $d instanceof \Carbon\Carbon ? $d->toDateString() : (string) $d)
            ->toArray();
    }

    /**
     * Get all badges earned by a user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBadges(int $userId)
    {
        return StreakBadge::where('user_id', $userId)
            ->orderBy('earned_at', 'desc')
            ->get();
    }
}
