@props(['variant' => 'gray', 'dot' => false])

@php
    $variants = [
        'primary' => 'bg-primary-50 text-primary-700 ring-1 ring-inset ring-primary-600/20',
        'success' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
        'warning' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20',
        'danger' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20',
        'gray' => 'bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/20',
        'info' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
    ];

    $colors = [
        'primary' => 'bg-primary-500',
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-red-500',
        'gray' => 'bg-gray-400',
        'info' => 'bg-blue-500',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ' . ($variants[$variant] ?? $variants['gray'])]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $colors[$variant] ?? $colors['gray'] }}"></span>
    @endif
    {{ $slot }}
</span>
