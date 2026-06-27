<x-app-layout title="Comparaison J-7 — LifeCheck"
    seoDescription="Compare ton humeur et ton énergie jour par jour avec la même semaine il y a 7 jours.">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 Comparaison glissante J-7
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation links -->
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('insights.index') }}"
                   class="text-sm text-indigo-600 hover:underline">
                    ← Retour aux insights
                </a>
                <div class="flex gap-3">
                    <a href="{{ route('insights.history') }}"
                       class="text-sm text-indigo-600 hover:underline">
                        📜 Historique
                    </a>
                    <a href="{{ route('trends') }}"
                       class="text-sm text-indigo-600 hover:underline">
                        📈 Tendances
                    </a>
                </div>
            </div>

            <!-- Period header -->
            <div class="text-center mb-8">
                <p class="text-sm text-gray-500">
                    Comparaison des <strong>7 derniers jours</strong>
                    (<span class="font-medium">{{ $thisPeriodStart->format('d/m') }}</span>
                    au <span class="font-medium">{{ $thisPeriodEnd->format('d/m/Y') }}</span>)
                    vs la <strong>semaine précédente</strong>
                    (<span class="font-medium">{{ $prevPeriodStart->format('d/m') }}</span>
                    au <span class="font-medium">{{ $prevPeriodEnd->format('d/m/Y') }}</span>)
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    Chaque jour est comparé au même jour de la semaine précédente (J-7).
                </p>
            </div>

            @if(empty($overallStats))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <p class="text-5xl mb-4">📭</p>
                    <p class="text-gray-400 text-lg">Pas assez de données pour la comparaison.</p>
                    <p class="text-gray-400 text-sm mt-2">Continue tes check-ins quotidiens pour voir apparaître la comparaison J-7.</p>
                    <a href="{{ route('checkin.create') }}"
                       class="mt-6 inline-block px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                        ✍️ Faire un check-in
                    </a>
                </div>
            @else
                <!-- Summary cards -->
                <div class="grid grid-cols-1 md:grid-cols-{{ min(count($overallStats) + 1, 4) }} gap-4 mb-8">
                    @php $statsCount = count($overallStats); @endphp
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-md p-5 text-white">
                        <p class="text-sm text-indigo-100 uppercase tracking-wide font-semibold mb-1">Période</p>
                        <p class="text-lg font-bold">{{ $thisPeriodStart->format('d/m') }} — {{ $thisPeriodEnd->format('d/m') }}</p>
                        <p class="text-xs text-indigo-200 mt-1">J-7 vs même jour précédent</p>
                    </div>

                    @foreach($overallStats as $label => $stats)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <p class="text-sm text-gray-500 uppercase tracking-wide font-semibold mb-2">{{ $label }}</p>
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-center">
                                <p class="text-xs text-gray-400">Cette semaine</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['avg_this'] ?? '—' }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-400">Semaine J-7</p>
                                <p class="text-2xl font-bold text-gray-500">{{ $stats['avg_prev'] ?? '—' }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-400">Écart</p>
                                <p class="text-2xl font-bold
                                    @if($stats['trend'] === 'up') text-green-600
                                    @elseif($stats['trend'] === 'down') text-red-600
                                    @else text-gray-500
                                    @endif">
                                    {{ $stats['diff'] !== null ? ($stats['diff'] > 0 ? '+' : '') . $stats['diff'] : '—' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400">
                            <span>{{ $stats['days_this'] }}/7 jours</span>
                            <span>{{ $stats['days_prev'] }}/7 jours</span>
                            <span>
                                @if($stats['trend'] === 'up') 📈 Hausse
                                @elseif($stats['trend'] === 'down') 📉 Baisse
                                @else ➡️ Stable
                                @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Daily comparison table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <span>📅</span> Comparaison jour par jour
                    </h3>

                    @php
                        $dimNames = array_keys($overallStats);
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-gray-200">
                                    <th class="text-left py-3 px-2 font-semibold text-gray-600">Jour</th>
                                    @foreach($dimNames as $label)
                                    <th class="text-center py-3 px-3 font-semibold text-gray-600" colspan="3">
                                        {{ $label }}
                                        <span class="block text-xs font-normal text-gray-400">Cette sem. / J-7 / Écart</span>
                                    </th>
                                    @endforeach
                                    <th class="text-center py-3 px-2 font-semibold text-gray-600">Check-in</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comparisonDays as $day)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-2">
                                        <span class="font-medium text-gray-800">{{ $day['day_name'] }}</span>
                                        <span class="text-xs text-gray-400 block">{{ $day['day_label'] }}</span>
                                    </td>

                                    @foreach($dimNames as $label)
                                    @php $dim = $day['dimensions'][$label] ?? null; @endphp
                                    <td class="text-center py-4 px-2">
                                        @if($dim && $dim['this_val'] !== null)
                                            <span class="font-bold text-lg text-indigo-600">{{ $dim['this_val'] }}</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center py-4 px-2">
                                        @if($dim && $dim['prev_val'] !== null)
                                            <span class="font-bold text-lg text-gray-500">{{ $dim['prev_val'] }}</span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center py-4 px-2">
                                        @if($dim && $dim['diff'] !== null)
                                            <span class="font-bold text-sm
                                                @if($dim['diff'] > 0) text-green-600
                                                @elseif($dim['diff'] < 0) text-red-600
                                                @else text-gray-500
                                                @endif">
                                                {{ $dim['diff_label'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                    @endforeach

                                    <td class="text-center py-4 px-2">
                                        @if($day['this_checkin'])
                                            <span class="text-green-500 text-lg" title="Check-in fait ce jour">✅</span>
                                        @else
                                            <span class="text-gray-300 text-lg" title="Pas de check-in ce jour">⬜</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex flex-wrap gap-6 text-sm text-gray-500">
                        <span>📖 <strong>Cette sem.</strong> = Valeur du jour actuel</span>
                        <span>📖 <strong>J-7</strong> = Valeur du même jour il y a 7 jours</span>
                        <span>📖 <strong>Écart</strong> = Différence (Cette sem. − J-7)</span>
                        <span class="text-green-600">🟢 Écart positif = Amélioration</span>
                        <span class="text-red-600">🔴 Écart négatif = Baisse</span>
                    </div>
                </div>

                <!-- Chart comparison -->
                @foreach($overallStats as $label => $stats)
                @if($stats['days_this'] > 0 || $stats['days_prev'] > 0)
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <span>📈</span> Évolution {{ $label }} — Cette semaine vs J-7
                    </h3>
                    <canvas id="chart-{{ Str::slug($label) }}" class="w-full h-64"></canvas>
                </div>
                @endif
                @endforeach
            @endif
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dims = {!! json_encode($overallStats) !!};
            const days = {!! json_encode(array_map(fn($d) => $d['day_label'], $comparisonDays)) !!};

            @foreach($overallStats as $label => $stats)
            (function() {
                const canvas = document.getElementById('chart-{{ Str::slug($label) }}');
                if (!canvas) return;

                const thisData = {!! json_encode(array_map(fn($d) => $d['dimensions'][$label]['this_val'] ?? null, $comparisonDays)) !!};
                const prevData = {!! json_encode(array_map(fn($d) => $d['dimensions'][$label]['prev_val'] ?? null, $comparisonDays)) !!};

                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: days,
                        datasets: [
                            {
                                label: 'Cette semaine',
                                data: thisData,
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                fill: true,
                                tension: 0.3,
                                pointBackgroundColor: 'rgb(99, 102, 241)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                borderWidth: 2.5,
                            },
                            {
                                label: 'J-7 (semaine précédente)',
                                data: prevData,
                                borderColor: 'rgb(156, 163, 175)',
                                backgroundColor: 'rgba(156, 163, 175, 0.1)',
                                fill: true,
                                tension: 0.3,
                                borderDash: [6, 3],
                                pointBackgroundColor: 'rgb(156, 163, 175)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                borderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 16,
                                    usePointStyle: true,
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#e2e8f0',
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        if (context.parsed.y === null) return context.dataset.label + ' : —';
                                        return context.dataset.label + ' : ' + context.parsed.y + '/10';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 10,
                                grid: { color: 'rgba(0,0,0,0.04)' },
                                ticks: { stepSize: 2 }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { maxTicksLimit: 7 }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            })();
            @endforeach
        });
    </script>
</x-app-layout>
