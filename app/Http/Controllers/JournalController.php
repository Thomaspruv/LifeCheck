<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    /**
     * List all journal entries for the user.
     */
    public function index()
    {
        $entries = JournalEntry::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('journal.index', compact('entries'));
    }

    /**
     * Show the form to write a journal entry.
     */
    public function create(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $entry = JournalEntry::where('user_id', Auth::id())
            ->where('date', $date)
            ->first();

        return view('journal.create', [
            'entry' => $entry,
            'date' => $date,
            'isNew' => !$entry,
        ]);
    }

    /**
     * Save a new journal entry (upsert).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:10000'],
            'date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        JournalEntry::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'date' => $validated['date'],
            ],
            ['content' => $validated['content']]
        );

        return redirect()->route('journal.index')
            ->with('success', '📔 Journal enregistré !');
    }

    /**
     * Show a specific journal entry (only own).
     */
    public function show(JournalEntry $journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403);
        }

        return view('journal.show', compact('journal'));
    }

    /**
     * Edit form (only own).
     */
    public function edit(JournalEntry $journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403);
        }

        return view('journal.edit', compact('journal'));
    }

    /**
     * Update a journal entry (only own).
     */
    public function update(Request $request, JournalEntry $journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:10000'],
        ]);

        $journal->update(['content' => $validated['content']]);

        return redirect()->route('journal.index')
            ->with('success', '📔 Journal mis à jour !');
    }

    /**
     * Delete a journal entry (only own).
     */
    public function destroy(JournalEntry $journal)
    {
        if ($journal->user_id !== Auth::id()) {
            abort(403);
        }

        $journal->delete();

        return redirect()->route('journal.index')
            ->with('success', '📔 Entrée supprimée.');
    }
}
