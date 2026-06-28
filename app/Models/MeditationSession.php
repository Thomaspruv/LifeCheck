<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeditationSession extends Model
{
    protected $fillable = [
        'user_id',
        'exercise_id',
        'exercise_name',
        'type',
        'duration_seconds',
        'completed',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(BreathingExercise::class, 'exercise_id');
    }
}
