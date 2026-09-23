@extends('layouts.admin')
@section('title', 'Pengiriman')
@section('content')

<div class="flex flex-wrap items-center gap-2 mb-6">
    <a href="{{ route('admin.deliveries.index', ['status' => 'aktif']) }}" class="pill {{ $filter === 'aktif' ? 'pill-active' : 'pill-inactive' }}">Aktif</a>
    <a href="{{ route('admin.deliveries.index', ['status' => 'selesai']) }}" class="pill {{ $filter === 'selesai' ? 'pill-active' : 'pill-inactive' }}">Selesai</a>
    <a href="{{ route('admin.deliveries.index', ['status' => 'semua']) }}" class="pill {{ $filter === 'semua' ? 'pill-active' : 'pill-inactive' }}">Semua</a>
</div>

@if($mapPoints->isNotEmpty())
    <div class="card-soft p-4 mb-6">
        <h3 class="section-eyebrow mb-3">Peta kurir yang sedang jalan ({{ $mapPoints->count() }})</h3>
        <div id="deliveries-map" style="height: 320px" class="rounded-xl overflow-hidden"></div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var points = @json($mapPoints);
            if (!points.length || typeof L === 'undefined') return;
            var map = L.map('deliveries-map').setView([points[0].lat, points[0].lng], 12);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; kontributor OpenStreetMap' }).addTo(map);
            var bounds = [];
            points.forEach(function (p) {
                var marker = L.marker([p.lat, p.lng]).addTo(map)
                    .bindPopup('<b>' + p.title + '</b><br>' + (p.stale ? '⚠️ ' : '') + 'diperbarui ' + p.ago + '<br><a href="' + p.url + '">Buka pesanan</a>');
                bounds.push([p.lat, p.lng]);
            });
            if (bounds.length > 1) map.fitBounds(bounds, { padding: [30, 30] });
        });
    </script>
@endif

<div class="card-soft overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-forest-50 text-ink-muted text-left">
            <tr>
                <th class="px-5 py-3 font-medium">No. Pesanan</th>
                <th class="px-5 py-3 font-medium">Pelanggan</th>
                <th class="px-5 py-3 font-medium">Kurir</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Update Terakhir</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-forest-100">
            @forelse($deliveries as $delivery)
                <tr>
                    <td class="px-5 py-3.5 font-medium text-ink">{{ $delivery->order->order_number }}</td>
                    <td class="px-5 py-3.5 text-ink">{{ $delivery->order->user->name }}</td>
                    <td class="px-5 py-3.5 text-ink-muted">{{ $delivery->courier->name }}</td>
                    <td class="px-5 py-3.5">
                        <span class="pill {{ $delivery->status === 'delivered' ? 'pill-active' : 'pill-inactive' }} !py-0.5 !px-2.5 text-xs">
                            {{ $delivery->status_label }}
                        </span>
                        @if($delivery->isStale())
                            <span class="block text-xs text-wine-500 mt-1">⚠️ lokasi lama tidak update</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-ink-muted whitespace-nowrap">
                        {{ $delivery->last_ping_at ? \App\Support\Fmt::ago($delivery->last_ping_at) : '-' }}
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <a href="{{ route('admin.orders.show', $delivery->order) }}" class="text-forest-600 font-medium hover:text-forest-700">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-ink-muted">Belum ada pengiriman pada filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-6">{{ $deliveries->links() }}</div>
@endsection
