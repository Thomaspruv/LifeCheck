<x-app-layout title="Ton profil Big Five — LifeCheck"
    seoDescription="Découvre ton profil de personnalité Big Five : Ouverture, Conscienciosité, Extraversion, Agréabilité et Névrosisme.">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🧠 Ton profil de personnalité
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Hero card --}}
            <div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-700 shadow-lg sm:rounded-2xl p-6 md:p-8 text-white">
                <div class="text-center">
                    <span class="text-6xl block mb-4">{{ $dominantIcon }}</span>
                    <h3 class="text-2xl font-bold mb-2">Ton trait dominant : {{ $dominantTrait }}</h3>
                    <p class="text-indigo-100 text-sm max-w-lg mx-auto">
                        Voici les résultats de ton questionnaire de personnalité Big Five.
                        Ces informations nous aident à personnaliser ton expérience LifeCheck.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                {{-- Radar Chart --}}
                <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-lg">📊</span>
                        <h3 class="font-semibold text-gray-800">Les 5 dimensions</h3>
                    </div>
                    <div class="relative" style="max-height: 380px;">
                        <canvas id="radarChart" class="w-full"></canvas>
                    </div>
                </div>

                {{-- Scores list --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($traits as $key => $trait)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow {{ $loop->first ? 'ring-2 ring-indigo-200' : '' }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">{{ $trait['icon'] }}</span>
                                <span class="font-semibold text-gray-800 text-sm">{{ $trait['name'] }}</span>
                            </div>
                            <span class="text-sm font-bold {{ $scores[$key] >= 60 ? 'text-green-600' : ($scores[$key] <= 35 ? 'text-orange-500' : 'text-gray-600') }}">
                                {{ $scores[$key] }}/100
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-1000"
                                 style="width: {{ $scores[$key] }}%"
                                 :class="{
                                     'bg-green-400': {{ $scores[$key] }} >= 60,
                                     'bg-indigo-400': {{ $scores[$key] }} >= 35 && {{ $scores[$key] }} < 60,
                                     'bg-orange-400': {{ $scores[$key] }} < 35
                                 }">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $trait['description'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Description block --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-lg">📝</span>
                    <h3 class="font-semibold text-gray-800">Ce que ça signifie pour toi</h3>
                </div>
                <div class="prose prose-sm max-w-none text-gray-600 space-y-2">
                    {!! nl2br(e($profileDescription)) !!}
                </div>
            </div>

            {{-- Call to action --}}
            <div class="text-center">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition-all transform hover:scale-105 text-base">
                    🚀 Accéder à mon tableau de bord
                </a>
                <p class="text-xs text-gray-400 mt-3">
                    Tu pourras retrouver ton profil à tout moment dans la section Profil.
                </p>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('radarChart');
            if (!canvas) return;

            new Chart(canvas, {
                type: 'radar',
                data: {
                    labels: [
                        '{{ $traits["openness"]["name"] }}',
                        '{{ $traits["conscientiousness"]["name"] }}',
                        '{{ $traits["extraversion"]["name"] }}',
                        '{{ $traits["agreeableness"]["name"] }}',
                        '{{ $traits["neuroticism"]["name"] }}',
                    ],
                    datasets: [{
                        label: 'Ton profil',
                        data: [
                            {{ $scores['openness'] }},
                            {{ $scores['conscientiousness'] }},
                            {{ $scores['extraversion'] }},
                            {{ $scores['agreeableness'] }},
                            {{ $scores['neuroticism'] }},
                        ],
                        backgroundColor: 'rgba(99, 102, 241, 0.15)',
                        borderColor: 'rgba(99, 102, 241, 0.8)',
                        borderWidth: 2.5,
                        pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.r + '/100';
                                }
                            }
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 25,
                                backdropColor: 'transparent',
                                font: { size: 10 }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.06)',
                            },
                            angleLines: {
                                color: 'rgba(0,0,0,0.06)',
                            },
                            pointLabels: {
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                },
                                color: '#374151'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
