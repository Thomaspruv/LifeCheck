@props(['goal'])

@php
    $total = $goal->total_milestones_count;
    $done = $goal->completed_milestones_count;
    $percent = $total > 0 ? min(100, (int) round(($done / $total) * 100)) : 0;

    $statusColors = [
        'active' => 'border-l-green-500',
        'completed' => 'border-l-blue-500',
        'abandoned' => 'border-l-gray-400',
    ];

    $statusLabels = [
        'active' => 'En cours',
        'completed' => 'Accompli',
        'abandoned' => 'Abandonné',
    ];

    $statusIcons = [
        'active' => '🟢',
        'completed' => '✅',
        'abandoned' => '📦',
    ];

    $borderColor = $statusColors[$goal->status] ?? 'border-l-gray-300';
    $label = $statusLabels[$goal->status] ?? $goal->status;
    $icon = $statusIcons[$goal->status] ?? '';
@endphp

<a href="{{ route('goals.show', $goal) }}"
   class="block bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 {{ $borderColor }} hover:shadow-md transition">
    <div class="p-6">
        <div class="flex items-start justify-between mb-3">
            <h4 class="font-semibold text-gray-800 text-lg">{{ $goal->title }}</h4>
            <span class="text-xs text-gray-500 whitespace-nowrap ml-2">{{ $icon }} {{ $label }}</span>
        </div>

        @if ($goal->description)
            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $goal->description }}</p>
        @endif

        @if ($total > 0)
            <!-- Progress bar -->
            <div class="mb-2">
                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                    <span>{{ $done }}/{{ $total }} jalons</span>
                    <span>{{ $percent }}%</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500
                        {{ $goal->status === 'completed' ? 'bg-blue-500' : ($goal->status === 'abandoned' ? 'bg-gray-400' : 'bg-green-500') }}"
                        style="width: {{ $percent }}%">
                    </div>
                </div>
            </div>
        @else
            <p class="text-xs text-gray-400 italic">Aucun jalon défini</p>
        @endif

        @if ($goal->target_date)
            <p class="text-xs text-gray-400 mt-2">🎯 Cible : {{ $goal->target_date->format('d/m/Y') }}</p>
        @endif

        <p class="text-xs text-gray-400 mt-1">
            Créé le {{ $goal->created_at->format('d/m/Y') }}
            @if ($goal->completed_at)
                · Terminé le {{ $goal->completed_at instanceof \Carbon\Carbon ? $goal->completed_at->format('d/m/Y') : \Carbon\Carbon::parse($goal->completed_at)->format('d/m/Y') }}
            @endif
        </p>
    </div>
</a>
