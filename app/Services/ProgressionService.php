<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\UserProgression;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressionService
{
    private const XP_PER_CHECKIN = 10;
    private const STREAK_BONUS_CAP = 50;
    private const STREAK_BONUS_PER_DAY = 5;
    private const LEVEL_XP_BASE = 100;
    private const LEVEL_XP_MULTIPLIER = 1.5;

    // Branch definitions with their node thresholds
    private const BRANCHES = [
        'consistency' => [
            'label' => 'Régularité',
            'icon' => '🔥',
            'color' => 'orange',
            'nodes' => [
                1 => ['label' => 'Débutant', 'icon' => '🌱', 'desc' => '3 jours de streak'],
                2 => ['label' => 'Assidu', 'icon' => '🌿', 'desc' => '7 jours de streak'],
                3 => ['label' => 'Régulier', 'icon' => '🌳', 'desc' => '21 jours de streak'],
                4 => ['label' => 'Persévérant', 'icon' => '🌲', 'desc' => '60 jours de streak'],
                5 => ['label' => 'Légende', 'icon' => '🏆', 'desc' => '150 jours de streak'],
            ],
            'thresholds' => [3, 7, 21, 60, 150],
            'units' => 'jours',
        ],
        'wellbeing' => [
            'label' => 'Bien-être',
            'icon' => '🧘',
            'color' => 'indigo',
            'nodes' => [
                1 => ['label' => 'Conscient', 'icon' => '😊', 'desc' => 'Moyenne ≥ 3/10'],
                2 => ['label' => 'Équilibré', 'icon' => '😌', 'desc' => 'Moyenne ≥ 5/10'],
                3 => ['label' => 'Serein', 'icon' => '🕊️', 'desc' => 'Moyenne ≥ 7/10'],
                4 => ['label' => 'Rayonnant', 'icon' => '🌟', 'desc' => 'Moyenne ≥ 8/10'],
                5 => ['label' => 'Épanoui', 'icon' => '💫', 'desc' => 'Moyenne ≥ 9.5/10'],
            ],
            'thresholds' => [3, 5, 7, 8, 9.5],
            'units' => '/10',
        ],
        'presence' => [
            'label' => 'Présence',
            'icon' => '📅',
            'color' => 'emerald',
            'nodes' => [
                1 => ['label' => 'Visiteur', 'icon' => '👣', 'desc' => '10 check-ins'],
                2 => ['label' => 'Habitué', 'icon' => '🚶', 'desc' => '30 check-ins'],
                3 => ['label' => 'Fidèle', 'icon' => '🏃', 'desc' => '60 check-ins'],
                4 => ['label' => 'Dévoué', 'icon' => '🧗', 'desc' => '120 check-ins'],
                5 => ['label' => 'Maître', 'icon' => '👑', 'desc' => '250 check-ins'],
            ],
            'thresholds' => [10, 30, 60, 120, 250],
            'units' => 'check-ins',
        ],
        'engagement' => [
            'label' => 'Engagement',
            'icon' => '🎯',
            'color' => 'purple',
            'nodes' => [
                1 => ['label' => 'Curieux', 'icon' => '🔍', 'desc' => '25% semaine complétée'],
                2 => ['label' => 'Motivé', 'icon' => '🔥', 'desc' => '50% semaine complétée'],
                3 => ['label' => 'Engagé', 'icon' => '💪', 'desc' => '75% semaine complétée'],
                4 => ['label' => 'Exemplaire', 'icon' => '⭐', 'desc' => '3 semaines parfaites'],
                5 => ['label' => 'Champion', 'icon' => '🏅', 'desc' => '10 semaines parfaites'],
            ],
            'thresholds' => [25, 50, 75, 3, 10],
            'units' => '%',
        ],
    ];

    /**
     * Get user progression data with XP, level, and branch status.
     */
    public function getProgression(int $userId): array
    {
        $progression = UserProgression::firstOrCreate(
            ['user_id' => $userId],
            ['total_xp' => 0, 'level' => 1, 'consistency_xp' => 0, 'wellbeing_xp' => 0, 'presence_xp' => 0, 'engagement_xp' => 0]
        );

        // Recalculate XP fresh each time
        $this->recalculateXp($progression);

        $levelData = $this->getLevelData($progression->level);
        $branches = $this->getBranchData($userId);

        return [
            'progression' => $progression,
            'currentLevel' => $progression->level,
            'totalXp' => $progression->total_xp,
            'xpForCurrentLevel' => $levelData['xp_for_current'],
            'xpForNextLevel' => $levelData['xp_for_next'],
            'levelProgress' => $levelData['progress'],
            'branches' => $branches,
        ];
    }

    /**
     * Recalculate all XP values based on actual user data.
     */
    private function recalculateXp(UserProgression $progression): void
    {
        $userId = $progression->user_id;

        // Total check-ins for presence XP
        $totalCheckins = CheckIn::where('user_id', $userId)->count();
        $presenceXp = $totalCheckins * self::XP_PER_CHECKIN;

        // Streak for consistency XP
        $streakService = app(StreakService::class);
        $streaks = $streakService->calculateStreaks($userId);
        $currentStreak = $streaks['current'];
        $streakBonus = min($currentStreak * self::STREAK_BONUS_PER_DAY, self::STREAK_BONUS_CAP);
        $consistencyXp = ($currentStreak * self::XP_PER_CHECKIN) + $streakBonus;

        // Average mood for wellbeing XP
        $avgMood = CheckIn::where('user_id', $userId)
            ->whereHas('items.templateItem', function ($q) {
                $q->where('input_type', 'slider');
            })
            ->with('items.templateItem')
            ->get()
            ->flatMap(function ($c) {
                return $c->items->filter(fn ($i) => $i->templateItem && $i->templateItem->input_type === 'slider');
            })
            ->avg('value') ?? 0;
        $wellbeingXp = (int) round($avgMood * 10 * $totalCheckins);

        // Weekly completion rate for engagement XP
        $engagementXp = $this->calculateEngagementXp($userId, $totalCheckins);

        $totalXp = $presenceXp + $consistencyXp + $wellbeingXp + $engagementXp;

        // Calculate level from XP
        $level = $this->calculateLevel($totalXp);

        $dirty = false;
        if ($progression->total_xp !== $totalXp) {
            $progression->total_xp = $totalXp;
            $dirty = true;
        }
        if ($progression->level !== $level) {
            $progression->level = $level;
            $dirty = true;
        }
        if ($progression->presence_xp !== $presenceXp) {
            $progression->presence_xp = $presenceXp;
            $dirty = true;
        }
        if ($progression->consistency_xp !== $consistencyXp) {
            $progression->consistency_xp = $consistencyXp;
            $dirty = true;
        }
        if ($progression->wellbeing_xp !== $wellbeingXp) {
            $progression->wellbeing_xp = $wellbeingXp;
            $dirty = true;
        }
        if ($progression->engagement_xp !== $engagementXp) {
            $progression->engagement_xp = $engagementXp;
            $dirty = true;
        }

        if ($dirty) {
            $progression->save();
        }
    }

    private function calculateEngagementXp(int $userId, int $totalCheckins): int
    {
        // Count weeks with > 5 check-ins (perfect weeks)
        $perfectWeeks = CheckIn::where('user_id', $userId)
            ->select(DB::raw("strftime('%Y-%W', date) as week"), DB::raw('COUNT(*) as count'))
            ->groupBy('week')
            ->having('count', '>=', 5)
            ->get()
            ->count();

        // Calculate weekly completion %
        $weeksActive = CheckIn::where('user_id', $userId)
            ->select(DB::raw("strftime('%Y-%W', date) as week"))
            ->distinct()
            ->get()
            ->count();

        $completionRate = $weeksActive > 0
            ? round(($perfectWeeks / $weeksActive) * 100)
            : 0;

        return ($completionRate * 5) + ($perfectWeeks * 20);
    }

    /**
     * Calculate level from total XP.
     * Level N requires: XP = sum of (BASE * MULTIPLIER^(lvl-1)) for lvl=1..N
     */
    private function calculateLevel(int $totalXp): int
    {
        $level = 1;
        $accumulated = 0;

        while (true) {
            $needed = (int) round(self::LEVEL_XP_BASE * pow(self::LEVEL_XP_MULTIPLIER, $level - 1));
            if ($accumulated + $needed > $totalXp) {
                break;
            }
            $accumulated += $needed;
            $level++;
        }

        return max(1, $level);
    }

    /**
     * Get XP thresholds for current and next level with progress percentage.
     */
    private function getLevelData(int $currentLevel): array
    {
        $xpForCurrent = 0;
        $xpForNext = (int) round(self::LEVEL_XP_BASE * pow(self::LEVEL_XP_MULTIPLIER, $currentLevel - 1));

        // The total XP accumulated to reach this level
        for ($i = 1; $i < $currentLevel; $i++) {
            $xpForCurrent += (int) round(self::LEVEL_XP_BASE * pow(self::LEVEL_XP_MULTIPLIER, $i - 1));
        }

        return [
            'xp_for_current' => $xpForCurrent,
            'xp_for_next' => $xpForNext,
            'progress' => 0, // Will be calculated with actual XP
        ];
    }

    /**
     * Get progression data for each branch.
     */
    private function getBranchData(int $userId): array
    {
        $streakService = app(StreakService::class);
        $streaks = $streakService->calculateStreaks($userId);
        $currentStreak = $streaks['current'];

        $totalCheckins = CheckIn::where('user_id', $userId)->count();

        // Average mood
        $avgMood = CheckIn::where('user_id', $userId)
            ->whereHas('items.templateItem', function ($q) {
                $q->where('input_type', 'slider');
            })
            ->with('items.templateItem')
            ->get()
            ->flatMap(function ($c) {
                return $c->items->filter(fn ($i) => $i->templateItem && $i->templateItem->input_type === 'slider');
            })
            ->avg('value') ?? 0;

        // Perfect weeks for engagement
        $totalWeeks = CheckIn::where('user_id', $userId)
            ->select(DB::raw("strftime('%Y-%W', date) as week"), DB::raw('COUNT(*) as count'))
            ->groupBy('week')
            ->having('count', '>=', 5)
            ->get()
            ->count();

        $branchData = [];

        $metricMap = [
            'consistency' => $currentStreak,
            'wellbeing' => (float) number_format($avgMood, 1),
            'presence' => $totalCheckins,
            'engagement' => $totalWeeks,
        ];

        foreach (self::BRANCHES as $key => $branch) {
            $metric = $metricMap[$key];
            $nodes = [];
            $unlockedCount = 0;

            foreach ($branch['nodes'] as $nodeLevel => $node) {
                $threshold = $branch['thresholds'][$nodeLevel - 1];
                $isUnlocked = $metric >= $threshold;
                if ($isUnlocked) {
                    $unlockedCount++;
                }

                $progress = 0;
                if (!$isUnlocked) {
                    $prevThreshold = $nodeLevel > 1 ? $branch['thresholds'][$nodeLevel - 2] : 0;
                    $range = $threshold - $prevThreshold;
                    $progress = $range > 0
                        ? min(100, max(0, ($metric - $prevThreshold) / $range * 100))
                        : 0;
                }

                $nodes[] = [
                    'level' => $nodeLevel,
                    'label' => $node['label'],
                    'icon' => $node['icon'],
                    'desc' => $node['desc'],
                    'threshold' => $threshold,
                    'is_unlocked' => $isUnlocked,
                    'progress' => (int) round($progress),
                ];
            }

            $branchData[$key] = [
                'key' => $key,
                'label' => $branch['label'],
                'icon' => $branch['icon'],
                'color' => $branch['color'],
                'metric' => $metric,
                'units' => $branch['units'],
                'nodes' => $nodes,
                'unlocked_count' => $unlockedCount,
                'total_nodes' => count($nodes),
            ];
        }

        return $branchData;
    }

    /**
     * Get all branch definitions (for static display).
     */
    public static function getBranches(): array
    {
        return self::BRANCHES;
    }
}
