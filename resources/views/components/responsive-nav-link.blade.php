@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-forest-500 text-start text-base font-medium text-forest-700 bg-forest-50 focus:outline-none focus:text-forest-800 focus:bg-forest-100 focus:border-forest-600 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-ink-muted hover:text-forest-700 hover:bg-forest-50 hover:border-forest-200 focus:outline-none focus:text-forest-700 focus:bg-forest-50 focus:border-forest-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
