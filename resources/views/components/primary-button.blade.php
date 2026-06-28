@props(['variant' => 'primary', 'size' => 'md', 'loading' => false])

@php
    $variants = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 active:bg-primary-800 shadow-sm',
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-primary-500 active:bg-gray-100 shadow-sm',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 active:bg-red-800 shadow-sm',
        'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 focus:ring-gray-400 active:bg-gray-200',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 active:bg-emerald-800 shadow-sm',
        'outline-primary' => 'bg-transparent text-primary-600 border border-primary-300 hover:bg-primary-50 focus:ring-primary-500 active:bg-primary-100',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'md' => 'px-4 py-2 text-sm gap-2',
        'lg' => 'px-6 py-3 text-base gap-2.5',
    ];

    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed ' . $variantClass . ' ' . $sizeClass
]) }}>
    @if ($loading)
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    @endif
    {{ $slot }}
</button>
