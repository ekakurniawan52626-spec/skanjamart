<div>
    <label class="block text-sm font-medium text-ink mb-1.5">Nama Produk</label>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required class="input-classic">
    @error('name') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>
<div>
    <label class="block text-sm font-medium text-ink mb-1.5">Kategori</label>
    <select name="category_id" class="input-classic">
        <option value="">- Tanpa Kategori -</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-medium text-ink mb-1.5">Deskripsi</label>
    <textarea name="description" rows="3" class="input-classic">{{ old('description', $product->description ?? '') }}</textarea>
</div>
<div class="grid grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium text-ink mb-1.5">Harga Jual (Rp)</label>
        <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" required min="0" class="input-classic">
        @error('price') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-ink mb-1.5">Harga Modal (Rp)</label>
        <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price ?? 0) }}" min="0" class="input-classic">
        <p class="text-xs text-ink-faint mt-1">Dipakai buat hitung keuntungan di Laporan.</p>
        @error('cost_price') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-ink mb-1.5">Stok</label>
        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? '') }}" required min="0" class="input-classic">
        @error('stock') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>
<div>
    <label class="block text-sm font-medium text-ink mb-1.5">Gambar Produk</label>
    <input type="file" name="image" accept="image/*" class="w-full text-sm text-ink-muted">
    @if(!empty($product))
        <div class="mt-2">
            <img src="{{ $product->image_url }}" class="w-20 h-20 object-cover rounded-xl">
            @unless($product->image)
                <p class="text-xs text-ink-faint mt-1">Belum upload gambar &mdash; ini foto placeholder otomatis.</p>
            @endunless
        </div>
    @endif
</div>
<label class="flex items-center gap-2 text-sm text-ink">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="rounded border-ink-faint/40 text-forest-600 focus:ring-forest-500">
    Tampilkan produk di toko
</label>
