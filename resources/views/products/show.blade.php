@extends('layouts.store')
@section('title', $product->name)
@section('content')

<div class="grid md:grid-cols-2 gap-12">
    <div class="aspect-square card-soft flex items-center justify-center overflow-hidden">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
    </div>

    <div>
        @if($product->category)
            <a href="{{ route('home', ['category' => $product->category_id]) }}" class="section-eyebrow">{{ $product->category->name }}</a>
        @endif
        <h1 class="font-display font-medium text-3xl text-ink mt-2 mb-4 leading-snug">{{ $product->name }}</h1>
        <span class="price-tag text-2xl mb-6 block">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
        <p class="text-ink-muted leading-relaxed mb-6">{{ $product->description ?: 'Tidak ada deskripsi.' }}</p>

        <p class="text-sm text-ink-muted mb-6">
            Stok: <span class="font-medium {{ $product->stock > 0 ? 'text-forest-600' : 'text-wine-500' }}">
                {{ $product->stock > 0 ? $product->stock . ' tersedia' : 'Habis' }}
            </span>
        </p>

        @auth
            @if($product->stock > 0)
                <form action="{{ route('cart.store', $product) }}" method="POST" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                        class="input-classic w-20 py-2.5 shrink-0" aria-label="Jumlah">
                    <button class="btn-brass flex-1 sm:flex-none min-w-[180px]">Tambah ke Keranjang</button>
                </form>
            @else
                <span class="inline-block bg-ink-faint/10 text-ink-faint px-6 py-2.5 rounded-full font-medium">Stok Habis</span>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn-primary">Masuk untuk membeli</a>
        @endauth
    </div>
</div>

@if($related->isNotEmpty())
<div class="mt-16">
    <h2 class="section-eyebrow mb-4">Produk Serupa</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach($related as $r)
            <a href="{{ route('products.show', $r) }}" class="card-soft overflow-hidden hover:shadow-soft transition">
                <div class="aspect-square bg-forest-50 flex items-center justify-center">
                    <img src="{{ $r->image_url }}" alt="{{ $r->name }}" class="w-full h-full object-cover">
                </div>
                <div class="p-3">
                    <p class="text-sm text-ink line-clamp-2 mb-1">{{ $r->name }}</p>
                    <span class="price-tag text-sm">Rp{{ number_format($r->price, 0, ',', '.') }}</span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif
@endsection
