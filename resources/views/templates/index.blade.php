<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes templates') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <p class="text-sm text-gray-500">Gère tes templates de check-in</p>
                <a href="{{ route('templates.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                    + Nouveau template
                </a>
            </div>

            @if ($templates->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center text-gray-500">
                        <p class="text-4xl mb-4">📋</p>
                        <p class="text-lg font-medium mb-2">Aucun template pour le moment</p>
                        <p class="text-sm mb-6">Crée ton premier template de check-in</p>
                        <a href="{{ route('templates.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            Créer un template
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($templates as $template)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                                            @if ($template->is_default)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    Défaut
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 mb-3">{{ $template->items->count() }} dimension(s)</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($template->items as $item)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                                    {{ $item->label }}
                                                    <span class="ml-1 text-gray-400">·</span>
                                                    <span class="ml-1 text-gray-500">{{ $item->input_type }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-4">
                                        @if (!$template->is_default)
                                            <form method="POST" action="{{ route('templates.setDefault', $template) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                                    Définir défaut
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('templates.edit', $template) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                            Modifier
                                        </a>

                                        <!-- Delete button triggers modal -->
                                        <button type="button"
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'delete-template-{{ $template->id }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                                            Supprimer
                                        </button>

                                        <!-- Delete confirmation modal -->
                                        <x-modal name="delete-template-{{ $template->id }}" focusable>
                                            <div class="p-6">
                                                <h2 class="text-lg font-medium text-gray-900">
                                                    Supprimer le template ?
                                                </h2>
                                                <p class="mt-2 text-sm text-gray-600">
                                                    Es-tu sûr de vouloir supprimer « {{ $template->name }} » ? Cette action est irréversible.
                                                </p>
                                                <div class="mt-6 flex justify-end gap-3">
                                                    <x-secondary-button x-on:click="$dispatch('close')">
                                                        Annuler
                                                    </x-secondary-button>
                                                    <form method="POST" action="{{ route('templates.destroy', $template) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <x-danger-button type="submit">
                                                            Supprimer
                                                        </x-danger-button>
                                                    </form>
                                                </div>
                                            </div>
                                        </x-modal>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
