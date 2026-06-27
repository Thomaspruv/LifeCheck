<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreakBadge extends Model
{
    protected $fillable = ['user_id', 'badge_type', 'badge_name', 'earned_at'];

    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
