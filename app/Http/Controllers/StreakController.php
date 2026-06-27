<?php

namespace App\Http\Controllers;

use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StreakController extends Controller
{
    public function __construct(
        private readonly StreakService $streakService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $streaks = $this->streakService->calculateStreaks($userId);

        // Award badges if applicable
        $awarded = $this->streakService->checkAndAwardBadges($userId);

        // Current month for calendar
        $year = (int) ($request->input('year', now()->year));
        $month = (int) ($request->input('month', now()->month));

        $checkedDates = $this->streakService->getCheckedDatesInMonth($userId, $year, $month);
        $checkedDatesSet = array_flip($checkedDates);

        // Build calendar grid
        $calendar = $this->buildCalendar($year, $month, $checkedDatesSet);

        // Badges
        $badges = $this->streakService->getBadges($userId);

        // Milestones definition
        $milestones = [
            ['days' => 7, 'icon' => '🔥', 'label' => '7 Days'],
            ['days' => 30, 'icon' => '💪', 'label' => '30 Days'],
            ['days' => 100, 'icon' => '👑', 'label' => '100 Days'],
            ['days' => 365, 'icon' => '🏆', 'label' => '365 Days'],
        ];

        // Navigation for month selector
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        $monthNames = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        return view('streaks.index', [
            'currentStreak' => $streaks['current'],
            'bestStreak' => $streaks['best'],
            'history' => $streaks['history'],
            'calendar' => $calendar,
            'year' => $year,
            'month' => $month,
            'monthName' => $monthNames[$month],
            'prevYear' => $prevYear,
            'prevMonth' => $prevMonth,
            'nextYear' => $nextYear,
            'nextMonth' => $nextMonth,
            'checkedDates' => $checkedDates,
            'badges' => $badges,
            'milestones' => $milestones,
            'awarded' => $awarded,
        ]);
    }

    /**
     * Build a monthly calendar grid.
     *
     * @param int $year
     * @param int $month
     * @param array<string, true> $checkedDatesSet
     * @return array<int, array{day: int, isChecked: bool, isToday: bool, isFuture: bool}>
     */
    private function buildCalendar(int $year, int $month, array $checkedDatesSet): array
    {
        $daysInMonth = \Carbon\Carbon::parse(sprintf('%04d-%02d-01', $year, $month))->daysInMonth;
        $today = now()->toDateString();
        $calendar = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);

            $calendar[] = [
                'day' => $day,
                'isChecked' => isset($checkedDatesSet[$dateStr]),
                'isToday' => $dateStr === $today,
                'isFuture' => $dateStr > $today,
            ];
        }

        return $calendar;
    }
}
