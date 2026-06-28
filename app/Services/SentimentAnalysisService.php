<?php

namespace App\Services;

class SentimentAnalysisService
{
    /**
     * French positive lexicon — words that indicate positive sentiment.
     */
    private const POSITIVE_WORDS = [
        'heureux', 'heureuse', 'joie', 'joyeux', 'joyeuse', 'content', 'contente',
        'génial', 'super', 'merveilleux', 'merveilleuse', 'formidable',
        'excellent', 'excellente', 'magnifique', 'fantastique', 'extraordinaire',
        'beau', 'belle', 'agréable', 'plaisir', 'ravissant', 'ravie',
        'sourire', 'rire', 'amusant', 'drôle', 'comique',
        'sérénité', 'serein', 'sereine', 'calme', 'paisible', 'tranquille',
        'reconnaissant', 'reconnaissante', 'gratitude', 'merci',
        'passionnant', 'passionnante', 'inspirant', 'inspirante',
        'épanoui', 'épanouie', 'épanouissement', 'accompli', 'accomplie',
        'réussi', 'réussie', 'réussite', 'succès', 'victoire',
        'chance', 'chanceux', 'chanceuse', 'adorable', 'charmant', 'charmante',
        'espérance', 'espoir', 'optimiste', 'confiant', 'confiante',
        'détendu', 'détendue', 'relax', 'relaxé', 'relaxée',
        'satisfait', 'satisfaite', 'satisfaction', 'fier', 'fière', 'fierté',
        'amour', 'aimer', 'adorer', 'apprécier', 'affection', 'tendresse',
        'bien-être', 'bienêtre', 'bien', 'bon', 'bonne', 'meilleur', 'meilleure',
        'progresser', 'progrès', 'améliorer', 'amélioration', 'croissance',
        'motivé', 'motivée', 'motivation', 'déterminé', 'déterminée',
        'énergie', 'énergique', 'dynamique', 'vitalité',
        'positif', 'positive', 'positivité', 'encouragé', 'encouragée',
        'courage', 'courageux', 'courageuse', 'brave',
        'sympa', 'sympathique', 'gentil', 'gentille', 'bonté',
        'paisible', 'harmonie', 'harmonieux', 'harmonieuse',
        'lumière', 'lumineux', 'lumineuse', 'rayonner', 'rayonnant',
        'sublime', 'idyllique', 'paradisiaque', 'magique',
        'douceur', 'doux', 'douce', 'tendre',
        'éclat', 'éclatant', 'éclatante', 'brillant', 'brillante',
        'performance', 'performant', 'performante', 'efficace',
        'abondance', 'abondant', 'abondante', 'prospère', 'prospérité',
        'rigoler', 'rigolade', 'rigolo', 'rigolote', 'hilare',
        'gai', 'gaie', 'gaieté', 'allègre', 'allégresse',
        'félicité', 'béatitude', 'extase', 'euphorie', 'euphorique',
        'enthousiasme', 'enthousiaste', 'ardeur', 'entrain',
        'plénitude', 'plein', 'pleine', 'comblé', 'comblée',
        'vivant', 'vivante', 'vie', 'vivre',
        'agréablement', 'parfaitement', 'joyeusement', 'heureusement',
        'facile', 'facilement', 'simple', 'simplicité',
        'intéressant', 'intéressante', 'captivant', 'captivante',
        'productif', 'productive', 'productivité', 'efficacité',
        'accomplissement', 'réalisation', 'abouti', 'aboutie',
        'nourrissant', 'nourrissante', 'enrichissant', 'enrichissante',
        'ressourçant', 'ressourçante', 'revigorant', 'revigorante',
        'apaisant', 'apaisante', 'réconfortant', 'réconfortante',
        'chaleureux', 'chaleureuse', 'chaleur', 'chaleureusement',
        'émerveillé', 'émerveillée', 'émerveillement', 'ébloui', 'éblouie',
        'respect', 'respectueux', 'respectueuse', 'estime',
        'consolé', 'consolée', 'réconfort', 'soutien',
        'solide', 'stable', 'stabilité', 'équilibre', 'équilibré', 'équilibrée',
        'hardi', 'hardie', 'audacieux', 'audacieuse', 'ambitieux', 'ambitieuse',
        'authentique', 'sincère', 'véritable', 'vrai', 'vraie',
        'rire', 'souriant', 'souriante', 'éclat de rire',
        'fun', 'cool', 'top', 'nickel', 'parfait', 'parfaite',
        'joli', 'jolie', 'mignon', 'mignonne',
        'étonnant', 'étonnante', 'surprenant', 'surprenante',
        'exceptionnel', 'exceptionnelle', 'remarquable', 'incroyable',
        'impressionnant', 'impressionnante', 'époustouflant',
        'gratifiant', 'gratifiante', 'valorisant', 'valorisante',
        'libre', 'liberté', 'libération', 'libéré', 'libérée',
        'frais', 'fraîche', 'fraîcheur', 'léger', 'légère', 'légèreté',
    ];

