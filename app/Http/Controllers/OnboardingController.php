<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
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

        session()->forget(['onboarding.dimensions', 'onboarding.input_types']);

        return redirect()->route('dashboard')->with('success', 'Bienvenue ! Votre template est prêt.');
    }
}
