<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-medium text-xl text-ink leading-tight">
            {{ __('Dashboard Saya') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="card-soft p-6">
                <p class="text-ink-muted">
                    Halo, <span class="font-semibold text-ink">{{ auth()->user()->name }}</span>! Ini ringkasan aktivitas belanja kamu.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="card-soft p-6">
                    <p class="section-eyebrow mb-2 !text-ink-faint">Total Pesanan</p>
                    <p class="font-display font-medium text-2xl text-ink">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="card-soft p-6">
                    <p class="section-eyebrow mb-2 !text-ink-faint">Sedang Diproses</p>
                    <p class="font-display font-medium text-2xl text-brass-500">{{ $stats['pending_orders'] }}</p>
                </div>
                <div class="card-soft p-6">
                    <p class="section-eyebrow mb-2 !text-ink-faint">Selesai</p>
                    <p class="font-display font-medium text-2xl text-forest-600">{{ $stats['completed_orders'] }}</p>
                </div>
            </div>

            <div class="card-soft p-6">
                <h3 class="font-display text-ink mb-4">Akses Cepat</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('home') }}" class="btn-primary">Belanja Sekarang</a>
                    <a href="{{ route('cart.index') }}" class="btn-secondary">Keranjang</a>
                    <a href="{{ route('orders.index') }}" class="btn-secondary">Semua Pesanan</a>
                    <a href="{{ route('profile.edit') }}" class="btn-secondary">Edit Profil</a>
                </div>
            </div>

            <div class="card-soft overflow-hidden">
                <div class="px-6 py-4 border-b border-forest-100 font-display text-ink">
                    Pesanan Terbaru
                </div>
                <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-forest-50 text-ink-muted text-left">
                        <tr>
                            <th class="px-6 py-3 font-medium whitespace-nowrap">No. Pesanan</th>
                            <th class="px-6 py-3 font-medium whitespace-nowrap">Tanggal</th>
                            <th class="px-6 py-3 font-medium whitespace-nowrap">Total</th>
                            <th class="px-6 py-3 font-medium whitespace-nowrap">Status</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-forest-100">
                        @forelse($orders as $order)
                            <tr>
                                <td class="px-6 py-3.5 font-medium text-ink whitespace-nowrap">{{ $order->order_number }}</td>
                                <td class="px-6 py-3.5 text-ink-muted whitespace-nowrap">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap"><span class="price-tag text-sm">Rp{{ number_format($order->total, 0, ',', '.') }}</span></td>
                                <td class="px-6 py-3.5 capitalize text-ink-muted whitespace-nowrap">{{ $order->status }}</td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('orders.show', $order) }}" class="inline-block px-3 py-1.5 rounded-full text-xs font-semibold text-forest-700 bg-forest-50 hover:bg-forest-100 transition">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-ink-muted">Belum ada pesanan. Yuk mulai belanja!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
