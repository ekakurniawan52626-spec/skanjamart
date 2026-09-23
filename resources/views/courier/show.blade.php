<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Kurir - {{ $delivery->order->order_number }} - SKANJAMart</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas text-ink min-h-screen font-sans">

<div class="max-w-lg mx-auto p-4 sm:p-6">
    <div class="text-center mb-6 pt-4">
        <span class="font-display font-semibold text-xl text-forest-700">SKANJA<span class="text-brass-500 italic">Mart</span></span>
        <p class="text-xs text-ink-faint mt-1">Halaman Kurir</p>
    </div>

    <div class="card-soft p-6 mb-4">
        <p class="section-eyebrow mb-1">Pesanan</p>
        <h1 class="font-display font-medium text-lg text-ink mb-4">#{{ $delivery->order->order_number }}</h1>

        <div class="space-y-2 text-sm mb-4">
            <p><span class="text-ink-faint">Penerima:</span> <span class="font-medium text-ink">{{ $delivery->order->user->name }}</span></p>
            <p><span class="text-ink-faint">Alamat:</span> <span class="font-medium text-ink">{{ $delivery->order->shipping_address }}</span></p>
            <p><span class="text-ink-faint">No. HP:</span> <a href="tel:{{ $delivery->order->phone }}" class="font-medium text-forest-600 underline">{{ $delivery->order->phone }}</a></p>
            <p><span class="text-ink-faint">Metode Bayar:</span> <span class="font-medium text-ink">{{ $delivery->order->payment_method === 'cod' ? 'COD (bayar tunai saat barang tiba)' : 'Sudah dibayar online' }}</span></p>
        </div>

        <div class="border-t border-forest-100 pt-4 mb-4">
            <p class="section-eyebrow mb-2">Item</p>
            <ul class="text-sm space-y-1.5 text-ink-muted">
                @foreach($delivery->order->items as $item)
                    <li>{{ $item->product_name }} &times;{{ $item->quantity }}</li>
                @endforeach
            </ul>
        </div>

        @if($delivery->status === 'delivered')
            <div class="rounded-xl bg-forest-50 border border-forest-100 text-forest-700 px-4 py-3 text-sm font-medium text-center">
                ✓ Pengiriman ini sudah selesai. Terima kasih!
            </div>
        @elseif($delivery->status === 'assigned')
            <form action="{{ route('courier.start', $delivery->token) }}" method="POST">
                @csrf
                <button class="btn-primary w-full !py-3">Mulai Antar</button>
            </form>
            <p class="text-xs text-ink-faint text-center mt-2">Lokasimu mulai dikirim otomatis setelah kamu tekan tombol ini.</p>
        @else
            <div
                x-data="skjCourier({
                    status: @js($delivery->status),
                    interval: @js($delivery->ping_interval_minutes),
                    url: @js(route('courier.location', $delivery->token)),
                    lastSent: @js($delivery->last_ping_at ? \App\Support\Fmt::time($delivery->last_ping_at) : null),
                })"
                x-init="init()"
                class="space-y-3"
            >
                <div class="rounded-xl bg-brass-50 border border-brass-200 px-4 py-3 text-sm">
                    <p class="font-medium text-brass-600" x-show="running">📍 Lokasi sedang dikirim tiap <span x-text="interval"></span> menit</p>
                    <p class="text-ink-faint mt-1" x-show="lastSent">Terakhir terkirim: <span x-text="lastSent"></span> · berikutnya sekitar <span x-text="nextText"></span></p>
                    <p class="text-ink-faint mt-1">Total terkirim: <span x-text="sentCount"></span> kali</p>
                </div>

                <p class="text-sm text-wine-500" x-show="error" x-text="error"></p>
                <p class="text-sm text-wine-500" x-show="!secure">Halaman ini perlu dibuka lewat alamat https agar GPS bisa dipakai.</p>

                <button type="button" @click="tick()" :disabled="busy" class="btn-secondary w-full !py-2.5 text-sm">
                    <span x-show="!busy">Kirim lokasi sekarang</span>
                    <span x-show="busy">Mengirim...</span>
                </button>

                <form action="{{ route('courier.complete', $delivery->token) }}" method="POST" onsubmit="return confirm('Tandai pesanan ini sudah sampai ke tujuan?')">
                    @csrf
                    <button class="btn-brass w-full !py-3">Tandai Sudah Sampai</button>
                </form>
            </div>
        @endif
    </div>

    <p class="text-center text-xs text-ink-faint">Jangan bagikan link ini ke orang lain.</p>
</div>

@if($delivery->status === 'on_the_way')
    <script src="{{ asset('js/skj-courier.js') }}"></script>
@endif
</body>
</html>
