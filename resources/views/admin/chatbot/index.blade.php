@extends('layouts.admin')
@section('title', 'Chatbot Customer Service')
@section('content')

<div class="grid sm:grid-cols-3 gap-4 mb-6">
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-1 !text-ink-faint">Total FAQ</p>
        <p class="font-display font-medium text-2xl text-ink">{{ $stats['faqs'] }}</p>
    </div>
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-1 !text-ink-faint">Chat 7 Hari Terakhir</p>
        <p class="font-display font-medium text-2xl text-ink">{{ $stats['chats_7d'] }}</p>
    </div>
    <div class="card-soft p-5">
        <p class="section-eyebrow mb-1 !text-ink-faint">Belum Terjawab (7 Hari)</p>
        <p class="font-display font-medium text-2xl text-brass-500">{{ $stats['unanswered_7d'] }}</p>
    </div>
</div>

@if($unanswered->isNotEmpty())
    <div class="card-soft p-5 mb-6">
        <h3 class="section-eyebrow mb-3">Pertanyaan yang Belum Bisa Dijawab Bot</h3>
        <p class="text-xs text-ink-faint mb-3">Tambahkan FAQ baru dari pertanyaan ini supaya bot bisa menjawabnya lain kali.</p>
        <ul class="divide-y divide-forest-100 text-sm">
            @foreach($unanswered as $row)
                <li class="py-2.5 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-ink truncate">{{ $row->question }}</p>
                        <p class="text-xs text-ink-faint">{{ $row->total }}x ditanyakan · terakhir {{ \App\Support\Fmt::ago($row->last_at) }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.faqs.create', ['question' => $row->question]) }}" class="text-forest-600 font-medium hover:text-forest-700 text-sm">Buat FAQ</a>
                        <form action="{{ route('admin.faqs.dismiss') }}" method="POST" onsubmit="return confirm('Hapus dari daftar ini?')">
                            @csrf @method('DELETE')
                            <input type="hidden" name="question" value="{{ $row->question }}">
                            <button class="text-ink-faint hover:text-wine-500 text-sm">Abaikan</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <form action="{{ route('admin.faqs.index') }}" method="GET" class="flex flex-wrap gap-2">
        <select name="category" onchange="this.form.submit()" class="input-classic text-sm py-2 w-auto">
            <option value="">Semua Kategori</option>
            @foreach(\App\Models\Faq::CATEGORIES as $key => $label)
                <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari FAQ..." class="input-classic text-sm py-2 w-auto">
        <button class="btn-secondary !py-2 !px-4 text-sm">Cari</button>
    </form>
    <a href="{{ route('admin.faqs.create') }}" class="btn-primary !py-2">+ Tambah FAQ</a>
</div>

<div class="card-soft overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-forest-50 text-ink-muted text-left">
            <tr>
                <th class="px-5 py-3 font-medium">Kategori</th>
                <th class="px-5 py-3 font-medium">Pertanyaan</th>
                <th class="px-5 py-3 font-medium">Dipakai</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-forest-100">
            @forelse($faqs as $faq)
                <tr>
                    <td class="px-5 py-3.5 text-ink-muted whitespace-nowrap">{{ $faq->category_label }}</td>
                    <td class="px-5 py-3.5 text-ink font-medium">{{ $faq->question }}</td>
                    <td class="px-5 py-3.5 text-ink-muted">{{ $faq->hits }}x</td>
                    <td class="px-5 py-3.5">
                        @if($faq->is_active)
                            <span class="pill pill-active !py-0.5 !px-2.5 text-xs">Aktif</span>
                        @else
                            <span class="pill pill-inactive !py-0.5 !px-2.5 text-xs">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-forest-600 font-medium hover:text-forest-700 mr-3">Ubah</a>
                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('Hapus FAQ ini?')">
                            @csrf @method('DELETE')
                            <button class="text-wine-500 font-medium hover:text-wine-600">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-ink-muted">Belum ada FAQ yang cocok.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-6">{{ $faqs->links() }}</div>
@endsection
