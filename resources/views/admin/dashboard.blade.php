@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-2 !text-ink-faint">Total Produk</p>
        <p class="font-display font-medium text-2xl text-ink">{{ $stats['total_products'] }}</p>
    </div>
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-2 !text-ink-faint">Total Pesanan</p>
        <p class="font-display font-medium text-2xl text-ink">{{ $stats['total_orders'] }}</p>
    </div>
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-2 !text-ink-faint">Pesanan Pending</p>
        <p class="font-display font-medium text-2xl text-brass-500">{{ $stats['pending_orders'] }}</p>
    </div>
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-2 !text-ink-faint">Total Pengguna</p>
        <p class="font-display font-medium text-2xl text-ink">{{ $stats['total_users'] }}</p>
    </div>
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-2 !text-ink-faint">Pendapatan</p>
        <span class="price-tag text-lg">Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</span>
    </div>
</div>

<div class="card-soft overflow-hidden">
    <div class="px-5 py-4 border-b border-forest-100 font-display text-ink">Pesanan Terbaru</div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-forest-50 text-ink-muted text-left">
            <tr><th class="px-5 py-3 font-medium">No. Pesanan</th><th class="px-5 py-3 font-medium">Pelanggan</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Status</th></tr>
        </thead>
        <tbody class="divide-y divide-forest-100">
            @forelse($recentOrders as $order)
                <tr>
                    <td class="px-5 py-3.5"><a href="{{ route('admin.orders.show', $order) }}" class="text-forest-600 font-medium hover:text-forest-700">{{ $order->order_number }}</a></td>
                    <td class="px-5 py-3.5 text-ink">{{ $order->user->name }}</td>
                    <td class="px-5 py-3.5"><span class="price-tag text-sm">Rp{{ number_format($order->total, 0, ',', '.') }}</span></td>
                    <td class="px-5 py-3.5 capitalize text-ink-muted">{{ $order->status }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
@endsection
