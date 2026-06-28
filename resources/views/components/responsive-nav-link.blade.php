@props(['active' => false, 'href' => '#', 'icon' => null])

@php
    $classes = ($active ?? false)
        ? 'flex items-center gap-3 w-full px-4 py-2 border-l-4 border-primary-500 text-left text-sm font-medium text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/30 focus:outline-none focus:text-primary-800 focus:bg-primary-100 focus:border-primary-700 transition duration-150'
        : 'flex items-center gap-3 w-full px-4 py-2 border-l-4 border-transparent text-left text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150';
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
    @if ($icon)
        <span class="shrink-0 w-5 h-5 flex items-center justify-center">{{ $icon }}</span>
    @endif
    {{ $slot }}
</a>
