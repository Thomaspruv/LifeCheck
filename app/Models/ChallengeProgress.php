<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeProgress extends Model
{
    protected $fillable = [
        'personal_challenge_id',
        'date',
        'is_done',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_done' => 'boolean',
        ];
    }

    public function personalChallenge(): BelongsTo
    {
        return $this->belongsTo(PersonalChallenge::class);
    }
}
