<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalityTrait extends Model
{
    protected $fillable = [
        'user_id',
        'openness',
        'conscientiousness',
        'extraversion',
        'agreeableness',
        'neuroticism',
        'answers',
    ];

    protected function casts(): array
    {
        return [
            'openness' => 'integer',
            'conscientiousness' => 'integer',
            'extraversion' => 'integer',
            'agreeableness' => 'integer',
            'neuroticism' => 'integer',
            'answers' => 'array',
        ];
    }

    /**
     * Get the user that owns the personality traits.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the label for the dominant trait.
     */
    public function dominantTrait(): ?string
    {
        $traits = [
            'openness' => 'Ouverture',
            'conscientiousness' => 'Conscienciosité',
            'extraversion' => 'Extraversion',
            'agreeableness' => 'Agréabilité',
            'neuroticism' => 'Névrosisme',
        ];

        $max = null;
        $maxTrait = null;
        foreach ($traits as $key => $label) {
            if ($this->$key !== null && ($max === null || $this->$key > $max)) {
                $max = $this->$key;
                $maxTrait = $label;
            }
        }

        return $maxTrait;
    }

    /**
     * Get a human-readable description of the personality profile.
     */
    public function getProfileDescription(): string
    {
        $descriptions = [];

        if ($this->openness !== null) {
            $descriptions[] = $this->openness >= 60
                ? 'Esprit curieux et imaginatif, ouvert aux nouvelles expériences.'
                : 'Plutôt terre-à-terre, préfère le concret et la tradition.';
        }

        if ($this->conscientiousness !== null) {
            $descriptions[] = $this->conscientiousness >= 60
                ? 'Organisé(e) et fiable, tu aimes avoir un cadre.'
                : 'Plutôt flexible et spontané(e), tu préfères la liberté.';
        }

        if ($this->extraversion !== null) {
            $descriptions[] = $this->extraversion >= 60
                ? 'Sociable et dynamique, tu puises ton énergie dans les échanges.'
                : 'Plutôt réservé(e) et calme, tu apprécies les moments en solo.';
        }

        if ($this->agreeableness !== null) {
            $descriptions[] = $this->agreeableness >= 60
                ? 'Empathique et coopératif(ive), les relations sont importantes pour toi.'
                : 'Plutôt direct(e) et indépendant(e), tu n\'as pas peur du conflit.';
        }

        if ($this->neuroticism !== null) {
            $descriptions[] = $this->neuroticism >= 60
                ? 'Sensible au stress, tu ressens les émotions avec intensité.'
                : 'Stable émotionnellement, tu gardes ton calme face aux difficultés.';
        }

        return implode("\n", $descriptions);
    }
}
