<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'target_date',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'target_date' => 'date',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(GoalMilestone::class)->orderBy('order');
    }

    /**
     * Calculate the completion percentage based on milestones (0-100).
     */
    public function getProgressPercentAttribute(): int
    {
        $total = $this->milestones()->count();
        if ($total === 0) {
            return 0;
        }

        $done = $this->milestones()->where('is_completed', true)->count();
        return min(100, (int) round(($done / $total) * 100));
    }

    /**
     * Get completed milestones count.
     */
    public function getCompletedMilestonesCountAttribute(): int
    {
        return $this->milestones()->where('is_completed', true)->count();
    }

    /**
     * Get total milestones count.
     */
    public function getTotalMilestonesCountAttribute(): int
    {
        return $this->milestones()->count();
    }
}
