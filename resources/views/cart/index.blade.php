@extends('layouts.store')
@section('title', 'Keranjang Belanja')
@section('content')

<h1 class="font-display font-medium text-2xl text-ink mb-8">Keranjang Belanja</h1>

@if($cart->items->isEmpty())
    <div class="card-soft p-12 text-center">
        <p class="text-ink-muted mb-4">Keranjang kamu masih kosong.</p>
        <a href="{{ route('home') }}" class="btn-primary">Mulai Belanja</a>
    </div>
@else
    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4">
            @foreach($cart->items as $item)
                <div class="card-soft p-4 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="w-16 h-16 bg-forest-50 rounded-xl overflow-hidden flex-shrink-0">
                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-ink truncate mb-1">{{ $item->product->name }}</p>
                            <span class="price-tag text-sm">Rp{{ number_format($item->product->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-2 sm:ml-auto sm:justify-end">
                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                                class="input-classic w-16 py-2 text-sm" aria-label="Jumlah">
                            <button class="px-3 py-2 rounded-full text-xs font-semibold text-forest-700 bg-forest-50 hover:bg-forest-100 transition">Perbarui</button>
                        </form>
                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="px-3 py-2 rounded-full text-xs font-semibold text-wine-500 bg-wine-500/10 hover:bg-wine-500/20 transition" aria-label="Hapus item">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card-soft p-6 h-fit">
            <h2 class="section-eyebrow mb-4">Ringkasan</h2>
            <div class="flex justify-between text-sm mb-2 text-ink-muted">
                <span>Subtotal</span>
                <span class="font-medium text-ink">Rp{{ number_format($cart->total, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-forest-100 my-4"></div>
            <div class="flex justify-between items-center mb-6">
                <span class="font-display text-lg text-ink">Total</span>
                <span class="price-tag text-lg">Rp{{ number_format($cart->total, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="btn-brass w-full">
                Lanjut ke Checkout
            </a>
        </div>
    </div>
@endif
@endsection
