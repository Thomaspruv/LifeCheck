<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmotionTag extends Model
{
    protected $fillable = ['user_id', 'name', 'color', 'icon'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkIns(): BelongsToMany
    {
        return $this->belongsToMany(CheckIn::class, 'check_in_emotion_tag')
            ->withTimestamps();
    }
}
