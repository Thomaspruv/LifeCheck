<?php

namespace App\Http\Controllers;

use App\Models\BreathingExercise;
use App\Models\MeditationSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BreathingController extends Controller
{
    /**
     * Display a listing of breathing exercises & meditations.
     */
    public function index()
    {
        // Seed defaults if none exist
        if (BreathingExercise::count() === 0) {
            BreathingExercise::seedDefaults();
        }

        $exercises = BreathingExercise::where(function ($q) {
            $q->where('is_default', true)
              ->orWhere('created_by', Auth::id());
        })->orderBy('category')->get();

        $categories = $exercises->groupBy('category');

        return view('breathing.index', [
            'exercises' => $exercises,
            'categories' => $categories,
        ]);
    }

    /**
     * Show a specific exercise with its timer/guide.
     */
    public function show(BreathingExercise $exercise)
    {
        return view('breathing.show', [
            'exercise' => $exercise,
        ]);
    }

    /**
     * Log a completed session.
     */
    public function complete(Request $request)
    {
        $validated = $request->validate([
            'exercise_id' => ['required', 'exists:breathing_exercises,id'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
            'completed' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $exercise = BreathingExercise::findOrFail($validated['exercise_id']);

        MeditationSession::create([
            'user_id' => Auth::id(),
            'exercise_id' => $exercise->id,
            'exercise_name' => $exercise->name,
            'type' => $exercise->type,
            'duration_seconds' => $validated['duration_seconds'],
            'completed' => $validated['completed'] ?? true,
            'completed_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('breathing.index')
            ->with('success', "🧘 Séance «{$exercise->name}» terminée ! "
                . gmdate('i:s', $validated['duration_seconds']) . ' — bien joué !');
    }

    /**
     * Show the user's meditation/breathing history.
     */
    public function history()
    {
        $sessions = MeditationSession::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => MeditationSession::where('user_id', Auth::id())->count(),
            'total_minutes' => MeditationSession::where('user_id', Auth::id())
                ->sum('duration_seconds') / 60,
            'this_week' => MeditationSession::where('user_id', Auth::id())
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'streak' => $this->calculateStreak(),
        ];

        return view('breathing.history', [
            'sessions' => $sessions,
            'stats' => $stats,
        ]);
    }

    /**
     * Calculate consecutive days with at least one session.
     */
    public static function calculateStreakForUser(int $userId): int
    {
        $dates = MeditationSession::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as session_date')
            ->distinct()
            ->orderBy('session_date', 'desc')
            ->pluck('session_date');

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $expected = now()->startOfDay();

        foreach ($dates as $date) {
            $date = \Carbon\Carbon::parse($date);
            if ($date->diffInDays($expected) === 0) {
                $streak++;
                $expected = $expected->subDay();
            } elseif ($date->diffInDays($expected) === 1) {
                $streak++;
                $expected = $date->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Calculate consecutive days with at least one session (current user).
     */
    private function calculateStreak(): int
    {
        return self::calculateStreakForUser(Auth::id());
    }
}
