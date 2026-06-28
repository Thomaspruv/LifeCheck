@props(['challenge'])

@php
    $totalDays = $challenge->duration_days;
    $doneDays = $challenge->done_days ?? $challenge->progress()->where('is_done', true)->count();
    $percent = $totalDays > 0 ? min(100, (int) round(($doneDays / $totalDays) * 100)) : 0;

    $statusColors = [
        'active' => 'border-l-green-500',
        'paused' => 'border-l-yellow-500',
        'completed' => 'border-l-blue-500',
        'failed' => 'border-l-gray-400',
    ];

    $statusLabels = [
        'active' => 'En cours',
        'paused' => 'En pause',
        'completed' => 'Terminé',
        'failed' => 'Échoué',
    ];

    $statusIcons = [
        'active' => '🟢',
        'paused' => '⏸️',
        'completed' => '✅',
        'failed' => '❌',
    ];

    $borderColor = $statusColors[$challenge->status] ?? 'border-l-gray-300';
    $label = $statusLabels[$challenge->status] ?? $challenge->status;
    $icon = $statusIcons[$challenge->status] ?? '';
@endphp

<a href="{{ route('challenges.show', $challenge) }}"
   class="block bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 {{ $borderColor }} hover:shadow-md transition">
    <div class="p-6">
        <div class="flex items-start justify-between mb-3">
            <h4 class="font-semibold text-gray-800 text-lg">{{ $challenge->title }}</h4>
            <span class="text-xs text-gray-500 whitespace-nowrap ml-2">{{ $icon }} {{ $label }}</span>
        </div>

        @if ($challenge->description)
            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $challenge->description }}</p>
        @endif

        <!-- Progress bar -->
        <div class="mb-2">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>{{ $doneDays }}/{{ $totalDays }} jours</span>
                <span>{{ $percent }}%</span>
            </div>
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500
                    {{ $challenge->status === 'completed' ? 'bg-blue-500' : ($challenge->status === 'failed' ? 'bg-gray-400' : 'bg-green-500') }}"
                    style="width: {{ $percent }}%">
                </div>
            </div>
        </div>

        <p class="text-xs text-gray-400 mt-2">
            Démarré le {{ $challenge->started_at->format('d/m/Y') }}
            @if ($challenge->completed_at)
                · Terminé le {{ $challenge->completed_at instanceof \Carbon\Carbon ? $challenge->completed_at->format('d/m/Y') : \Carbon\Carbon::parse($challenge->completed_at)->format('d/m/Y') }}
            @endif
        </p>
    </div>
</a>
