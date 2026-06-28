@props(['disabled' => false, 'prefix' => null, 'suffix' => null])

<div class="relative">
    @if ($prefix)
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <span class="text-gray-400 sm:text-sm">{{ $prefix }}</span>
        </div>
    @endif

    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
        'class' => 'block w-full rounded-lg border-gray-300 shadow-sm transition-all duration-150 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 disabled:bg-gray-50 disabled:text-gray-500 text-sm' . ($prefix ? ' pl-10' : '') . ($suffix ? ' pr-10' : '')
    ]) !!} />

    @if ($suffix)
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <span class="text-gray-400 sm:text-sm">{{ $suffix }}</span>
        </div>
    @endif
</div>
