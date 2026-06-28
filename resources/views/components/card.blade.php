@props(['padding' => true, 'header' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-card overflow-hidden']) }}>
    @if ($header)
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            {{ $header }}
        </div>
    @elseif (isset($title) || isset($action))
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                @isset($title)
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                @endisset
                @isset($subtitle)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $subtitle }}</p>
                @endisset
            </div>
            @isset($action)
                <div class="flex items-center gap-2">{{ $action }}</div>
            @endisset
        </div>
    @endif

    @if ($padding)
        <div class="px-6 py-5">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif

    @if ($footer)
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 rounded-b-xl">
            {{ $footer }}
        </div>
    @endif
</div>
