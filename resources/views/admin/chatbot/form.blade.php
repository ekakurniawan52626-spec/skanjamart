@extends('layouts.admin')
@section('title', $faq->exists ? 'Ubah FAQ' : 'Tambah FAQ')
@section('content')

<div class="max-w-2xl card-soft p-6">
    <h2 class="font-display text-ink mb-5">{{ $faq->exists ? 'Ubah FAQ' : 'Tambah FAQ' }}</h2>

    <form action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" method="POST" class="space-y-4">
        @csrf
        @if($faq->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Kategori</label>
            <select name="category" required class="input-classic">
                @foreach(\App\Models\Faq::CATEGORIES as $key => $label)
                    <option value="{{ $key }}" @selected(old('category', $faq->category) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Pertanyaan</label>
            <input type="text" name="question" value="{{ old('question', $faq->question) }}" required class="input-classic" placeholder="Contoh: Bagaimana cara membatalkan pesanan?">
            @error('question') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Kata Kunci Tambahan (opsional, pisahkan dengan spasi/koma)</label>
            <input type="text" name="keywords" value="{{ old('keywords', $faq->keywords) }}" class="input-classic" placeholder="Contoh: batal cancel pembatalan">
            <p class="text-xs text-ink-faint mt-1">Bantu bot mengenali pertanyaan yang mirip tapi pakai kata berbeda.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-ink mb-1.5">Jawaban</label>
            <textarea name="answer" rows="6" required class="input-classic">{{ old('answer', $faq->answer) }}</textarea>
            <p class="text-xs text-ink-faint mt-1">Bisa pakai <code>{jam}</code> (jam layanan) dan <code>{kontak}</code> (ajakan menghubungi admin, otomatis kosong kalau kontak belum diisi).</p>
            @error('answer') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Label Tombol (opsional)</label>
                <input type="text" name="action_label" value="{{ old('action_label', $faq->action_label) }}" class="input-classic" placeholder="Contoh: Lihat Pesanan Saya">
            </div>
            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Link Tombol (opsional)</label>
                <input type="text" name="action_url" value="{{ old('action_url', $faq->action_url) }}" class="input-classic" placeholder="/pesanan atau https://...">
                @error('action_url') <p class="text-wine-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-ink">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq->is_active ?? true)) class="rounded border-ink-faint/40 text-forest-600 focus:ring-forest-500">
            Aktif (dipakai bot untuk menjawab)
        </label>

        <div class="flex gap-3 pt-2">
            <button class="btn-primary !py-2">Simpan</button>
            <a href="{{ route('admin.faqs.index') }}" class="btn-secondary !py-2">Batal</a>
        </div>
    </form>
</div>
@endsection
