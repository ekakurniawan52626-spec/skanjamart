@extends('layouts.store')
@section('title', 'Pembayaran')
@section('content')

<div class="max-w-lg mx-auto card-soft p-6 sm:p-10 text-center">
    <p class="section-eyebrow mb-3">Selesaikan Pembayaran</p>
    <h1 class="font-display font-medium text-2xl text-ink mb-2">Pesanan #{{ $order->order_number }}</h1>
    <span class="price-tag text-xl block mb-8">Rp{{ number_format($order->total, 0, ',', '.') }}</span>

    <button id="pay-button" class="btn-brass w-full sm:w-auto">
        Bayar Sekarang
    </button>

    <p class="text-xs text-ink-faint mt-6">Setelah pembayaran berhasil, status pesanan akan otomatis diperbarui.</p>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function () { window.location.href = "{{ route('orders.show', $order) }}"; },
            onPending: function () { window.location.href = "{{ route('orders.show', $order) }}"; },
            onError: function () { alert('Pembayaran gagal, silakan coba lagi.'); },
        });
    });
</script>
@endsection
