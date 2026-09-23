<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-full bg-wine-500 px-5 py-2.5 text-sm font-semibold text-canvas transition hover:bg-wine-600 focus:outline-none focus:ring-2 focus:ring-wine-500 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
