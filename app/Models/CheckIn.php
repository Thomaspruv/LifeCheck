<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CheckIn extends Model
{
    protected $fillable = ['user_id', 'template_id', 'date', 'notes', 'sentiment_score', 'sentiment_label', 'sentiment_intensity'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'sentiment_score' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CheckInItem::class);
    }

    public function emotionTags(): BelongsToMany
    {
        return $this->belongsToMany(EmotionTag::class, 'check_in_emotion_tag')
            ->withTimestamps();
    }
}
