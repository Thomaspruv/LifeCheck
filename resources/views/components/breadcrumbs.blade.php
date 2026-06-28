@props(['items' => []])

@if(count($items) > 0)
    {{-- Breadcrumb navigation pour le SEO et l'accessibilité --}}
    <nav aria-label="Fil d'Ariane" class="mb-4">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
            @foreach($items as $i => $item)
                @php $isLast = $loop->last; @endphp
                <li class="flex items-center gap-1.5">
                    @if(!$loop->first)
                        <svg class="w-3.5 h-3.3 text-gray-300 dark:text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    @endif

                    @if($isLast || empty($item['url']))
                        <span class="font-medium {{ $isLast ? 'text-gray-800 dark:text-gray-200' : 'text-gray-500 dark:text-gray-400' }}"
                              @if($isLast) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </span>
                    @else
                        <a href="{{ $item['url'] }}"
                           class="text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-150">
                            {{ $item['label'] }}
                        </a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    {{-- Données structurées BreadcrumbList pour le SEO --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": {!! json_encode(array_map(fn($item, $idx) => [
            '@@type' => 'ListItem',
            'position' => $idx + 1,
            'name' => $item['label'],
            'item' => empty($item['url']) ? request()->url() : url($item['url']),
        ], $items, array_keys($items)), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    }
    </script>
@endif
