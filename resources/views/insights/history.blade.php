<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📜 Historique des Insights
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back link -->
            <div class="flex justify-end mb-4">
                <a href="{{ route('insights.index') }}"
                   class="text-sm text-indigo-600 hover:underline">
                    ← Insight de la semaine
                </a>
            </div>

            @if($insights->isEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <p class="text-5xl mb-4">📭</p>
                    <p class="text-gray-400 text-lg">Aucun résumé hebdomadaire pour le moment.</p>
                    <p class="text-gray-400 text-sm mt-2">Les insights seront générés automatiquement chaque semaine.</p>
                    <a href="{{ route('insights.index') }}"
                       class="mt-6 inline-block px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                        🤖 Voir l'insight de la semaine
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($insights as $insight)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4
                        @if($insight->trend === 'up') border-green-400
                        @elseif($insight->trend === 'down') border-red-400
                        @else border-gray-400
                        @endif">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <!-- Date range -->
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">
                                    Semaine du {{ $insight->week_start->format('d/m/Y') }}
                                    au {{ $insight->week_end->format('d/m/Y') }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $insight->checkin_count }}/{{ $insight->total_days }} jours · 
                                    Humeur : <strong>{{ $insight->avg_mood }}</strong>/10
                                </p>
                            </div>

                            <!-- Mood + emotion + trend badges -->
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <p class="text-2xl">{{ $insight->dominant_emotion }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl">
                                        @if($insight->trend === 'up') 📈
                                        @elseif($insight->trend === 'down') 📉
                                        @else ➡️
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                        @if($insight->trend === 'up') bg-green-100 text-green-700
                                        @elseif($insight->trend === 'down') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-600
                                        @endif">
                                        @if($insight->trend === 'up') Hausse
                                        @elseif($insight->trend === 'down') Baisse
                                        @else Stable
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Summary (expandable) -->
                        @if($insight->summary)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $insight->summary }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
