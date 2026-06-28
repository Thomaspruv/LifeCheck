<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🤖 Insight Hebdo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation sub-links -->
            <div class="flex justify-end mb-4 space-x-4">
                <a href="{{ route('insights.j7') }}"
                   class="text-sm text-indigo-600 hover:underline">
                    📊 Comparaison J-7 →
                </a>
                <a href="{{ route('insights.history') }}"
                   class="text-sm text-indigo-600 hover:underline">
                    📜 Voir l'historique →
                </a>
            </div>

            @if($checkinCount === 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <p class="text-5xl mb-4">📭</p>
                    <p class="text-gray-400 text-lg">Aucun check-in cette semaine.</p>
                    <p class="text-gray-400 text-sm mt-2">Commence à noter ton humeur pour obtenir des insights personnalisés.</p>
                    <a href="{{ route('checkin.create') }}"
                       class="mt-6 inline-block px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                        ✍️ Faire un check-in
                    </a>
                </div>
            @else
                <!-- Week header -->
                <div class="text-center mb-6">
                    <p class="text-sm text-gray-500">
                        Semaine du <strong>{{ $weekStart->format('d/m/Y') }}</strong>
                        au <strong>{{ $weekEnd->format('d/m/Y') }}</strong>
                    </p>
                </div>

                <!-- Main insight card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Average Mood -->
                        <div class="text-center p-4 bg-indigo-50 rounded-xl">
                            <p class="text-sm text-gray-500 uppercase tracking-wide font-semibold mb-2">Humeur moyenne</p>
                            <p class="text-5xl font-bold text-indigo-600">{{ $avgMood ?? '—' }}</p>
                            <p class="text-sm text-gray-400 mt-1">/ 10</p>
                        </div>

                        <!-- Dominant Emotion -->
                        <div class="text-center p-4 bg-yellow-50 rounded-xl">
                            <p class="text-sm text-gray-500 uppercase tracking-wide font-semibold mb-2">Émotion dominante</p>
                            <p class="text-5xl">{{ $dominantEmotion }}</p>
                            <p class="text-sm text-gray-400 mt-1">
                                @php
                                    $name = match ($dominantEmotion) {
                                        '😢' => 'Triste',
                                        '😟' => 'Inquiet',
                                        '😐' => 'Neutre',
                                        '🙂' => 'Content',
                                        '😊' => 'Joyeux',
                                        '😄' => 'Très joyeux',
                                        '😁' => 'Radieux',
                                        '🥳' => 'En fête',
                                        default => $dominantEmotion,
                                    };
                                @endphp
                                {{ $name }}
                            </p>
                        </div>

                        <!-- Trend -->
                        <div class="text-center p-4
                            @if($trend === 'up') bg-green-50
                            @elseif($trend === 'down') bg-red-50
                            @else bg-gray-50
                            @endif rounded-xl">
                            <p class="text-sm text-gray-500 uppercase tracking-wide font-semibold mb-2">Tendance</p>
                            <p class="text-5xl">
                                @if($trend === 'up') 📈
                                @elseif($trend === 'down') 📉
                                @else ➡️
                                @endif
                            </p>
                            <p class="text-sm mt-1
                                @if($trend === 'up') text-green-600
                                @elseif($trend === 'down') text-red-600
                                @else text-gray-500
                                @endif font-medium">
                                @if($trend === 'up') Hausse
                                @elseif($trend === 'down') Baisse
                                @else Stable
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Check-in progress bar -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Progression des check-ins</span>
                            <span class="text-sm font-semibold text-gray-600">{{ $checkinCount }}/{{ $totalDays }} jours</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-indigo-500 h-3 rounded-full transition-all duration-500"
                                 style="width: {{ ($checkinCount / $totalDays) * 100 }}%"></div>
                        </div>
                    </div>

                    <!-- Summary -->
                    @if($summary)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $summary }}</p>
                    </div>
                    @endif

                    <!-- Sentiment Analysis Section -->
                    @if($notesWithSentiment > 0)
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">📝 Analyse de sentiment des notes</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Average sentiment -->
                            <div class="text-center p-3 bg-purple-50 rounded-xl">
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Sentiment moyen</p>
                                <p class="text-3xl font-bold {{ $avgSentiment > 0 ? 'text-green-600' : ($avgSentiment < 0 ? 'text-red-500' : 'text-gray-500') }}">
                                    {{ $avgSentiment !== null ? number_format($avgSentiment, 2) : '—' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">de -1.00 à +1.00</p>
                            </div>

                            <!-- Notes with sentiment count -->
                            <div class="text-center p-3 bg-indigo-50 rounded-xl">
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Notes analysées</p>
                                <p class="text-3xl font-bold text-indigo-600">{{ $notesWithSentiment }}</p>
                                <p class="text-xs text-gray-400 mt-1">cette semaine</p>
                            </div>

                            <!-- Distribution -->
                            <div class="text-center p-3 bg-gray-50 rounded-xl">
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-1">Répartition</p>
                                <div class="flex justify-center gap-3 mt-1">
                                    <span title="Positif" class="flex items-center gap-1">
                                        <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>
                                        <span class="text-sm font-medium text-green-700">{{ $sentimentDistribution['positif'] ?? 0 }}</span>
                                    </span>
                                    <span title="Neutre" class="flex items-center gap-1">
                                        <span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span>
                                        <span class="text-sm font-medium text-gray-600">{{ $sentimentDistribution['neutre'] ?? 0 }}</span>
                                    </span>
                                    <span title="Négatif" class="flex items-center gap-1">
                                        <span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>
                                        <span class="text-sm font-medium text-red-600">{{ $sentimentDistribution['negatif'] ?? 0 }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('insights.sentiment') }}"
                               class="text-sm text-purple-600 hover:underline font-medium">
                                📊 Voir l'analyse détaillée →
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
