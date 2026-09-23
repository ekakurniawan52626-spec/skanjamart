@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-xl border-ink-faint/40 bg-white text-ink shadow-sm focus:border-forest-500 focus:ring-forest-500']) }}>
