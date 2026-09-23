@extends('layouts.store')
@section('title', 'Checkout')
@section('content')

<h1 class="font-display font-medium text-2xl text-ink mb-8">Checkout</h1>

<div class="grid lg:grid-cols-3 gap-8">
    <form action="{{ route('checkout.store') }}" method="POST" class="lg:col-span-2 card-soft p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Alamat Pengiriman</label>
            <textarea name="shipping_address" rows="3" required
                class="input-classic">{{ auth()->user()->address }}</textarea>
            @error('shipping_address') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">No. HP</label>
            <input type="text" name="phone" value="{{ auth()->user()->phone }}" required class="input-classic">
            @error('phone') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-2">Metode Pembayaran</label>
            <div class="space-y-2">
                <label class="flex items-center gap-3 border border-forest-100 rounded-xl px-4 py-3 cursor-pointer has-[:checked]:border-forest-500 has-[:checked]:bg-forest-50 transition">
                    <input type="radio" name="payment_method" value="midtrans" checked class="text-forest-600 focus:ring-forest-500">
                    <span class="text-sm text-ink">Bayar Online (Kartu / VA / E-wallet - Midtrans)</span>
                </label>
                <label class="flex items-center gap-3 border border-forest-100 rounded-xl px-4 py-3 cursor-pointer has-[:checked]:border-forest-500 has-[:checked]:bg-forest-50 transition">
                    <input type="radio" name="payment_method" value="cod" class="text-forest-600 focus:ring-forest-500">
                    <span class="text-sm text-ink">Bayar di Tempat (COD)</span>
                </label>
            </div>
        </div>
        <button class="btn-brass w-full">Buat Pesanan</button>
    </form>

    <div class="card-soft p-6 h-fit">
        <h2 class="section-eyebrow mb-4">Ringkasan Pesanan</h2>
        <ul class="space-y-2.5 text-sm mb-4">
            @foreach($cart->items as $item)
                <li class="flex justify-between text-ink-muted">
                    <span>{{ $item->product->name }} &times;{{ $item->quantity }}</span>
                    <span class="font-medium text-ink">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-forest-100 pt-4 flex justify-between items-center">
            <span class="font-display text-lg text-ink">Total</span>
            <span class="price-tag text-lg">Rp{{ number_format($cart->total, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
@endsection
