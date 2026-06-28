@props(['variant' => 'secondary', 'size' => 'md'])

@php
    $variants = [
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-primary-500 active:bg-gray-100 shadow-sm',
        'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 focus:ring-gray-400',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];

    $variantClass = $variants[$variant] ?? $variants['secondary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed ' . $variantClass . ' ' . $sizeClass
]) }}>
    {{ $slot }}
</button>
