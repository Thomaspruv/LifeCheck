@props(['challenge'])

@php
    $config = [
        'active' => ['badge' => 'warning', 'label' => __('En cours')],
        'completed' => ['badge' => 'success', 'label' => __('Réussi')],
        'failed' => ['badge' => 'danger', 'label' => __('Échoué')],
    ];
    $cfg = $config[$challenge->status] ?? ['badge' => 'gray', 'label' => $challenge->status];
@endphp

<div class="card p-5 hover:shadow-card-hover transition-all duration-200">
    <div class="flex items-start justify-between gap-3 mb-2">
        <h4 class="font-semibold text-gray-900 text-base">{{ $challenge->title }}</h4>
        <x-badge :variant="$cfg['badge']">{{ $cfg['label'] }}</x-badge>
    </div>

    @if ($challenge->description)
        <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $challenge->description }}</p>
    @endif

    @if ($challenge->type)
        <p class="text-xs text-gray-400">
            <span class="font-medium">{{ __('Type') }} :</span>
            @lang("challenges.types.{$challenge->type}")
        </p>
    @endif
</div>
