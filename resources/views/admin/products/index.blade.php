@extends('layouts.admin')
@section('title', 'Produk')
@section('content')

<div class="flex justify-between items-center mb-5">
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="input-classic text-sm py-2">
        <button class="text-sm text-forest-600 font-medium hover:text-forest-700">Cari</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn-primary !py-2">+ Tambah Produk</a>
</div>

<div class="card-soft overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-forest-50 text-ink-muted text-left">
            <tr><th class="px-5 py-3 font-medium">Produk</th><th class="px-5 py-3 font-medium">Kategori</th><th class="px-5 py-3 font-medium">Harga</th><th class="px-5 py-3 font-medium">Stok</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-forest-100">
            @forelse($products as $product)
                <tr>
                    <td class="px-5 py-3.5 font-medium text-ink">{{ $product->name }}</td>
                    <td class="px-5 py-3.5 text-ink-muted">{{ $product->category->name ?? '-' }}</td>
                    <td class="px-5 py-3.5"><span class="price-tag text-sm">Rp{{ number_format($product->price, 0, ',', '.') }}</span></td>
                    <td class="px-5 py-3.5 text-ink">{{ $product->stock }}</td>
                    <td class="px-5 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-xs {{ $product->is_active ? 'bg-forest-50 text-forest-700' : 'bg-ink-faint/10 text-ink-faint' }}">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right space-x-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-forest-600 font-medium hover:text-forest-700">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button class="text-wine-500 font-medium hover:text-wine-600">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-ink-muted">Belum ada produk.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-6">{{ $products->links() }}</div>
@endsection
