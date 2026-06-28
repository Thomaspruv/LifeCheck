<x-app-layout
    :breadcrumbs="[['label' => __('Tableau de bord'), 'url' => route('dashboard')], ['label' => __('Tendances')]]">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tendances') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(empty($dimensions))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <p class="text-gray-400 text-lg">Pas assez de données pour afficher les tendances.</p>
                    <a href="{{ route('checkin.create') }}" class="mt-4 inline-block text-indigo-600 hover:underline">Faire un check-in →</a>
                </div>
            @else
                <div class="space-y-8">
                    @foreach($dimensions as $label => $dim)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">{{ $label }}</h3>

                        @if($dim['type'] === 'slider' && count($dim['data']) > 1)
                            <canvas id="chart-{{ Str::slug($label) }}" class="w-full h-48"></canvas>
                        @elseif($dim['type'] === 'emoji' && count($dim['data']) > 1)
                            <canvas id="chart-{{ Str::slug($label) }}" class="w-full h-48"></canvas>
                        @else
                            <div class="space-y-2">
                                @foreach($dim['data'] as $d)
                                <div class="flex justify-between text-sm border-b border-gray-100 py-1">
                                    <span class="text-gray-500">{{ $d['date'] }}</span>
                                    <span class="text-gray-800">{{ $d['raw'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        @foreach($dimensions as $label => $dim)
                            @if(($dim['type'] === 'slider' || $dim['type'] === 'emoji') && count($dim['data']) > 1)
                                (function() {
                                    const ctx = document.getElementById('chart-{{ Str::slug($label) }}');
                                    if (!ctx) return;
                                    new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: {!! json_encode(array_column($dim['data'], 'date')) !!},
                                            datasets: [{
                                                label: '{{ $label }}',
                                                data: {!! json_encode(array_column($dim['data'], 'value')) !!},
                                                borderColor: 'rgb(99, 102, 241)',
                                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                                fill: true,
                                                tension: 0.3,
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            plugins: { legend: { display: false } },
                                            scales: {
                                                y: { beginAtZero: true, max: 10 },
                                                x: { ticks: { maxTicksLimit: 10 } }
                                            }
                                        }
                                    });
                                })();
                            @endif
                        @endforeach
                    });
                </script>
            @endif
        </div>
    </div>
</x-app-layout>
