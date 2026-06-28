<?php

namespace App\Http\Controllers;

use App\Models\PersonalChallenge;
use App\Services\ChallengeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    public function __construct(
        private readonly ChallengeService $challengeService
    ) {}

    /**
     * Display a listing of all user challenges.
     */
    public function index()
    {
        $challenges = $this->challengeService->getUserChallenges();

        $stats = [
            'active' => $challenges->where('status', 'active')->count(),
            'completed' => $challenges->where('status', 'completed')->count(),
            'failed' => $challenges->where('status', 'failed')->count(),
            'total' => $challenges->count(),
        ];

        return view('challenges.index', [
            'challenges' => $challenges,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new challenge.
     */
    public function create()
    {
        return view('challenges.create');
    }

    /**
     * Store a newly created challenge.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $challenge = $this->challengeService->createChallenge($validated);

        return redirect()->route('challenges.show', $challenge)
            ->with('success', 'Défi créé avec succès ! 🎯');
    }

    /**
     * Display the specified challenge.
     */
    public function show(Request $request, PersonalChallenge $challenge)
    {
        if ($challenge->user_id !== Auth::id()) {
            abort(403);
        }

        $year = (int) ($request->input('year', now()->year));
        $month = (int) ($request->input('month', now()->month));

        $calendar = $this->challengeService->getCalendarData($challenge, $year, $month);
        $progressPercent = $challenge->progress_percent;
        $currentStreak = $challenge->current_streak;

        // Navigation for month selector
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        $monthNames = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        return view('challenges.show', [
            'challenge' => $challenge,
            'calendar' => $calendar,
            'progressPercent' => $progressPercent,
            'currentStreak' => $currentStreak,
            'year' => $year,
            'month' => $month,
            'monthName' => $monthNames[$month],
            'prevYear' => $prevYear,
            'prevMonth' => $prevMonth,
            'nextYear' => $nextYear,
            'nextMonth' => $nextMonth,
        ]);
    }

    /**
     * Log today's progress for the challenge.
     */
    public function progress(Request $request, PersonalChallenge $challenge)
    {
        if ($challenge->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $this->challengeService->logProgress(
            $challenge,
            $validated['date'] ?? null,
            true,
            $validated['note'] ?? null,
        );

        return redirect()->route('challenges.show', $challenge)
            ->with('success', 'Progression enregistrée ! ✅');
    }

    /**
     * Pause the challenge.
     */
    public function pause(PersonalChallenge $challenge)
    {
        if ($challenge->user_id !== Auth::id()) {
            abort(403);
        }

        $this->challengeService->pauseChallenge($challenge);

        return redirect()->route('challenges.show', $challenge)
            ->with('success', 'Défi mis en pause. ⏸️');
    }

    /**
     * Resume the challenge.
     */
    public function resume(PersonalChallenge $challenge)
    {
        if ($challenge->user_id !== Auth::id()) {
            abort(403);
        }

        $this->challengeService->resumeChallenge($challenge);

        return redirect()->route('challenges.show', $challenge)
            ->with('success', 'Défi repris ! 🚀');
    }

    /**
     * Mark the challenge as failed.
     */
    public function fail(PersonalChallenge $challenge)
    {
        if ($challenge->user_id !== Auth::id()) {
            abort(403);
        }

        $this->challengeService->failChallenge($challenge);

        return redirect()->route('challenges.show', $challenge)
            ->with('info', 'Défi marqué comme échoué. Vous pouvez recommencer ! 💪');
    }

    /**
     * Remove the challenge.
     */
    public function destroy(PersonalChallenge $challenge)
    {
        if ($challenge->user_id !== Auth::id()) {
            abort(403);
        }

        $this->challengeService->deleteChallenge($challenge);

        return redirect()->route('challenges.index')
            ->with('success', 'Défi supprimé.');
    }
}
