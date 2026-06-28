<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgression extends Model
{
    protected $fillable = [
        'user_id',
        'total_xp',
        'level',
        'consistency_xp',
        'wellbeing_xp',
        'presence_xp',
        'engagement_xp',
    ];

    protected function casts(): array
    {
        return [
            'total_xp' => 'integer',
            'level' => 'integer',
            'consistency_xp' => 'integer',
            'wellbeing_xp' => 'integer',
            'presence_xp' => 'integer',
            'engagement_xp' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
