<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\EmotionTag;
use App\Models\Template;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    public function __construct(
        private readonly StreakService $streakService
    ) {}

    /**
     * Show the check-in form for today, or a specific catch-up date.
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        $template = Template::where('user_id', $user->id)
            ->where('is_default', true)
            ->with('items')
            ->first();

        if (!$template) {
            return redirect()->route('onboarding.step1')
                ->with('error', "Crée d'abord ton template de check-in.");
        }

        $targetDate = $request->query('date', now()->toDateString());

        // Check if already done for this date
        $existing = CheckIn::where('user_id', $user->id)
            ->where('date', $targetDate)
            ->first();

        if ($existing) {
            if ($targetDate === now()->toDateString()) {
                return redirect()->route('dashboard')
                    ->with('success', '✅ Check-in déjà fait aujourd\'hui !');
            }
            return redirect()->route('checkin.catch-up')
                ->with('info', "Tu as déjà fait ton check-in pour le {$targetDate}.");
        }

        $isCatchUp = $targetDate !== now()->toDateString();

        return view('checkin.create', [
            'template' => $template,
            'tags' => EmotionTag::where('user_id', $user->id)->orderBy('name')->get(),
            'targetDate' => $targetDate,
            'isCatchUp' => $isCatchUp,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $targetDate = $request->input('date', now()->toDateString());

        $template = Template::where('user_id', $user->id)
            ->where('is_default', true)
            ->with('items')
            ->firstOrFail();

        // Check duplicate for the target date
        $exists = CheckIn::where('user_id', $user->id)
            ->where('date', $targetDate)
            ->exists();
        if ($exists) {
            if ($targetDate === now()->toDateString()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Tu as déjà fait ton check-in aujourd\'hui.');
            }
            return redirect()->route('checkin.catch-up')
                ->with('error', "Tu as déjà fait un check-in pour le {$targetDate}.");
        }

        $items = $template->items;
        $rules = [
            'date' => ['nullable', 'date', 'before_or_equal:today'],
        ];
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

        // Validate tags if any
        $tagIds = [];
        if ($request->has('tags')) {
            $tagIds = $request->validate([
                'tags' => ['nullable', 'array'],
                'tags.*' => ['exists:emotion_tags,id'],
            ])['tags'] ?? [];

            // Ensure tags belong to the user
            $userTagIds = EmotionTag::where('user_id', $user->id)
                ->whereIn('id', $tagIds)
                ->pluck('id')
                ->toArray();
        }

        $checkin = CheckIn::create([
            'user_id' => $user->id,
            'template_id' => $template->id,
            'date' => $targetDate,
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

        // Attach tags
        if (!empty($userTagIds)) {
            $checkin->emotionTags()->attach($userTagIds);
        }

        // Award badges after catch-up too
        $this->streakService->checkAndAwardBadges($user->id);

        $message = $targetDate === now()->toDateString()
            ? '✅ Check-in enregistré !'
            : "✅ Check-in du {$targetDate} enregistré !";

        return redirect()->route('dashboard')
            ->with('success', $message);
    }

    /**
     * Show missed days available for catch-up check-in.
     */
    public function catchUp()
    {
        $user = Auth::user();

        $template = Template::where('user_id', $user->id)
            ->where('is_default', true)
            ->first();

        if (!$template) {
            return redirect()->route('onboarding.step1')
                ->with('error', "Crée d'abord ton template de check-in.");
        }

        // Find dates in the last 7 days (excluding today) that have no check-in
        $missedDates = [];
        $today = now()->toDateString();

        for ($i = 1; $i <= 7; $i++) {
            $candidate = now()->subDays($i)->toDateString();
            $exists = CheckIn::where('user_id', $user->id)
                ->where('date', $candidate)
                ->exists();

            if (!$exists) {
                $missedDates[] = $candidate;
            }
        }

        if (empty($missedDates)) {
            return redirect()->route('dashboard')
                ->with('success', '✅ Aucun jour à rattraper — tu es à jour !');
        }

        // Get today's date status
        $todayDone = CheckIn::where('user_id', $user->id)
            ->where('date', $today)
            ->exists();

        return view('checkin.catch-up', [
            'missedDates' => $missedDates,
            'todayDone' => $todayDone,
        ]);
    }
}
