@extends('layouts.admin')
@section('title', 'Laporan')
@section('content')

<div class="grid grid-cols-2 lg:grid-cols-2 gap-4 mb-8">
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-2 !text-ink-faint">Total Omzet (non-cancelled)</p>
        <span class="price-tag text-lg">Rp{{ number_format($totalOmzet, 0, ',', '.') }}</span>
    </div>
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-2 !text-ink-faint">Total Keuntungan</p>
        <span class="price-tag text-lg text-forest-600">Rp{{ number_format($totalProfit, 0, ',', '.') }}</span>
        <p class="text-xs text-ink-faint mt-1">Hanya menghitung produk yang sudah diisi harga modalnya.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="card-soft p-5">
        <p class="font-display text-ink mb-4">Produk Paling Diminati (unit terjual)</p>
        <div class="h-64"><canvas id="chartTerlaris"></canvas></div>
    </div>
    <div class="card-soft p-5">
        <p class="font-display text-ink mb-4">Produk Paling Kurang Diminati</p>
        <div class="h-64"><canvas id="chartSepi"></canvas></div>
    </div>
</div>

<div class="card-soft overflow-hidden">
    <div class="px-5 py-4 border-b border-forest-100 font-display text-ink">Keuntungan per Produk</div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-forest-50 text-ink-muted text-left">
            <tr>
                <th class="px-5 py-3 font-medium">Produk</th>
                <th class="px-5 py-3 font-medium">Unit Terjual</th>
                <th class="px-5 py-3 font-medium">Omzet</th>
                <th class="px-5 py-3 font-medium">Keuntungan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-forest-100">
            @forelse($report as $row)
                <tr>
                    <td class="px-5 py-3.5 text-ink">{{ $row->product_name }}</td>
                    <td class="px-5 py-3.5 text-ink-muted">{{ $row->total_qty }}</td>
                    <td class="px-5 py-3.5"><span class="price-tag text-sm">Rp{{ number_format($row->total_omzet, 0, ',', '.') }}</span></td>
                    <td class="px-5 py-3.5">
                        @if(is_null($row->profit))
                            <span class="text-xs text-ink-faint">Harga modal belum diisi</span>
                        @else
                            <span class="price-tag text-sm {{ $row->profit >= 0 ? 'text-forest-600' : 'text-wine-500' }}">Rp{{ number_format($row->profit, 0, ',', '.') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-ink-muted">Belum ada data penjualan.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@endsection

@push('scripts')
<script type="application/json" id="chart-terlaris-data">@json($terlaris)</script>
<script type="application/json" id="chart-sepi-data">@json($palingSepi)</script>
@vite(['resources/js/reports.js'])
@endpush