    /**
     * French negative lexicon — words that indicate negative sentiment.
     */
    private const NEGATIVE_WORDS = [
        'triste', 'tristesse', 'chagrin', 'peine', 'douleur', 'douloureux',
        'malheureux', 'malheureuse', 'malheur', 'déprimé', 'déprimée',
        'dépression', 'dépressif', 'dépressive', 'mélancolie', 'mélancolique',
        'désespoir', 'désespéré', 'désespérée', 'désespérant',
        'angoissé', 'angoissée', 'angoisse', 'anxiété', 'anxieux', 'anxieuse',
        'stressé', 'stressée', 'stress', 'stressant', 'stressante',
        'peur', 'peureux', 'peureuse', 'effrayé', 'effrayée', 'effrayant',
        'crainte', 'craintif', 'craintive', 'redouter', 'redoutable',
        'inquiet', 'inquiète', 'inquiétude', 'souci', 'soucieux', 'soucieuse',
        'nerveux', 'nerveuse', 'nervosité', 'tendu', 'tendue', 'tension',
        'fatigué', 'fatiguée', 'fatigue', 'épuisé', 'épuisée', 'épuisement',
        'éreinté', 'éreintée', 'exténué', 'exténuée', 'harassé', 'harassée',
        'las', 'lasse', 'lassitude', 'accablé', 'accablée',
        'colère', 'en colère', 'fâché', 'fâchée', 'furieux', 'furieuse',
        'énervé', 'énervée', 'énervement', 'agacé', 'agacée', 'agacement',
        'irrité', 'irritée', 'irritation', 'exaspéré', 'exaspérée',
        'rage', 'rageur', 'rageuse', 'haine', 'haineux', 'haineuse',
        'frustré', 'frustrée', 'frustration', 'amer', 'amère', 'amertume',
        'déçu', 'déçue', 'déception', 'décevant', 'décevante',
        'désillusion', 'désillusionné', 'désillusionnée', 'désenchanté',
        'dégouté', 'dégoutée', 'dégoût', 'dégoûtant', 'dégoûtante',
        'honte', 'honteux', 'honteuse', 'humilié', 'humiliée', 'humiliation',
        'coupable', 'culpabilité', 'remords', 'regret', 'regretter',
        'solitude', 'seul', 'seule', 'isolé', 'isolée', 'isolement',
        'abandonné', 'abandonnée', 'abandon', 'rejet', 'rejeté', 'rejetée',
        'vide', 'vide', 'désolé', 'désolée', 'vide intérieur',
        'souffrance', 'souffrir', 'souffrant', 'souffrante', 'martyre',
        'pleur', 'pleurer', 'pleuré', 'larme', 'sanglots', 'sangloter',
        'blessé', 'blessée', 'blessure', 'meurtri', 'meurtrie',
        'mal', 'mauvais', 'mauvaise', 'pire', 'terrible', 'horrible',
        'affreux', 'affreuse', 'atroce', 'épouvantable', 'abominable',
        'désastreux', 'désastreuse', 'désastre', 'catastrophe', 'catastrophique',
        'nul', 'nulle', 'négatif', 'négative', 'négativité',
        'difficile', 'compliqué', 'compliquée', 'dur', 'dure', 'pénible',
        'insupportable', 'insoutenable', 'intolérable', 'invivable',
        'lourd', 'lourde', 'pesant', 'pesante', 'oppressant', 'oppressante',
        'étouffant', 'étouffante', 'suffocant', 'suffocante',
        'ennui', 'ennuyé', 'ennuyée', 'ennuyeux', 'ennuyeuse',
        'monotone', 'monotonie', 'routine', 'lassant', 'lassante',
        'indifférent', 'indifférente', 'indifférence', 'apathique',
        'démotivé', 'démotivée', 'démotivation', 'découragé', 'découragée',
        'découragement', 'abattu', 'abattue', 'abattement',
        'morose', 'morosité', 'sombre', 'obscur', 'obscure', 'ténébreux',
        'pessimiste', 'pessimisme', 'cynique', 'cynisme',
        'crispé', 'crispée', 'contracté', 'contractée',
        'insatisfait', 'insatisfaite', 'insatisfaction', 'mécontent', 'mécontente',
        'jaloux', 'jalouse', 'jalousie', 'envieux', 'envieuse', 'envie',
        'rancune', 'rancunier', 'rancunière', 'ressentiment', 'vengeance',
        'trahi', 'trahie', 'trahison', 'trompé', 'trompée',
        'incompris', 'incomprise', 'incompréhension', 'mépris', 'méprisé',
        'ignoré', 'ignorée', 'négligé', 'négligée', 'mis de côté',
        'impuissant', 'impuissante', 'impuissance', 'vulnérable',
        'perdu', 'perdue', 'perte', 'égard', 'égaré', 'égarée',
        'confus', 'confuse', 'confusion', 'embrouillé', 'embrouillée',
        'désorienté', 'désorientée', 'désorientation', 'perplexe',
        'inquiétant', 'inquiétante', 'alarmant', 'alarmante',
        'problème', 'problèmes', 'souci', 'soucis', 'difficulté', 'difficultés',
        'obstacle', 'obstacles', 'embûche', 'entrave', 'blocage',
        'conflit', 'conflits', 'dispute', 'disputes', 'querelle', 'querelles',
        'échec', 'échecs', 'raté', 'ratée', 'rater', 'foiré', 'foirée',
        'erreur', 'erreurs', 'faute', 'fautes', 'bourde', 'bourdes',
        'crise', 'crises', 'trauma', 'traumatisme', 'traumatisé', 'traumatisée',
        'panique', 'paniqué', 'paniquée', 'phobie', 'phobique',
        'insomnie', 'insomnies', 'cauchemar', 'cauchemars', 'cauchenardesque',
        'migraine', 'migraines', 'malade', 'maladie', 'souffrant',
        'échec' , 'ratage', 'défaite', 'déroute',
        'lamentable', 'pitoyable', 'misérable', 'minable', 'ridicule',
        'insécurité', 'insécurisé', 'insécurisée', 'précaire',
        'instable', 'instabilité', 'chaotique', 'chaos', 'désordre',
        'agité', 'agitée', 'agitation', 'tourmenté', 'tourmentée',
        'obsédé', 'obsédée', 'obsession', 'obsessionnel',
        'paranoïaque', 'paranoïa', 'méfiant', 'méfiante', 'méfiance',
        'détestable', 'détester', 'haïssable', 'exécrable',
        'toxique', 'nocif', 'nocive', 'nuisible', 'destructeur', 'destructrice',
        'violence', 'violent', 'violente', 'agressif', 'agressive', 'agression',
        'cassé', 'cassée', 'brisé', 'brisée', 'fracassé', 'fracassée',
        'haine', 'hair', 'détester', 'déplorer', 'haïr',
    ];

