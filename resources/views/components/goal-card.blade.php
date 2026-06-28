@props(['goal'])

@php
    $total = $goal->total_milestones_count;
    $done = $goal->completed_milestones_count;
    $percent = $total > 0 ? min(100, (int) round(($done / $total) * 100)) : 0;

    $statusConfig = [
        'active' => ['border' => 'border-l-emerald-500', 'badge' => 'success', 'label' => __('En cours'), 'icon' => '🟢'],
        'completed' => ['border' => 'border-l-blue-500', 'badge' => 'primary', 'label' => __('Accompli'), 'icon' => '✅'],
        'abandoned' => ['border' => 'border-l-gray-400', 'badge' => 'gray', 'label' => __('Abandonné'), 'icon' => '📦'],
    ];

    $cfg = $statusConfig[$goal->status] ?? ['border' => 'border-l-gray-300', 'badge' => 'gray', 'label' => $goal->status, 'icon' => ''];
@endphp

<a href="{{ route('goals.show', $goal) }}"
   class="block bg-white overflow-hidden rounded-xl border border-gray-200 border-l-4 {{ $cfg['border'] }} shadow-card hover:shadow-card-hover transition-all duration-200 hover:-translate-y-0.5">
    <div class="p-5">
        <div class="flex items-start justify-between gap-4 mb-3">
            <h4 class="font-semibold text-gray-900 text-base leading-snug">{{ $goal->title }}</h4>
            <x-badge :variant="$cfg['badge']" :dot="true">{{ $cfg['icon'] }} {{ $cfg['label'] }}</x-badge>
        </div>

        @if ($goal->description)
            <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $goal->description }}</p>
        @endif

        @if ($total > 0)
            <div>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                    <span>{{ $done }}/{{ $total }} {{ __('jalons') }}</span>
                    <span class="font-medium">{{ $percent }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-fill {{ $goal->status === 'completed' ? 'bg-blue-500' : ($goal->status === 'abandoned' ? 'bg-gray-400' : 'bg-emerald-500') }}"
                         style="width: {{ $percent }}%"></div>
                </div>
            </div>
        @else
            <p class="text-xs text-gray-400 italic">{{ __('Aucun jalon défini') }}</p>
        @endif

        <div class="flex items-center gap-3 mt-3 text-xs text-gray-400">
            @if ($goal->target_date)
                <span>🎯 {{ $goal->target_date->format('d/m/Y') }}</span>
            @endif
            <span>{{ __('Créé') }} {{ $goal->created_at->format('d/m/Y') }}</span>
            @if ($goal->completed_at)
                <span>· {{ __('Terminé') }}
                    {{ $goal->completed_at instanceof \Carbon\Carbon ? $goal->completed_at->format('d/m/Y') : \Carbon\Carbon::parse($goal->completed_at)->format('d/m/Y') }}
                </span>
            @endif
        </div>
    </div>
</a>
