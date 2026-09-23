@extends('layouts.store')
@section('title', 'Pesanan Saya')
@section('content')

<h1 class="font-display font-medium text-2xl text-ink mb-8">Pesanan Saya</h1>

<div class="card-soft overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-forest-50 text-ink-muted text-left">
            <tr>
                <th class="px-5 py-3 font-medium whitespace-nowrap">No. Pesanan</th>
                <th class="px-5 py-3 font-medium whitespace-nowrap">Tanggal</th>
                <th class="px-5 py-3 font-medium whitespace-nowrap">Total</th>
                <th class="px-5 py-3 font-medium whitespace-nowrap">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-forest-100">
            @forelse($orders as $order)
                <tr>
                    <td class="px-5 py-3.5 font-medium text-ink whitespace-nowrap">{{ $order->order_number }}</td>
                    <td class="px-5 py-3.5 text-ink-muted whitespace-nowrap">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3.5 whitespace-nowrap"><span class="price-tag text-sm">Rp{{ number_format($order->total, 0, ',', '.') }}</span></td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-forest-50 text-forest-700 capitalize">{{ $order->status }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                        <a href="{{ route('orders.show', $order) }}" class="inline-block px-3 py-1.5 rounded-full text-xs font-semibold text-forest-700 bg-forest-50 hover:bg-forest-100 transition">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-ink-muted">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
