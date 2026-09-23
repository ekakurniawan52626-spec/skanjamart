@extends('layouts.admin')
@section('title', 'Pesanan #' . $order->order_number)
@section('content')

<div class="max-w-2xl card-soft p-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="font-display font-medium text-lg text-ink">{{ $order->order_number }}</h2>
            <p class="text-ink-faint text-sm mt-0.5">{{ $order->user->name }} &middot; {{ $order->user->email }}</p>
        </div>
        <form action="{{ route('admin.orders.update', $order) }}" method="POST">
            @csrf @method('PATCH')
            <select name="status" onchange="this.form.submit()" class="input-classic text-sm py-2">
                @foreach(['pending','processing','shipped','completed','cancelled'] as $status)
                    <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 text-sm mb-6">
        <div><p class="text-ink-faint mb-1">Alamat</p><p class="font-medium text-ink">{{ $order->shipping_address }}</p></div>
        <div><p class="text-ink-faint mb-1">No. HP</p><p class="font-medium text-ink">{{ $order->phone }}</p></div>
        <div><p class="text-ink-faint mb-1">Metode Pembayaran</p><p class="font-medium text-ink capitalize">{{ $order->payment_method }}</p></div>
        <div><p class="text-ink-faint mb-1">Status Pembayaran</p><p class="font-medium text-ink capitalize">{{ $order->payment_status }}</p></div>
    </div>

    <div class="border-t border-forest-100 pt-5">
        <h3 class="section-eyebrow mb-3">Item</h3>
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

    @include('partials.delivery-panel')
</div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/skj-tracking.js') }}"></script>
    <script src="{{ asset('js/skj-picker.js') }}"></script>
@endpush
