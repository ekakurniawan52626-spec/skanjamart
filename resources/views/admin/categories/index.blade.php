@extends('layouts.admin')
@section('title', 'Kategori')
@section('content')

<div class="grid lg:grid-cols-3 gap-8">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="card-soft p-6 h-fit space-y-4">
        @csrf
        <h2 class="font-display text-ink">Tambah Kategori</h2>
        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Nama Kategori</label>
            <input type="text" name="name" required class="input-classic">
            @error('name') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <button class="btn-primary !py-2">Simpan</button>
    </form>

    <div class="lg:col-span-2 card-soft overflow-hidden h-fit">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-forest-50 text-ink-muted text-left">
                <tr><th class="px-5 py-3 font-medium">Nama</th><th class="px-5 py-3 font-medium">Jumlah Produk</th><th class="px-5 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-forest-100">
                @forelse($categories as $cat)
                    <tr>
                        <td class="px-5 py-3.5 font-medium text-ink">{{ $cat->name }}</td>
                        <td class="px-5 py-3.5 text-ink-muted">{{ $cat->products_count }}</td>
                        <td class="px-5 py-3.5 text-right">
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf @method('DELETE')
                                <button class="text-wine-500 font-medium hover:text-wine-600">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-ink-muted">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
