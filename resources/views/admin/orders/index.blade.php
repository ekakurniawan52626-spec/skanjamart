@extends('layouts.admin')
@section('title', 'Pesanan')
@section('content')

<div class="card-soft overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-forest-50 text-ink-muted text-left">
            <tr><th class="px-5 py-3 font-medium">No. Pesanan</th><th class="px-5 py-3 font-medium">Pelanggan</th><th class="px-5 py-3 font-medium">Total</th><th class="px-5 py-3 font-medium">Pembayaran</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-forest-100">
            @forelse($orders as $order)
                <tr>
                    <td class="px-5 py-3.5 font-medium text-ink">{{ $order->order_number }}</td>
                    <td class="px-5 py-3.5 text-ink">{{ $order->user->name }}</td>
                    <td class="px-5 py-3.5"><span class="price-tag text-sm">Rp{{ number_format($order->total, 0, ',', '.') }}</span></td>
                    <td class="px-5 py-3.5 capitalize text-ink-muted">{{ $order->payment_status }}</td>
                    <td class="px-5 py-3.5 capitalize text-ink-muted">{{ $order->status }}</td>
                    <td class="px-5 py-3.5 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-forest-600 font-medium hover:text-forest-700">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-ink-muted">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-6">{{ $orders->links() }}</div>
@endsection
