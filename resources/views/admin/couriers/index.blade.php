@extends('layouts.admin')
@section('title', 'Kurir')
@section('content')

<div class="grid lg:grid-cols-3 gap-8">
    <form action="{{ route('admin.couriers.store') }}" method="POST" class="card-soft p-6 h-fit space-y-4">
        @csrf
        <h2 class="font-display text-ink">Tambah Kurir</h2>
        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Nama</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="input-classic">
            @error('name') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">No. HP (aktif di WhatsApp)</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx" class="input-classic">
            @error('phone') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Kendaraan (opsional)</label>
            <input type="text" name="vehicle" value="{{ old('vehicle') }}" placeholder="Motor, Mobil box, dll." class="input-classic">
        </div>
        <button class="btn-primary !py-2">Simpan</button>
    </form>

    <div class="lg:col-span-2 card-soft overflow-hidden h-fit">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-forest-50 text-ink-muted text-left">
                <tr>
                    <th class="px-5 py-3 font-medium">Nama</th>
                    <th class="px-5 py-3 font-medium">No. HP</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Pengiriman Aktif</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-forest-100">
                @forelse($couriers as $courier)
                    <tr>
                        <td class="px-5 py-3.5 font-medium text-ink">
                            {{ $courier->name }}
                            @if($courier->vehicle)
                                <span class="block text-xs text-ink-faint font-normal">{{ $courier->vehicle }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-ink-muted">{{ $courier->phone }}</td>
                        <td class="px-5 py-3.5">
                            @if($courier->is_active)
                                <span class="pill pill-active !py-0.5 !px-2.5 text-xs">Aktif</span>
                            @else
                                <span class="pill pill-inactive !py-0.5 !px-2.5 text-xs">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-ink-muted">{{ $courier->active_deliveries_count }} / {{ $courier->total_deliveries_count }} total</td>
                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <a href="{{ route('admin.couriers.edit', $courier) }}" class="text-forest-600 font-medium hover:text-forest-700 mr-3">Ubah</a>
                            <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kurir ini?')">
                                @csrf @method('DELETE')
                                <button class="text-wine-500 font-medium hover:text-wine-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-ink-muted">Belum ada kurir. Tambahkan lewat form di samping.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
