<x-app-layout title="Exercices de respiration & méditation — LifeCheck"
    seoDescription="Retrouve le calme avec nos exercices de respiration et méditation guidée. Box breathing, cohérence cardiaque, méditation pleine conscience et plus.">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🧘 Respiration & Méditation
            </h2>
            <a href="{{ route('breathing.history') }}"
               class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                📊 Mon historique
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($exercises->isEmpty())
                <div class="text-center py-16">
                    <p class="text-6xl mb-4">🧘</p>
                    <p class="text-lg text-gray-500">Aucun exercice disponible.</p>
                </div>
            @else
                @foreach ($categories as $category => $items)
                    <div class="mb-10">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center gap-2">
                            <span>📂</span> {{ $category }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach ($items as $exercise)
                                <a href="{{ route('breathing.show', $exercise) }}"
                                   class="group bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-indigo-200 transition-all block">
                                    <div class="flex items-center gap-3 mb-3">
                                        <span class="text-3xl">{{ $exercise->icon }}</span>
                                        <div>
                                            <h4 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition-colors">
                                                {{ $exercise->name }}
                                            </h4>
                                            <span class="text-xs font-medium text-gray-400">{{ $exercise->type === 'breathing' ? 'Respiration' : 'Méditation' }}</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 line-clamp-2 mb-3">
                                        {{ $exercise->description }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex gap-1">
                                            @foreach ($exercise->duration_options ?? [] as $min)
                                                <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full font-medium">
                                                    {{ $min }}min
                                                </span>
                                            @endforeach
                                        </div>
                                        <span class="text-xs text-indigo-500 font-medium group-hover:underline">
                                            Commencer →
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Daily Tip -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-2xl p-6 mt-6">
                <div class="flex items-start gap-4">
                    <span class="text-3xl shrink-0">💡</span>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">Le savais-tu ?</h3>
                        <p class="text-sm text-gray-600">
                            Seulement <strong>5 minutes</strong> de respiration profonde par jour peuvent réduire
                            significativement le stress et améliorer ta concentration. Essaye la cohérence cardiaque
                            pendant une pause café !
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
