<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📝 Analyse de sentiment NLP
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('insights.index') }}"
                   class="text-sm text-indigo-600 hover:underline">
                    ← Retour aux insights
                </a>

                <!-- Period selector -->
                <div class="flex gap-2">
                    @foreach([7, 14, 30, 90] as $d)
                    <a href="{{ route('insights.sentiment', ['days' => $d]) }}"
                       class="px-3 py-1 text-sm rounded-full transition
                              {{ $days === $d ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $d }}j
                    </a>
                    @endforeach
                </div>
            </div>

            @if($totalWithNotes === 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <p class="text-5xl mb-4">📝</p>
                    <p class="text-gray-400 text-lg">Aucune note avec analyse de sentiment.</p>
                    <p class="text-gray-400 text-sm mt-2">
                        Ajoute des notes à tes check-ins pour voir l'analyse de sentiment apparaître ici.
                    </p>
                    <a href="{{ route('checkin.create') }}"
                       class="mt-6 inline-block px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                        ✍️ Faire un check-in
                    </a>
                </div>
            @else
                <!-- Overview cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Score moyen</p>
                        <p class="text-3xl font-bold mt-1
                            {{ $avgScore > 0.15 ? 'text-green-600' : ($avgScore < -0.15 ? 'text-red-500' : 'text-gray-500') }}">
                            {{ $avgScore !== null ? number_format($avgScore, 2) : '—' }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">sur {{ $days }} jours</p>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">😊 Positifs</p>
                        <p class="text-3xl font-bold text-green-600 mt-1">{{ $distribution['positif'] ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            @php $total = array_sum($distribution); @endphp
                            {{ $total > 0 ? round(($distribution['positif'] ?? 0) / $total * 100) : 0 }}%
                        </p>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">😐 Neutres</p>
                        <p class="text-3xl font-bold text-gray-500 mt-1">{{ $distribution['neutre'] ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $total > 0 ? round(($distribution['neutre'] ?? 0) / $total * 100) : 0 }}%
                        </p>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg p-4 text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">😢 Négatifs</p>
                        <p class="text-3xl font-bold text-red-500 mt-1">{{ $distribution['negatif'] ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $total > 0 ? round(($distribution['negatif'] ?? 0) / $total * 100) : 0 }}%
                        </p>
                    </div>
                </div>

                <!-- Distribution bar -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-8">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Répartition des sentiments</h3>
                    <div class="w-full h-6 rounded-full overflow-hidden flex">
                        <div class="bg-green-400 h-full transition-all duration-500"
                             style="width: {{ $total > 0 ? ($distribution['positif'] / $total) * 100 : 0 }}%"
                             title="Positif: {{ $distribution['positif'] ?? 0 }}"></div>
                        <div class="bg-gray-300 h-full transition-all duration-500"
                             style="width: {{ $total > 0 ? ($distribution['neutre'] / $total) * 100 : 0 }}%"
                             title="Neutre: {{ $distribution['neutre'] ?? 0 }}"></div>
                        <div class="bg-red-400 h-full transition-all duration-500"
                             style="width: {{ $total > 0 ? ($distribution['negatif'] / $total) * 100 : 0 }}%"
                             title="Négatif: {{ $distribution['negatif'] ?? 0 }}"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-2">
                        <span>😊 {{ $distribution['positif'] ?? 0 }} positif(s)</span>
                        <span>😐 {{ $distribution['neutre'] ?? 0 }} neutre(s)</span>
                        <span>😢 {{ $distribution['negatif'] ?? 0 }} négatif(s)</span>
                    </div>
                </div>

                <!-- Trend chart (CSS-based bar chart) -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-8">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Évolution du sentiment</h3>

                    @if(count($chartData) > 0)
                    <div class="space-y-2">
                        @php
                            $minScore = min(array_column($chartData, 'avg_score'));
                            $maxScore = max(array_column($chartData, 'avg_score'));
                            $range = max(0.01, $maxScore - $minScore);
                        @endphp
                        @foreach(array_reverse($chartData) as $point)
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 w-24 shrink-0">{{ $point['date'] }}</span>
                            <div class="flex-1 h-7 bg-gray-100 rounded relative overflow-hidden">
                                <!-- Neutral center line -->
                                <div class="absolute left-1/2 top-0 bottom-0 w-0.5 bg-gray-300"></div>
                                <!-- Bar -->
                                <div class="absolute top-1 bottom-1 rounded transition-all duration-300
                                    {{ $point['avg_score'] > 0.15 ? 'bg-green-400' : ($point['avg_score'] < -0.15 ? 'bg-red-400' : 'bg-gray-400') }}"
                                     style="
                                        left: {{ 50 + ($point['avg_score'] / max(1, abs($maxScore), abs($minScore))) * 45 }}%;
                                        width: {{ max(4, abs($point['avg_score']) / max(1, abs($maxScore), abs($minScore))) * 40 }}%;
                                        transform: translateX(-50%);
                                     ">
                                </div>
                            </div>
                            <span class="text-xs font-medium w-12 text-right
                                {{ $point['avg_score'] > 0.15 ? 'text-green-600' : ($point['avg_score'] < -0.15 ? 'text-red-500' : 'text-gray-500') }}">
                                {{ number_format($point['avg_score'], 2) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-400 text-sm text-center py-4">Pas assez de données pour afficher le graphique.</p>
                    @endif
                </div>

                <!-- Detailed list of analyzed notes -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">
                        Notes analysées ({{ $totalWithNotes }})
                    </h3>

                    <div class="space-y-3">
                        @forelse($scores as $item)
                        <div class="p-3 border rounded-lg flex items-start gap-3
                            {{ $item['label'] === 'positif' ? 'border-green-200 bg-green-50/50' : '' }}
                            {{ $item['label'] === 'negatif' ? 'border-red-200 bg-red-50/50' : '' }}
                            {{ $item['label'] === 'neutre' ? 'border-gray-200' : '' }}">
                            <span class="text-2xl">
                                @if($item['score'] >= 0.5) 😊
                                @elseif($item['score'] >= 0.15) 🙂
                                @elseif($item['score'] > -0.15) 😐
                                @elseif($item['score'] > -0.5) 😟
                                @else 😢
                                @endif
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <span class="text-xs text-gray-500">{{ $item['date'] }}</span>
                                    <span class="text-xs font-semibold
                                        {{ $item['label'] === 'positif' ? 'text-green-600' : ($item['label'] === 'negatif' ? 'text-red-500' : 'text-gray-500') }}">
                                        {{ number_format($item['score'], 2) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 mt-1 truncate">{{ $item['notes'] }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-400 text-sm text-center py-4">Aucune note analysée.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Info box -->
                <div class="mt-6 p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <p class="text-xs text-purple-700">
                        <strong>🔍 Comment ça marche ?</strong>
                        L'analyse de sentiment utilise un lexique français de mots positifs et négatifs
                        pour évaluer le ton de tes notes de journal. Le score va de <strong>-1.00</strong> (très négatif)
                        à <strong>+1.00</strong> (très positif). Les mots d'intensité (très, vraiment…) et
                        les négations (pas, ne…) sont pris en compte pour une analyse plus précise.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
