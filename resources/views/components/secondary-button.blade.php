<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center gap-2 rounded-full border border-forest-100 bg-white px-5 py-2.5 text-sm font-semibold text-forest-700 transition hover:border-forest-300 hover:bg-forest-50 focus:outline-none focus:ring-2 focus:ring-forest-500 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
