<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        $template = Template::where('user_id', $user->id)
            ->where('is_default', true)
            ->with('items')
            ->first();

        if (!$template) {
            return redirect()->route('onboarding.step1')
                ->with('error', 'Crée d\'abord ton template de check-in.');
        }

        // Check if already done today
        $today = now()->toDateString();
        $existing = CheckIn::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return redirect()->route('dashboard')
                ->with('success', '✅ Check-in déjà fait aujourd\'hui !');
        }

        return view('checkin.create', [
            'template' => $template,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $template = Template::where('user_id', $user->id)
            ->where('is_default', true)
            ->with('items')
            ->firstOrFail();

        // Check duplicate
        $exists = CheckIn::where('user_id', $user->id)->where('date', $today)->exists();
        if ($exists) {
            return redirect()->route('dashboard')->with('error', 'Tu as déjà fait ton check-in aujourd\'hui.');
        }

        $items = $template->items;
        $rules = [];
        foreach ($items as $item) {
            $rules["value_{$item->id}"] = match ($item->input_type) {
                'slider' => ['required', 'integer', 'min:1', 'max:10'],
                'checkbox' => ['nullable', 'array'],
                'text' => ['nullable', 'string', 'max:1000'],
                'emoji' => ['required', 'string', 'max:10'],
                default => ['nullable', 'string'],
            };
        }

        $validated = $request->validate($rules);

        $checkin = CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'date' => $today,
            'notes' => $request->notes,
        ]);

        foreach ($items as $item) {
            $value = $validated["value_{$item->id}"] ?? '';
            if (is_array($value)) {
                $value = json_encode($value);
            }
            $checkin->items()->create([
                'template_item_id' => $item->id,
                'value' => (string) $value,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', '✅ Check-in enregistré !');
    }
}
