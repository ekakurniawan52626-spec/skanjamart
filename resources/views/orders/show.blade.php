@extends('layouts.store')
@section('title', 'Pesanan #' . $order->order_number)
@section('content')

<div class="max-w-2xl mx-auto card-soft p-8">
    <div class="flex justify-between items-start mb-6">
        <div>
            <p class="section-eyebrow mb-1">Pesanan</p>
            <h1 class="font-display font-medium text-xl text-ink">#{{ $order->order_number }}</h1>
            <p class="text-ink-faint text-sm mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-medium bg-forest-50 text-forest-700 capitalize">{{ $order->status_label }}</span>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 text-sm mb-6">
        <div>
            <p class="text-ink-faint mb-1">Alamat Pengiriman</p>
            <p class="font-medium text-ink">{{ $order->shipping_address }}</p>
        </div>
        <div>
            <p class="text-ink-faint mb-1">No. HP</p>
            <p class="font-medium text-ink">{{ $order->phone }}</p>
        </div>
        <div>
            <p class="text-ink-faint mb-1">Metode Pembayaran</p>
            <p class="font-medium text-ink capitalize">{{ $order->payment_method }}</p>
        </div>
        <div>
            <p class="text-ink-faint mb-1">Status Pembayaran</p>
            <p class="font-medium text-ink">{{ $order->payment_status_label }}</p>
        </div>
    </div>

    @if($order->payment_method === 'midtrans' && $order->payment_status === 'unpaid' && $order->status !== 'cancelled')
        <a href="{{ route('payment.pay', $order) }}" class="btn-brass w-full mb-6">
            Lanjutkan Pembayaran
        </a>
    @endif

    {{-- ===== Lacak Pesanan ===== --}}
    <div
        x-data="skjTracking({ initial: @js($tracking), url: @js(route('orders.tracking', $order)), pollMs: 30000 })"
        x-init="init()"
        class="border-t border-forest-100 pt-5 mb-6"
    >
        <h2 class="section-eyebrow mb-4">Lacak Pesanan</h2>

        <ol class="space-y-0">
            <template x-for="(step, i) in data.steps" :key="step.key">
                <li class="flex gap-3 pb-5 last:pb-0 relative">
                    <div class="flex flex-col items-center">
                        <span
                            class="w-6 h-6 rounded-full flex items-center justify-center text-xs shrink-0"
                            :class="step.done ? 'bg-forest-500 text-canvas' : (step.current ? 'bg-brass-400 text-white' : 'bg-forest-50 text-ink-faint border border-forest-100')"
                        >
                            <span x-show="step.done">✓</span>
                        </span>
                        <span class="w-px flex-1 bg-forest-100" x-show="i < data.steps.length - 1"></span>
                    </div>
                    <div class="pb-1">
                        <p class="text-sm font-medium" :class="step.done || step.current ? 'text-ink' : 'text-ink-faint'" x-text="step.label"></p>
                        <p class="text-xs text-ink-faint mt-0.5" x-show="step.hint" x-text="step.hint"></p>
                        <p class="text-xs text-ink-faint mt-0.5" x-show="step.at" x-text="step.at"></p>
                    </div>
                </li>
            </template>
        </ol>

        <template x-if="delivery">
            <div class="mt-4 pt-4 border-t border-forest-100 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-medium text-ink" x-text="delivery.courier.name"></p>
                        <p class="text-xs text-ink-faint" x-text="delivery.status_label"></p>
                    </div>
                    <a :href="'https://wa.me/' + delivery.courier.phone.replace(/[^0-9]/g,'').replace(/^0/,'62')" target="_blank" class="btn-secondary !py-1.5 !px-3.5 text-sm">Hubungi Kurir</a>
                </div>

                <p class="text-sm text-forest-700 font-medium" x-text="delivery.eta_text"></p>
                <p class="text-xs text-ink-faint" x-show="delivery.last" x-text="delivery.last ? ('Posisi kurir diperbarui ' + delivery.last.ago) : ''"></p>
                <p class="text-xs text-wine-500" x-show="delivery.stale">⚠️ Posisi kurir sudah agak lama tidak diperbarui. Sabar ya, atau hubungi kurir lewat tombol di atas.</p>

                <template x-if="hasMapData">
                    <div x-ref="map" style="height: 260px" class="rounded-xl overflow-hidden border border-forest-100"></div>
                </template>
                <p class="text-xs text-ink-faint" x-show="mapError">Peta belum bisa dimuat (periksa koneksi internet).</p>
            </div>
        </template>

        <p class="text-xs text-ink-faint mt-3" x-show="!finished">Diperbarui otomatis · terakhir <span x-text="data.refreshed_at"></span></p>
        <p class="text-xs text-wine-500 mt-3" x-show="offline">Gagal memuat pembaruan, akan dicoba lagi otomatis.</p>
    </div>

    <div class="border-t border-forest-100 pt-5">
        <h2 class="section-eyebrow mb-3">Item Pesanan</h2>
        <ul class="space-y-2.5 text-sm mb-4">
            @foreach($order->items as $item)
                <li class="flex justify-between text-ink-muted">
                    <span>{{ $item->product_name }} &times;{{ $item->quantity }}</span>
                    <span class="font-medium text-ink">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>
        <div class="border-t border-forest-100 pt-4 flex justify-between items-center">
            <span class="font-display text-lg text-ink">Total</span>
            <span class="price-tag text-lg">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/skj-tracking.js') }}"></script>
@endpush
