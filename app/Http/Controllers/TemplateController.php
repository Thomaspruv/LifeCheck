<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\TemplateItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
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

    /**
     * Display a listing of the user's templates.
     */
    public function index()
    {
        $templates = Template::where('user_id', Auth::id())
            ->with('items')
            ->orderBy('id', 'desc')
            ->get();

        return view('templates.index', [
            'templates' => $templates,
        ]);
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('templates.create', [
            'dimensions' => self::DIMENSIONS,
            'inputTypes' => self::INPUT_TYPES,
        ]);
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.label' => ['required', 'string', 'max:255'],
            'items.*.input_type' => ['required', 'string', 'in:' . implode(',', array_keys(self::INPUT_TYPES))],
        ]);

        $template = Template::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'is_default' => $request->boolean('is_default'),
        ]);

        foreach ($request->items as $i => $item) {
            TemplateItem::create([
                'template_id' => $template->id,
                'label' => $item['label'],
                'input_type' => $item['input_type'],
                'position' => $i,
            ]);
        }

        return redirect()->route('templates.index')
            ->with('success', 'Template « ' . e($template->name) . ' » créé avec succès.');
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403);
        }

        $template->load('items');

        return view('templates.edit', [
            'template' => $template,
            'dimensions' => self::DIMENSIONS,
            'inputTypes' => self::INPUT_TYPES,
        ]);
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:template_items,id'],
            'items.*.label' => ['required', 'string', 'max:255'],
            'items.*.input_type' => ['required', 'string', 'in:' . implode(',', array_keys(self::INPUT_TYPES))],
        ]);

        $template->update([
            'name' => $request->name,
            'is_default' => $request->boolean('is_default'),
        ]);

        // Collect incoming item IDs to keep
        $incomingIds = collect($request->items)->pluck('id')->filter();

        // Delete removed items
        $template->items()->whereNotIn('id', $incomingIds)->delete();

        // Upsert remaining items
        foreach ($request->items as $i => $item) {
            if (!empty($item['id'])) {
                TemplateItem::where('id', $item['id'])
                    ->where('template_id', $template->id)
                    ->update([
                        'label' => $item['label'],
                        'input_type' => $item['input_type'],
                        'position' => $i,
                    ]);
            } else {
                TemplateItem::create([
                    'template_id' => $template->id,
                    'label' => $item['label'],
                    'input_type' => $item['input_type'],
                    'position' => $i,
                ]);
            }
        }

        return redirect()->route('templates.index')
            ->with('success', 'Template « ' . e($template->name) . ' » mis à jour.');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403);
        }

        $name = $template->name;
        $template->items()->delete();
        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template « ' . e($name) . ' » supprimé.');
    }

    /**
     * Set a template as the default (toggle).
     */
    public function setDefault(Template $template)
    {
        if ($template->user_id !== Auth::id()) {
            abort(403);
        }

        // Unset all others
        Template::where('user_id', Auth::id())->update(['is_default' => false]);

        // Set this one as default
        $template->update(['is_default' => true]);

        return redirect()->route('templates.index')
            ->with('success', 'Template « ' . e($template->name) . ' » défini comme défaut.');
    }
}
