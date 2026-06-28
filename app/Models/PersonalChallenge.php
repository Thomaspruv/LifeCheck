<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonalChallenge extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'duration_days',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ChallengeProgress::class);
    }

    /**
     * Calculate the current progress percentage (0-100).
     */
    public function getProgressPercentAttribute(): int
    {
        $totalDays = $this->duration_days;
        if ($totalDays <= 0) {
            return 0;
        }

        $doneDays = $this->progress()->where('is_done', true)->count();
        return min(100, (int) round(($doneDays / $totalDays) * 100));
    }

    /**
     * Get the current streak of consecutive done days for this challenge.
     */
    public function getCurrentStreakAttribute(): int
    {
        $progress = $this->progress()
            ->where('is_done', true)
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn ($d) => $d instanceof \Carbon\Carbon ? $d->toDateString() : (string) $d)
            ->toArray();

        if (empty($progress)) {
            return 0;
        }

        $streak = 0;
        $cursor = now()->toDateString();

        while (in_array($cursor, $progress)) {
            $streak++;
            $cursor = now()->subDays($streak)->toDateString();
        }

        if ($streak === 0) {
            $yesterday = now()->subDay()->toDateString();
            if (in_array($yesterday, $progress)) {
                $streak = 1;
                $cursor = now()->subDays(2)->toDateString();
                while (in_array($cursor, $progress)) {
                    $streak++;
                    $cursor = now()->subDays($streak + 1)->toDateString();
                }
            }
        }

        return $streak;
    }
}
