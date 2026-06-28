<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class BreathingExercise extends Model
{
    protected $fillable = [
        'name',
        'benefits',
        'description',
        'type',
        'category',
        'pattern_data',
        'duration_options',
        'icon',
        'color',
        'instructions',
        'is_default',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'pattern_data' => 'array',
            'duration_options' => 'array',
            'is_default' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the default breathing exercises bundled with the app.
     */
    public static function getDefaultExercises(): array
    {
        return [
            [
                'name' => 'Respiration carrée (Box breathing)',
                'description' => 'Technique utilisée par les Navy SEALs pour rester calme sous pression. Inspire, retiens, expire, retiens — 4 secondes chaque phase.',
                'type' => 'breathing',
                'category' => 'Calme & Focus',
                'pattern_data' => ['inhale' => 4, 'hold1' => 4, 'exhale' => 4, 'hold2' => 4],
                'duration_options' => [1, 3, 5, 10],
                'icon' => '⬜',
                'color' => '#6366f1',
                'benefits' => 'Réduit l\'anxiété, améliore la concentration, régule le système nerveux.',
                'instructions' => "1. Assieds-toi confortablement, le dos droit.\n2. Inspire profondément par le nez (4 sec)\n3. Retiens ta respiration (4 sec)\n4. Expire lentement par la bouche (4 sec)\n5. Retiens à vide (4 sec)\n6. Répète le cycle.",
            ],
            [
                'name' => 'Respiration 4-7-8 (Relaxation profonde)',
                'description' => 'La technique du "soupir relaxant" du Dr. Andrew Weil. Idéale pour s\'endormir ou se calmer rapidement.',
                'type' => 'breathing',
                'category' => 'Sommeil & Détente',
                'pattern_data' => ['inhale' => 4, 'hold1' => 7, 'exhale' => 8, 'hold2' => 0],
                'duration_options' => [1, 2, 3, 5],
                'icon' => '🌙',
                'color' => '#8b5cf6',
                'benefits' => 'Calme le mental, prépare au sommeil, réduit le stress, abaisse la fréquence cardiaque.',
                'instructions' => "1. Place le bout de ta langue contre le palais.\n2. Expire complètement par la bouche.\n3. Inspire par le nez (4 sec)\n4. Retiens ta respiration (7 sec)\n5. Expire par la bouche (8 sec)\n6. Répète 4 cycles.",
            ],
            [
                'name' => 'Cohérence cardiaque (5-5)',
                'description' => 'Respiration équilibrée qui synchronise le rythme cardiaque avec la respiration. 5 secondes à l\'inspire, 5 à l\'expire.',
                'type' => 'breathing',
                'category' => 'Équilibre',
                'pattern_data' => ['inhale' => 5, 'hold1' => 0, 'exhale' => 5, 'hold2' => 0],
                'duration_options' => [3, 5, 10, 15],
                'icon' => '💚',
                'color' => '#10b981',
                'benefits' => 'Équilibre le système nerveux autonome, réduit le stress chronique, améliore la variabilité cardiaque.',
                'instructions' => "1. Installe-toi confortablement.\n2. Inspire profondément par le nez (5 sec)\n3. Expire doucement par la bouche (5 sec)\n4. Garde un rythme régulier.\n5. Continue pendant 5 minutes minimum.",
            ],
            [
                'name' => 'Respiration alternée (Nadi Shodhana)',
                'description' => 'Technique de yoga qui équilibre les hémisphères du cerveau et calme le mental.',
                'type' => 'breathing',
                'category' => 'Énergie & Équilibre',
                'pattern_data' => ['inhale' => 4, 'hold1' => 0, 'exhale' => 6, 'hold2' => 0],
                'duration_options' => [3, 5, 7, 10],
                'icon' => '🔄',
                'color' => '#f59e0b',
                'benefits' => 'Équilibre les énergies, clarifie l\'esprit, réduit l\'anxiété, améliore la concentration.',
                'instructions' => "1. Assieds-toi en posture confortable.\n2. Bouche la narine droite avec le pouce.\n3. Inspire par la narine gauche (4 sec)\n4. Bouche la narine gauche avec l'annulaire.\n5. Expire par la narine droite (6 sec)\n6. Inspire par la narine droite (4 sec)\n7. Bouche la narine droite.\n8. Expire par la narine gauche (6 sec)\n9. Répète le cycle.",
            ],
            [
                'name' => 'Méditation guidée (pleine conscience)',
                'description' => 'Une session de méditation en pleine conscience avec guide intégré. Suis les instructions pour te recentrer.',
                'type' => 'meditation',
                'category' => 'Pleine conscience',
                'pattern_data' => null,
                'duration_options' => [3, 5, 10, 15, 20],
                'icon' => '🧘',
                'color' => '#ec4899',
                'benefits' => 'Réduit le stress, améliore la concentration, développe la pleine conscience, favorise le bien-être émotionnel.',
                'instructions' => "1. Installe-toi confortablement, assis ou allongé.\n2. Ferme les yeux.\n3. Porte ton attention sur ta respiration naturelle.\n4. Observe tes pensées sans jugement.\n5. Reviens doucement à ta respiration.\n6. Quand la session se termine, ouvre les yeux doucement.",
            ],
            [
                'name' => 'Scan corporel (Body Scan)',
                'description' => 'Méditation guidée qui parcourt chaque partie du corps pour relâcher les tensions accumulées.',
                'type' => 'meditation',
                'category' => 'Relaxation',
                'pattern_data' => null,
                'duration_options' => [5, 10, 15, 20],
                'icon' => '👁️',
                'color' => '#06b6d4',
                'benefits' => 'Relâche les tensions musculaires, améliore la conscience corporelle, favorise un sommeil réparateur.',
                'instructions' => "1. Allonge-toi confortablement.\n2. Ferme les yeux.\n3. Porte ton attention sur tes pieds — sens les tensions.\n4. Remonte lentement : jambes, hanches, ventre, dos.\n5. Continue vers : poitrine, épaules, bras, mains.\n6. Termine par : cou, mâchoire, visage, cuir chevelu.\n7. Relâche chaque zone avant de passer à la suivante.",
            ],
        ];
    }

    /**
     * Seed the default exercises into the database.
     */
    public static function seedDefaults(): void
    {
        foreach (self::getDefaultExercises() as $data) {
            self::firstOrCreate(
                ['name' => $data['name'], 'is_default' => true],
                $data
            );
        }
    }
}
