<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\GoalMilestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    /**
     * Display a listing of all user goals.
     */
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())
            ->withCount(['milestones', 'milestones as completed_milestones_count' => function ($q) {
                $q->where('is_completed', true);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'active' => $goals->where('status', 'active')->count(),
            'completed' => $goals->where('status', 'completed')->count(),
            'total' => $goals->count(),
        ];

        return view('goals.index', [
            'goals' => $goals,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new goal.
     */
    public function create()
    {
        return view('goals.create');
    }

    /**
     * Store a newly created goal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_date' => ['nullable', 'date', 'after_or_equal:today'],
            'milestones' => ['nullable', 'array'],
            'milestones.*.title' => ['required_with:milestones.*', 'string', 'max:255'],
            'milestones.*.description' => ['nullable', 'string', 'max:1000'],
        ]);

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Create milestones if provided
        if (!empty($validated['milestones'])) {
            foreach ($validated['milestones'] as $index => $milestone) {
                if (!empty($milestone['title'])) {
                    $goal->milestones()->create([
                        'title' => $milestone['title'],
                        'description' => $milestone['description'] ?? null,
                        'order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Objectif créé avec succès ! 🎯');
    }

    /**
     * Display the specified goal with its milestones.
     */
    public function show(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $milestones = $goal->milestones()->orderBy('order')->get();

        return view('goals.show', [
            'goal' => $goal,
            'milestones' => $milestones,
            'progressPercent' => $goal->progress_percent,
        ]);
    }

    /**
     * Show the form for editing the specified goal.
     */
    public function edit(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $milestones = $goal->milestones()->orderBy('order')->get();

        return view('goals.edit', [
            'goal' => $goal,
            'milestones' => $milestones,
        ]);
    }

    /**
     * Update the specified goal.
     */
    public function update(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_date' => ['nullable', 'date'],
        ]);

        $goal->update($validated);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Objectif mis à jour ! ✏️');
    }

    /**
     * Add a milestone to the goal.
     */
    public function addMilestone(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $maxOrder = $goal->milestones()->max('order') ?? -1;

        $goal->milestones()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Jalon ajouté ! 🏁');
    }

    /**
     * Toggle a milestone's completion status.
     */
    public function toggleMilestone(Request $request, Goal $goal, GoalMilestone $milestone)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        if ($milestone->goal_id !== $goal->id) {
            abort(404);
        }

        if ($milestone->is_completed) {
            $milestone->uncomplete();
        } else {
            $milestone->complete();
        }

        // Check if all milestones are completed → auto-complete the goal
        if ($goal->milestones()->count() > 0) {
            $allDone = $goal->milestones()
                ->where('is_completed', false)
                ->count() === 0;

            if ($allDone && $goal->status === 'active') {
                $goal->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                return redirect()->route('goals.show', $goal)
                    ->with('success', '🎉 Félicitations ! Tous les jalons sont complétés !');
            }
        }

        // If a milestone was toggled off, mark goal back as active if it was completed
        if (!$milestone->is_completed && $goal->status === 'completed') {
            $goal->update([
                'status' => 'active',
                'completed_at' => null,
            ]);
        }

        return redirect()->route('goals.show', $goal)
            ->with('success', $milestone->is_completed ? '✅ Jalon complété !' : '↩️ Jalon réouvert.');
    }

    /**
     * Delete a milestone.
     */
    public function deleteMilestone(Request $request, Goal $goal, GoalMilestone $milestone)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        if ($milestone->goal_id !== $goal->id) {
            abort(404);
        }

        $milestone->delete();

        // Re-order remaining milestones
        $remaining = $goal->milestones()->orderBy('order')->get();
        foreach ($remaining as $index => $m) {
            $m->update(['order' => $index]);
        }

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Jalon supprimé.');
    }

    /**
     * Mark the goal as completed.
     */
    public function complete(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Mark all pending milestones as completed
        $goal->milestones()
            ->where('is_completed', false)
            ->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);

        return redirect()->route('goals.show', $goal)
            ->with('success', '🎉 Objectif accompli ! Félicitations !');
    }

    /**
     * Mark the goal as abandoned.
     */
    public function abandon(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->update([
            'status' => 'abandoned',
        ]);

        return redirect()->route('goals.show', $goal)
            ->with('info', 'Objectif abandonné. Tu peux le reprendre plus tard ! 💪');
    }

    /**
     * Reactivate an abandoned goal.
     */
    public function reactivate(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->update([
            'status' => 'active',
        ]);

        return redirect()->route('goals.show', $goal)
            ->with('success', 'Objectif réactivé ! 🚀');
    }

    /**
     * Remove the specified goal.
     */
    public function destroy(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->delete();

        return redirect()->route('goals.index')
            ->with('success', 'Objectif supprimé.');
    }
}
