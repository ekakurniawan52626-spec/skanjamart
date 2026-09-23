@extends('layouts.admin')
@section('title', 'Ubah Kurir')
@section('content')

<div class="max-w-lg card-soft p-6">
    <h2 class="font-display text-ink mb-5">Ubah Kurir: {{ $courier->name }}</h2>
    <form action="{{ route('admin.couriers.update', $courier) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Nama</label>
            <input type="text" name="name" value="{{ old('name', $courier->name) }}" required class="input-classic">
            @error('name') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">No. HP (aktif di WhatsApp)</label>
            <input type="text" name="phone" value="{{ old('phone', $courier->phone) }}" required class="input-classic">
            @error('phone') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Kendaraan (opsional)</label>
            <input type="text" name="vehicle" value="{{ old('vehicle', $courier->vehicle) }}" class="input-classic">
        </div>
        <label class="flex items-center gap-2 text-sm text-ink">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $courier->is_active)) class="rounded border-ink-faint/40 text-forest-600 focus:ring-forest-500">
            Kurir aktif (nonaktifkan kalau kurir sedang cuti/berhenti, tanpa menghapus riwayat)
        </label>
        <div class="flex gap-3 pt-2">
            <button class="btn-primary !py-2">Simpan</button>
            <a href="{{ route('admin.couriers.index') }}" class="btn-secondary !py-2">Batal</a>
        </div>
    </form>
</div>
@endsection
