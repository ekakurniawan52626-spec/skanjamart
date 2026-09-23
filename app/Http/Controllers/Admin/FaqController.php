<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = Faq::query()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->category))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . addcslashes($request->q, '%_\\') . '%';
                $q->where(fn ($w) => $w->where('question', 'like', $term)->orWhere('answer', 'like', $term)->orWhere('keywords', 'like', $term));
            })
            ->orderBy('category')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        // Pertanyaan yang belum bisa dijawab bot (dikelompokkan biar tidak dobel).
        $unanswered = ChatLog::query()
            ->where('answered', false)
            ->selectRaw('question, count(*) as total, max(created_at) as last_at')
            ->groupBy('question')
            ->orderByDesc('last_at')
            ->limit(15)
            ->get();

        $stats = [
            'faqs' => Faq::count(),
            'chats_7d' => ChatLog::where('created_at', '>=', now()->subDays(7))->count(),
            'unanswered_7d' => ChatLog::where('answered', false)->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.chatbot.index', compact('faqs', 'unanswered', 'stats'));
    }

    public function create(Request $request)
    {
        // Dari daftar "belum terjawab": ?question=... mengisi kolom pertanyaan otomatis.
        $faq = new Faq([
            'category' => 'umum',
            'question' => (string) $request->query('question', ''),
            'is_active' => true,
        ]);

        return view('admin.chatbot.form', compact('faq'));
    }

    public function store(Request $request)
    {
        Faq::create($this->validated($request));

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ ditambahkan. Bot langsung memakainya.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.chatbot.form', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $faq->update($this->validated($request));

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', 'FAQ dihapus.');
    }

    /** Sembunyikan satu pertanyaan dari daftar "belum terjawab". */
    public function dismiss(Request $request)
    {
        $data = $request->validate(['question' => 'required|string|max:500']);

        ChatLog::where('answered', false)->where('question', $data['question'])->delete();

        return back()->with('success', 'Pertanyaan dihapus dari daftar.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(array_keys(Faq::CATEGORIES))],
            'question' => 'required|string|max:255',
            'keywords' => 'nullable|string|max:1000',
            'answer' => 'required|string|max:3000',
            'action_label' => 'nullable|string|max:60|required_with:action_url',
            'action_url' => ['nullable', 'string', 'max:255', 'regex:/^(\/|https?:\/\/)/', 'required_with:action_label'],
        ], [
            'action_url.regex' => 'Link harus diawali "/" (halaman di toko ini) atau "https://".',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
