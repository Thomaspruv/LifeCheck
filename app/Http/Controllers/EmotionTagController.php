<?php

namespace App\Http\Controllers;

use App\Models\EmotionTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmotionTagController extends Controller
{
    public function index(): View
    {
        $tags = EmotionTag::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('tags.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'icon' => ['required', 'string', 'max:10'],
        ]);

        // Vérifier unicité par utilisateur
        $exists = EmotionTag::where('user_id', Auth::id())
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Tu as déjà un tag avec ce nom.']);
        }

        EmotionTag::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'color' => $validated['color'],
            'icon' => $validated['icon'],
        ]);

        return redirect()->route('tags.index')
            ->with('success', "🏷️ Tag « {$validated['name']} » créé !");
    }

    public function edit(EmotionTag $tag): View
    {
        $this->authorizeAccess($tag);

        return view('tags.edit', compact('tag'));
    }

    public function update(Request $request, EmotionTag $tag): RedirectResponse
    {
        $this->authorizeAccess($tag);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'color' => ['required', 'string', 'max:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'icon' => ['required', 'string', 'max:10'],
        ]);

        // Vérifier unicité (sauf le même tag)
        $exists = EmotionTag::where('user_id', Auth::id())
            ->where('name', $validated['name'])
            ->where('id', '!=', $tag->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'Un autre tag porte déjà ce nom.']);
        }

        $tag->update($validated);

        return redirect()->route('tags.index')
            ->with('success', "🏷️ Tag « {$validated['name']} » mis à jour !");
    }

    public function destroy(EmotionTag $tag): RedirectResponse
    {
        $this->authorizeAccess($tag);

        $name = $tag->name;
        $tag->delete();

        return redirect()->route('tags.index')
            ->with('success', "🗑️ Tag « {$name} » supprimé.");
    }

    private function authorizeAccess(EmotionTag $tag): void
    {
        if ($tag->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
