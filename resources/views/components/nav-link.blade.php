@props(['active' => false, 'href' => '#', 'icon' => null])

@php
    $classes = ($active ?? false)
        ? 'inline-flex items-center gap-2 px-1 pt-1 border-b-2 border-primary-500 text-sm font-medium text-gray-900 dark:text-white focus:outline-none focus:border-primary-700 transition duration-150'
        : 'inline-flex items-center gap-2 px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-700 dark:focus:text-gray-200 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150';
@endphp

<a {{ $attributes->merge(['href' => $href, 'class' => $classes]) }}>
    @if ($icon)
        <span class="shrink-0">{{ $icon }}</span>
    @endif
    {{ $slot }}
</a>
