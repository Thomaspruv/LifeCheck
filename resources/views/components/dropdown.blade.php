@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-800', 'dropdownClasses' => ''])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    'bottom' => 'origin-bottom',
    'right' => 'ltr:origin-top-right rtl:origin-top-left end-0',
    default => 'origin-top',
};

$width = match ($width) {
    '48' => 'w-48',
    '56' => 'w-56',
    '64' => 'w-64',
    default => 'w-48',
};
@endphp

<div x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false" class="relative">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $width }} rounded-xl {{ $alignmentClasses }} {{ $dropdownClasses }}"
         style="display: none;"
         @click="open = false">
        <div class="{{ $contentClasses }} rounded-xl ring-1 ring-black/5 dark:ring-white/10 shadow-dropdown border border-gray-100 dark:border-gray-700">
            {{ $content }}
        </div>
    </div>
</div>
