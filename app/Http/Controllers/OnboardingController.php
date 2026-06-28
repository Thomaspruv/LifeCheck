<?php

namespace App\Http\Controllers;

use App\Models\PersonalityTrait;
use App\Models\Template;
use App\Models\TemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    const DIMENSIONS = [
        'humeur' => 'Humeur',
        'energie' => 'Énergie',
        'productivite' => 'Productivité',
        'sante' => 'Santé',
        'gratitude' => 'Gratitude',
        'intentions' => 'Intentions',
        'texte_libre' => 'Texte libre',
    ];

    const INPUT_TYPES = [
        'slider' => 'Slider (1-10)',
        'emoji' => 'Émoji',
        'text' => 'Texte libre',
        'checkbox' => 'Cases à cocher',
    ];

    // Big Five (OCEAN) questions — 3 per trait, scale 1-5
    const BIG_FIVE_QUESTIONS = [
        'openness' => [
            'name' => 'Ouverture',
            'description' => 'Curiosité intellectuelle, créativité, goût pour la nouveauté',
            'icon' => '🎨',
            'questions' => [
                ['text' => 'J\'aime explorer de nouvelles idées et concepts.', 'reversed' => false],
                ['text' => 'Je suis attiré(e) par l\'art, la culture et l\'imagination.', 'reversed' => false],
                ['text' => 'Je préfère les situations familières aux découvertes.', 'reversed' => true],
            ],
        ],
        'conscientiousness' => [
            'name' => 'Conscienciosité',
            'description' => 'Organisation, discipline, fiabilité',
            'icon' => '📋',
            'questions' => [
                ['text' => 'Je suis organisé(e) et méthodique dans ce que je fais.', 'reversed' => false],
                ['text' => 'Je termine toujours ce que je commence.', 'reversed' => false],
                ['text' => 'J\'ai tendance à être négligent(e) ou désorganisé(e).', 'reversed' => true],
            ],
        ],
        'extraversion' => [
            'name' => 'Extraversion',
            'description' => 'Sociabilité, énergie, enthousiasme dans les interactions',
            'icon' => '🎉',
            'questions' => [
                ['text' => 'Je suis à l\'aise dans les situations sociales et les groupes.', 'reversed' => false],
                ['text' => 'Je préfère les activités solitaires aux activités de groupe.', 'reversed' => true],
                ['text' => 'Je déborde d\'énergie quand je suis avec d\'autres personnes.', 'reversed' => false],
            ],
        ],
        'agreeableness' => [
            'name' => 'Agréabilité',
            'description' => 'Empathie, coopération, confiance envers les autres',
            'icon' => '🤝',
            'questions' => [
                ['text' => 'Je fais facilement confiance aux autres.', 'reversed' => false],
                ['text' => 'Je suis empathique et à l\'écoute des autres.', 'reversed' => false],
                ['text' => 'J\'aime débattre et remettre en question les idées des autres.', 'reversed' => true],
            ],
        ],
        'neuroticism' => [
            'name' => 'Névrosisme',
            'description' => 'Sensibilité émotionnelle, réactivité au stress',
            'icon' => '🌊',
            'questions' => [
                ['text' => 'Je suis facilement stressé(e) ou tendu(e).', 'reversed' => false],
                ['text' => 'Je ressens souvent des émotions négatives.', 'reversed' => false],
                ['text' => 'Je reste calme et serein(e) face aux difficultés.', 'reversed' => true],
            ],
        ],
    ];

    const LIKERT_LABELS = [
        1 => 'Pas du tout',
        2 => 'Plutôt pas',
        3 => 'Neutre',
        4 => 'Plutôt',
        5 => 'Tout à fait',
    ];

    public function step1()
    {
        return view('onboarding.step1', [
            'dimensions' => self::DIMENSIONS,
        ]);
    }

    public function postStep1(Request $request)
    {
        $request->validate([
            'dimensions' => ['required', 'array', 'min:1'],
            'dimensions.*' => ['string', 'in:' . implode(',', array_keys(self::DIMENSIONS))],
        ]);

        session(['onboarding.dimensions' => $request->dimensions]);

        return redirect()->route('onboarding.step2');
    }

    public function step2()
    {
        $dimensions = session('onboarding.dimensions');

        if (!$dimensions) {
            return redirect()->route('onboarding.step1');
        }

        $selectedDimensions = [];
        foreach ($dimensions as $key) {
            $selectedDimensions[$key] = self::DIMENSIONS[$key];
        }

        return view('onboarding.step2', [
            'dimensions' => $selectedDimensions,
            'inputTypes' => self::INPUT_TYPES,
        ]);
    }

    public function postStep2(Request $request)
    {
        $dimensions = session('onboarding.dimensions');

        if (!$dimensions) {
            return redirect()->route('onboarding.step1');
        }

        $rules = [];
        foreach ($dimensions as $dim) {
            $rules["input_types.$dim"] = ['required', 'string', 'in:' . implode(',', array_keys(self::INPUT_TYPES))];
        }

        $request->validate($rules);

        session(['onboarding.input_types' => $request->input_types]);

        return redirect()->route('onboarding.step3');
    }

    public function step3()
    {
        $dimensions = session('onboarding.dimensions');
        $inputTypes = session('onboarding.input_types');

        if (!$dimensions || !$inputTypes) {
            return redirect()->route('onboarding.step1');
        }

        $items = [];
        foreach ($dimensions as $dim) {
            $items[] = [
                'label' => self::DIMENSIONS[$dim],
                'input_type' => $inputTypes[$dim],
            ];
        }

        return view('onboarding.step3', [
            'items' => $items,
            'inputTypeLabels' => self::INPUT_TYPES,
        ]);
    }

    public function step4()
    {
        $dimensions = session('onboarding.dimensions');
        $inputTypes = session('onboarding.input_types');

        if (!$dimensions || !$inputTypes) {
            return redirect()->route('onboarding.step1');
        }

        return view('onboarding.step4', [
            'traits' => self::BIG_FIVE_QUESTIONS,
            'likertLabels' => self::LIKERT_LABELS,
        ]);
    }

    public function postStep4(Request $request)
    {
        $dimensions = session('onboarding.dimensions');
        $inputTypes = session('onboarding.input_types');

        if (!$dimensions || !$inputTypes) {
            return redirect()->route('onboarding.step1');
        }

        // Build validation rules for all questions
        $rules = [];
        foreach (self::BIG_FIVE_QUESTIONS as $traitKey => $trait) {
            foreach ($trait['questions'] as $i => $question) {
                $rules["answers.{$traitKey}.{$i}"] = ['required', 'integer', 'min:1', 'max:5'];
            }
        }

        $request->validate($rules);

        $answers = $request->input('answers');

        // Calculate scores (0-100) for each trait
        $scores = [];
        foreach (self::BIG_FIVE_QUESTIONS as $traitKey => $trait) {
            $total = 0;
            $count = count($trait['questions']);
            foreach ($trait['questions'] as $i => $question) {
                $value = (int) $answers[$traitKey][$i];
                if ($question['reversed']) {
                    $value = 6 - $value; // Reverse: 1→5, 2→4, 3→3, 4→2, 5→1
                }
                $total += $value;
            }
            // Convert to 0-100 scale (min = $count, max = $count * 5)
            $min = $count;
            $max = $count * 5;
            $scores[$traitKey] = $max > $min
                ? (int) round(($total - $min) / ($max - $min) * 100)
                : 50;
        }

        session([
            'onboarding.big_five_answers' => $answers,
            'onboarding.big_five_scores' => $scores,
        ]);

        return redirect()->route('onboarding.store');
    }

    public function store()
    {
        $dimensions = session('onboarding.dimensions');
        $inputTypes = session('onboarding.input_types');

        if (!$dimensions || !$inputTypes) {
            return redirect()->route('onboarding.step1');
        }

        $template = Template::create([
            'user_id' => Auth::id(),
            'name' => 'Mon template',
            'is_default' => true,
        ]);

        foreach ($dimensions as $i => $dim) {
            TemplateItem::create([
                'template_id' => $template->id,
                'label' => self::DIMENSIONS[$dim],
                'input_type' => $inputTypes[$dim],
                'position' => $i,
            ]);
        }

        // Save personality traits if completed
        $scores = session('onboarding.big_five_scores');
        $answers = session('onboarding.big_five_answers');

        if ($scores && $answers) {
            PersonalityTrait::create([
                'user_id' => Auth::id(),
                'openness' => $scores['openness'] ?? null,
                'conscientiousness' => $scores['conscientiousness'] ?? null,
                'extraversion' => $scores['extraversion'] ?? null,
                'agreeableness' => $scores['agreeableness'] ?? null,
                'neuroticism' => $scores['neuroticism'] ?? null,
                'answers' => $answers,
            ]);
        }

        session()->forget([
            'onboarding.dimensions',
            'onboarding.input_types',
            'onboarding.big_five_answers',
            'onboarding.big_five_scores',
        ]);

        return redirect()->route('personality.results')->with('success', 'Bienvenue ! Ton profil de personnalité a été enregistré.');
    }

    /**
     * Show the Big Five personality results.
     */
    public function results()
    {
        $personality = PersonalityTrait::where('user_id', Auth::id())->first();

        if (!$personality) {
            return redirect()->route('dashboard')->with('error', 'Aucun profil de personnalité trouvé.');
        }

        $scores = [
            'openness' => $personality->openness,
            'conscientiousness' => $personality->conscientiousness,
            'extraversion' => $personality->extraversion,
            'agreeableness' => $personality->agreeableness,
            'neuroticism' => $personality->neuroticism,
        ];

        $traits = self::BIG_FIVE_QUESTIONS;
        $dominantTrait = $personality->dominantTrait();

        $dominantIcons = [
            'Ouverture' => '🎨',
            'Conscienciosité' => '📋',
            'Extraversion' => '🎉',
            'Agréabilité' => '🤝',
            'Névrosisme' => '🌊',
        ];

        $dominantIcon = $dominantIcons[$dominantTrait] ?? '🧠';
        $profileDescription = $personality->getProfileDescription();

        return view('onboarding.results', compact('scores', 'traits', 'dominantTrait', 'dominantIcon', 'profileDescription'));
    }
}
