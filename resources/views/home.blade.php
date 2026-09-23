@extends('layouts.store')
@section('title', 'Belanja di SKANJAMart')
@section('content')

<div class="relative overflow-hidden rounded-3xl bg-hero-gradient border border-forest-100 px-5 sm:px-8 py-8 sm:py-12 md:py-16 mb-8 sm:mb-10">
    <div class="relative max-w-xl">
        <p class="section-eyebrow mb-3">Toko Pilihan &middot; Sejak 2026</p>
        <h1 class="font-display font-medium text-2xl sm:text-3xl md:text-4xl text-forest-700 leading-tight mb-4">
            Belanja tenang, harga bersahabat, kualitas terjaga.
        </h1>
        <p class="text-ink-muted leading-relaxed">
            Temukan seleramu Bangun kreativitasmu
        </p>
    </div>
    <div class="hidden md:block absolute -right-6 -bottom-10 w-56 h-56 rounded-full bg-brass-200/30 blur-2xl"></div>
</div>

<div class="flex flex-col md:flex-row gap-10">
    <aside class="md:w-56 flex-shrink-0">
        <h2 class="section-eyebrow mb-4">Kategori</h2>
        <ul class="flex flex-wrap md:flex-col gap-2">
            <li>
                <a href="{{ route('home') }}" class="pill {{ !request('category') ? 'pill-active' : 'pill-inactive' }}">
                    Semua Produk
                </a>
            </li>
            @foreach($categories as $cat)
                <li>
                    <a href="{{ route('home', ['category' => $cat->id]) }}"
                        class="pill {{ request('category') == $cat->id ? 'pill-active' : 'pill-inactive' }}">
                        {{ $cat->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </aside>

    <div class="flex-1">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @forelse($products as $product)
                <a href="{{ route('products.show', $product) }}" class="group card-soft overflow-hidden hover:shadow-soft hover:-translate-y-0.5 transition">
                    <div class="aspect-square bg-forest-50 flex items-center justify-center overflow-hidden">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    </div>
                    <div class="p-4">
                        <p class="text-sm text-ink line-clamp-2 mb-2 leading-snug">{{ $product->name }}</p>
                        <span class="price-tag text-base">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-ink-muted text-sm">Belum ada produk yang cocok.</p>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
