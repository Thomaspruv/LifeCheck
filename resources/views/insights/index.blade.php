<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🤖 Insight Hebdo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation sub-links -->
            <div class="flex justify-end mb-4">
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
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
