<x-app-layout title="Mes tags — LifeCheck"
    seoDescription="Gère tes tags personnalisés pour catégoriser tes émotions lors des check-ins.">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('🏷️ Mes tags émotionnels') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Create Button -->
            <div class="mb-6 flex justify-end">
                <a href="{{ route('tags.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
                    + Nouveau tag
                </a>
            </div>

            <!-- Tags Grid -->
            @if($tags->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($tags as $tag)
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all"
                             style="border-left: 4px solid {{ $tag->color }};">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="text-3xl">{{ $tag->icon }}</span>
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $tag->name }}</h3>
                                        <span class="inline-block mt-1 w-6 h-1 rounded-full"
                                              style="background-color: {{ $tag->color }};"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center gap-2 text-sm">
                                <a href="{{ route('tags.edit', $tag) }}"
                                   class="text-indigo-600 hover:underline flex items-center gap-1">
                                    ✏️ Modifier
                                </a>
                                <span class="text-gray-300">·</span>
                                <form method="POST" action="{{ route('tags.destroy', $tag) }}"
                                      onsubmit="return confirm('Supprimer le tag « {{ $tag->name }} » ?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline flex items-center gap-1">
                                        🗑️ Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <p class="text-5xl mb-4">🏷️</p>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucun tag pour le moment</h3>
                    <p class="text-gray-400 text-sm mb-6">
                        Crée des tags pour catégoriser tes émotions et retrouver facilement tes humeurs.
                    </p>
                    <a href="{{ route('tags.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
                        + Créer mon premier tag
                    </a>
                </div>
            @endif

            <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-start gap-3">
                    <span class="text-2xl">💡</span>
                    <div>
                        <h4 class="font-medium text-gray-700 text-sm">À quoi servent les tags ?</h4>
                        <p class="text-gray-400 text-xs mt-1">
                            Les tags te permettent de catégoriser tes émotions par thème : travail, famille, santé,
                            loisirs… Tu peux les associer à chaque check-in et retrouver l'évolution de chaque dimension.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