    /**
     * Negation words that invert sentiment.
     */
    private const NEGATION_WORDS = [
        'pas', 'ne', 'ni', 'plus', 'jamais', 'rien', 'personne',
        'aucun', 'aucune', 'sans', 'guère', 'nullement', 'point',
    ];

    /**
     * Intensifiers that amplify sentiment.
     */
    private const INTENSIFIERS = [
        'très', 'vraiment', 'extrêmement', 'tellement', 'si', 'fort',
        'profondément', 'totalement', 'complètement', 'absolument',
        'particulièrement', 'remarquablement', 'incroyablement',
        'terriblement', 'hautement', 'intensément', 'vivement',
    ];

    /**
     * Analyze the sentiment of a French text.
     *
     * @return array{score: float, label: string, positive_words: array, negative_words: array, intensity: string}
     */
    public function analyze(string $text): array
    {
        $text = mb_strtolower(trim($text));

        if (empty($text)) {
            return [
                'score' => 0.0,
                'label' => 'neutre',
                'positive_words' => [],
                'negative_words' => [],
                'intensity' => 'faible',
            ];
        }

        // Tokenize
        $words = preg_split('/[\s\p{P}]+/u', $text);
        $words = array_filter($words, fn($w) => mb_strlen($w) > 1);
        $words = array_values($words);

        $positiveWords = [];
        $negativeWords = [];
        $score = 0.0;
        $totalWeight = 0;

        $wordCount = count($words);

        for ($i = 0; $i < $wordCount; $i++) {
            $word = $words[$i];
            $multiplier = 1.0;

            // Check for negation in the previous 3 words
            for ($j = max(0, $i - 3); $j < $i; $j++) {
                if (in_array($words[$j], self::NEGATION_WORDS, true)) {
                    $multiplier = -0.5;
                    break;
                }
            }

            // Check for intensifiers in the previous 2 words
            if ($multiplier > 0) {
                for ($j = max(0, $i - 2); $j < $i; $j++) {
                    if (in_array($words[$j], self::INTENSIFIERS, true)) {
                        $multiplier = 1.5;
                        break;
                    }
                }
            }

            // Check for exclamation marks after — amplifies
            $exclamationBonus = 1.0;
            if ($i < $wordCount - 1 && in_array($words[$i + 1], ['!', '!!', '!!!'], true)) {
                $exclamationBonus = 1.3;
            }

            if (in_array($word, self::POSITIVE_WORDS, true)) {
                $weight = 1.0 * $multiplier * $exclamationBonus;
                $score += $weight;
                $totalWeight += abs($weight);
                $positiveWords[] = $word;
            } elseif (in_array($word, self::NEGATIVE_WORDS, true)) {
                $weight = 1.0 * $multiplier * $exclamationBonus;
                $score -= $weight;
                $totalWeight += abs($weight);
                $negativeWords[] = $word;
            }
        }

        // Normalize score to [-1, 1]
        if ($totalWeight > 0) {
            $score = max(-1.0, min(1.0, $score / $totalWeight));
        }

        // Determine label
        $label = 'neutre';
        if ($score > 0.15) {
            $label = 'positif';
        } elseif ($score < -0.15) {
            $label = 'negatif';
        }

        // Determine intensity
        $intensity = 'faible';
        $absScore = abs($score);
        if ($absScore > 0.5) {
            $intensity = 'fort';
        } elseif ($absScore > 0.3) {
            $intensity = 'moyen';
        }

        return [
            'score' => round($score, 4),
            'label' => $label,
            'positive_words' => array_unique($positiveWords),
            'negative_words' => array_unique($negativeWords),
            'intensity' => $intensity,
        ];
    }

    /**
     * Get a sentiment emoji based on score.
     */
    public function getSentimentEmoji(float $score): string
    {
        return match (true) {
            $score >= 0.5 => '😊',
            $score >= 0.15 => '🙂',
            $score > -0.15 => '😐',
            $score > -0.5 => '😟',
            default => '😢',
        };
    }

    /**
     * Get a human-readable sentiment label in French.
     */
    public function getSentimentLabel(string $label, string $intensity): string
    {
        return match ($label) {
            'positif' => match ($intensity) {
                'fort' => 'Très positif',
                'moyen' => 'Plutôt positif',
                default => 'Légèrement positif',
            },
            'negatif' => match ($intensity) {
                'fort' => 'Très négatif',
                'moyen' => 'Plutôt négatif',
                default => 'Légèrement négatif',
            },
            default => 'Neutre',
        };
    }
}
