<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyInsight extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'week_end',
        'avg_mood',
        'dominant_emotion',
        'checkin_count',
        'total_days',
        'trend',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'week_end' => 'date',
            'avg_mood' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
