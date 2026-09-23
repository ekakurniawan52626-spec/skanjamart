<?php

namespace App\Services\Chatbot;

use App\Models\Category;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Support\Fmt;

/**
 * Otak chatbot customer service (tanpa layanan AI eksternal, jadi gratis dan cepat).
 *
 * Urutan: sapaan -> nomor pesanan (data asli) -> "pesanan saya" -> FAQ -> produk -> saran/fallback.
 */
class ChatbotService
{
    private const PRODUCT_CUES = [
        'harga', 'stok', 'stock', 'ada', 'adakah', 'tersedia', 'ready', 'jual', 'dijual', 'beli', 'cari', 'mencari',
        'produk', 'barang', 'rekomendasi', 'murah', 'diskon', 'promo',
    ];

    private const HOWTO_MARKERS = ['cara', 'caranya', 'bagaimana', 'gimana', 'gmn', 'bgmn', 'langkah', 'apa', 'apakah', 'arti', 'maksud'];

    private const ORDER_WORDS = ['pesanan', 'pesenan', 'order', 'orderan', 'paket', 'barang', 'belanjaan', 'pembelian'];

    /** Kata yang menandakan pelanggan menanyakan pesanannya SENDIRI. */
    private const PERSONAL_WORDS = ['saya', 'aku', 'ku', 'gue', 'gw', 'punyaku', 'belum', 'kok'];

    /** Kata "cek status" yang cukup jelas meski tanpa "saya" (mis. "cek status pesanan"). */
    private const CHECK_WORDS = ['status', 'lacak', 'melacak', 'tracking', 'track', 'dimana', 'posisi', 'cek', 'resi'];

    /** Kata pertanyaan waktu/posisi yang hanya dianggap pertanyaan pribadi kalau ada "saya"/"belum". */
    private const TRACK_WORDS = [
        'status', 'lacak', 'melacak', 'tracking', 'track', 'dimana', 'posisi', 'sampai', 'nyampe', 'tiba', 'kapan', 'resi',
        'cek', 'proses', 'diproses', 'dikirim', 'kirim', 'kurir', 'belum', 'lama',
    ];

    private const GREETINGS = [
        'halo', 'hai', 'hi', 'hei', 'hey', 'hello', 'p', 'permisi', 'assalamualaikum', 'salam', 'selamat', 'pagi', 'siang', 'sore', 'malam', 'kak', 'min', 'test', 'tes',
    ];

    public function __construct(private FaqMatcher $matcher = new FaqMatcher())
    {
    }

    /**
     * @return array{reply: string, intent: string, answered: bool, faq_id: ?int, links: array, suggestions: array}
     */
    public function reply(string $message, ?User $user = null): array
    {
        $message = trim((string) preg_replace('/\s+/u', ' ', $message));
        $message = mb_substr($message, 0, 300);

        if ($message === '') {
            return $this->result('Tulis pertanyaanmu di kolom bawah ya, nanti aku bantu.', 'smalltalk', suggestions: $this->popularQuestions());
        }

        $words = Text::words($message);
        $wordCount = count($words);

        // 1. Sapaan, terima kasih, pamit
        if ($small = $this->smallTalk($message, $words)) {
            return $small;
        }

        // 2. Nomor pesanan di pesan -> cek data asli
        if (preg_match('/\bORD-[A-Z0-9]{6,}\b/i', $message, $m)) {
            return $this->orderByNumber(strtoupper($m[0]), $user);
        }

        // 3. "pesanan saya sampai mana?" (bukan pertanyaan cara/penjelasan)
        $orderIntent = $this->myOrderIntent($words);
        if ($orderIntent === 'personal' || ($orderIntent === 'check' && $user)) {
            return $this->myOrders($user);
        }

        // 4. FAQ
        $faqs = Faq::where('is_active', true)->get();
        $ranked = $this->matcher->rank($faqs, $message);
        $best = $ranked[0] ?? null;

        if ($best && $best['score'] >= FaqMatcher::STRONG) {
            return $this->faqAnswer($best['faq'], $ranked);
        }

        // 5. Produk (harga / stok / ketersediaan)
        if ($product = $this->productAnswer($message, $words)) {
            return $product;
        }

        // 6. FAQ yang kecocokannya lebih lemah
        if ($best && $best['score'] >= FaqMatcher::WEAK) {
            return $this->faqAnswer($best['faq'], $ranked, weak: true);
        }

        return $this->fallback();
    }

    // ------------------------------------------------------------------ Sapaan

    private function smallTalk(string $message, array $words): ?array
    {
        $count = count($words);
        $lower = mb_strtolower($message);

        if ($count <= 6 && preg_match('/\b(terima ?kasih|makasih|mksh|thanks|thank you|thx|trims|tengkyu)\b/u', $lower)) {
            return $this->result('Sama-sama! Kalau ada yang mau ditanyakan lagi, aku di sini ya. 😊', 'smalltalk', suggestions: $this->popularQuestions(3));
        }

        if ($count <= 4 && preg_match('/\b(dadah|bye|sampai jumpa|daaah)\b/u', $lower)) {
            return $this->result('Sampai jumpa! Selamat berbelanja di SKANJAMart. 👋', 'smalltalk');
        }

        if ($count <= 4 && $count > 0 && count(array_diff($words, self::GREETINGS)) === 0) {
            return $this->result(
                "Halo! Aku asisten SKANJAMart. Aku bisa bantu soal pemesanan, pembayaran, pelacakan pesanan, dan cek produk. Mau tanya apa?",
                'smalltalk',
                suggestions: $this->popularQuestions()
            );
        }

        return null;
    }

    // ------------------------------------------------------------- Data pesanan

    /**
     * 'personal' = jelas menanyakan pesanan sendiri ("pesanan saya belum sampai")
     * 'check'    = perintah cek status ("cek status pesanan"); hanya dijawab dengan data kalau sudah login
     * null       = bukan pertanyaan data pesanan (mis. "bagaimana cara melacak pesanan?" -> FAQ)
     */
    private function myOrderIntent(array $words): ?string
    {
        if (count(array_intersect($words, self::ORDER_WORDS)) === 0) {
            return null;
        }

        $asksHow = count(array_intersect($words, self::HOWTO_MARKERS)) > 0;

        if (! $asksHow && count(array_intersect($words, self::PERSONAL_WORDS)) > 0
            && count(array_intersect($words, self::TRACK_WORDS)) > 0) {
            return 'personal';
        }

        if (! $asksHow && count(array_intersect($words, self::CHECK_WORDS)) > 0) {
            return 'check';
        }

        return null;
    }

    private function needLogin(): array
    {
        return $this->result(
            'Supaya data pesananmu aman, aku hanya bisa mengecek pesanan setelah kamu masuk ke akun. Silakan masuk dulu, lalu tanya lagi ya.',
            'order',
            links: [['label' => 'Masuk', 'url' => route('login', [], false)]],
        );
    }

    private function orderByNumber(string $number, ?User $user): array
    {
        if (! $user) {
            return $this->needLogin();
        }

        $order = Order::with('delivery.courier')
            ->where('order_number', $number)
            ->where('user_id', $user->id)
            ->first();

        if (! $order) {
            return $this->result(
                "Aku tidak menemukan pesanan $number di akunmu. Coba periksa lagi nomornya (format ORD- diikuti huruf/angka), atau lihat daftar pesananmu.",
                'order',
                links: [['label' => 'Lihat Pesanan Saya', 'url' => route('orders.index', [], false)]],
                answered: false,
            );
        }

        return $this->result($this->describeOrder($order), 'order', links: [
            ['label' => 'Buka detail & peta', 'url' => route('orders.show', $order, false)],
        ]);
    }

    private function myOrders(?User $user): array
    {
        if (! $user) {
            return $this->needLogin();
        }

        $orders = Order::with('delivery.courier')->where('user_id', $user->id)->latest()->limit(3)->get();

        if ($orders->isEmpty()) {
            return $this->result(
                'Kamu belum punya pesanan. Yuk mulai belanja!',
                'order',
                links: [['label' => 'Ke Beranda', 'url' => route('home', [], false)]],
            );
        }

        $text = $orders->count() === 1
            ? "Ini pesananmu:\n\n"
            : "Ini {$orders->count()} pesanan terakhirmu:\n\n";
        $text .= $orders->map(fn (Order $o) => $this->describeOrder($o))->implode("\n\n");

        $links = $orders->map(fn (Order $o) => [
            'label' => 'Detail ' . $o->order_number,
            'url' => route('orders.show', $o, false),
        ])->all();

        return $this->result($text, 'order', links: $links);
    }

    private function describeOrder(Order $order): string
    {
        $lines = [
            "Pesanan {$order->order_number}",
            'Status: ' . $order->status_label . ' | Pembayaran: ' . $order->payment_status_label,
        ];

        $delivery = $order->delivery;

        if ($order->status === 'cancelled') {
            $lines[] = 'Pesanan ini sudah dibatalkan.';
        } elseif ($delivery) {
            $lines[] = 'Kurir: ' . ($delivery->courier?->name ?? '-') . ' (' . $delivery->status_label . ')';

            if ($delivery->status === 'on_the_way') {
                if ($delivery->last_ping_at) {
                    $lines[] = 'Posisi kurir diperbarui ' . Fmt::ago($delivery->last_ping_at) . '.';
                }
                if (($km = $delivery->distanceKm()) !== null) {
                    $lines[] = 'Jarak kurir ke tujuan sekitar ' . number_format($km, 1, ',', '.') . ' km (garis lurus).';
                }
            }

            $lines[] = 'Perkiraan tiba: ' . $delivery->etaText();
        } elseif ($order->status === 'completed') {
            $lines[] = 'Pesanan sudah selesai.';
        } elseif ($order->status === 'shipped') {
            $lines[] = 'Pesananmu sedang dalam pengiriman.';
        } elseif ($order->payment_method === 'midtrans' && $order->payment_status === 'unpaid') {
            $lines[] = 'Pesanan ini belum dibayar. Selesaikan lewat tombol "Lanjutkan Pembayaran" di detail pesanan.';
        } else {
            $lines[] = 'Pesananmu sedang disiapkan. Kurir belum ditugaskan, jadi perkiraan tiba belum tersedia.';
        }

        return implode("\n", $lines);
    }

    // --------------------------------------------------------------------- FAQ

    private function faqAnswer(Faq $faq, array $ranked, bool $weak = false): array
    {
        Faq::whereKey($faq->id)->increment('hits');

        $links = $this->contactLinksIfMentioned($faq->answer);
        if ($faq->action_label && $faq->action_url) {
            array_unshift($links, ['label' => $faq->action_label, 'url' => $faq->action_url]);
        }

        $related = collect($ranked)
            ->filter(fn ($r) => $r['faq']->id !== $faq->id && $r['score'] >= FaqMatcher::WEAK)
            ->take(2)
            ->map(fn ($r) => $r['faq']->question)
            ->values()
            ->all();

        $answer = $this->renderAnswer($faq->answer);

        if ($weak) {
            $answer = "Mungkin ini yang kamu maksud:\n\n" . $answer;
        }

        return $this->result($answer, 'faq', faqId: $faq->id, links: $links, suggestions: $related);
    }

    /** Ganti placeholder {jam} dan {kontak}. */
    private function renderAnswer(string $answer): string
    {
        $contact = config('skanjamart.contact');
        $hasContact = ! empty($contact['whatsapp']) || ! empty($contact['email']);

        $answer = str_replace('{jam}', (string) ($contact['hours'] ?? ''), $answer);
        $answer = str_replace('{kontak}', $hasContact ? 'Kamu bisa langsung menghubungi admin lewat tombol di bawah.' : '', $answer);

        return trim($answer);
    }

    /** Tombol WhatsApp/email hanya ditampilkan kalau jawabannya memang menyuruh menghubungi admin. */
    private function contactLinksIfMentioned(string $rawAnswer): array
    {
        if (! str_contains($rawAnswer, '{kontak}')) {
            return [];
        }

        return $this->contactLinks();
    }

    private function contactLinks(): array
    {
        $contact = config('skanjamart.contact');
        $links = [];

        if (! empty($contact['whatsapp'])) {
            $number = preg_replace('/\D+/', '', (string) $contact['whatsapp']);
            $links[] = ['label' => 'Chat WhatsApp Admin', 'url' => 'https://wa.me/' . $number];
        }

        if (! empty($contact['email'])) {
            $links[] = ['label' => 'Kirim Email', 'url' => 'mailto:' . $contact['email']];
        }

        return $links;
    }

    // ------------------------------------------------------------------ Produk

    private function productAnswer(string $message, array $words): ?array
    {
        $hasCue = count(array_intersect($words, self::PRODUCT_CUES)) > 0;

        $terms = array_values(array_filter(
            Text::keywords($message),
            fn ($w) => mb_strlen($w) >= 3 && ! in_array($w, self::PRODUCT_CUES, true)
        ));

        if ($terms === []) {
            return null;
        }

        $terms = array_slice($terms, 0, 5);

        $candidates = Product::query()
            ->where('is_active', true)
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->orWhere('name', 'like', '%' . addcslashes($term, '%_\\') . '%');
                }
            })
            ->limit(40)
            ->get();

        $scored = $candidates->map(function (Product $p) use ($terms) {
            $name = mb_strtolower($p->name);
            $hits = collect($terms)->filter(fn ($t) => str_contains($name, $t))->count();

            return ['product' => $p, 'hits' => $hits];
        })->sortByDesc('hits')->values();

        $top = $scored->first();

        // Tanpa kata isyarat ("harga", "stok", ...), hanya jawab kalau semua kata cocok dengan nama produk.
        if ($top && ($hasCue || $top['hits'] === count($terms))) {
            $products = $scored->where('hits', $top['hits'])->take(3)->pluck('product');

            $lines = $products->map(fn (Product $p) => '• ' . $p->name . ' - Rp' . number_format($p->price, 0, ',', '.') . ' (' . ($p->stock > 0 ? 'stok ' . $p->stock : 'stok habis') . ')');

            $text = "Ini yang aku temukan:\n" . $lines->implode("\n");

            $links = $products->map(fn (Product $p) => [
                'label' => $p->name,
                'url' => route('products.show', $p, false),
            ])->all();

            return $this->result($text, 'product', links: $links);
        }

        // Nama kategori?
        if ($hasCue) {
            $category = Category::query()
                ->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->orWhere('name', 'like', '%' . addcslashes($term, '%_\\') . '%');
                    }
                })
                ->first();

            if ($category) {
                return $this->result(
                    "Ada kategori {$category->name} di SKANJAMart. Lihat semua produknya di sini:",
                    'product',
                    links: [['label' => 'Lihat kategori ' . $category->name, 'url' => route('home', ['category' => $category->id], false)]],
                );
            }

            return $this->result(
                'Aku belum menemukan produk itu di toko. Coba pakai kata lain, atau cari lewat kolom pencarian di beranda.',
                'product',
                links: [['label' => 'Ke Beranda', 'url' => route('home', [], false)]],
                answered: false,
            );
        }

        return null;
    }

    // ---------------------------------------------------------------- Fallback

    private function fallback(): array
    {
        $links = $this->contactLinks();

        $text = 'Maaf, aku belum punya jawaban untuk itu. Coba tulis dengan kata lain, atau pilih salah satu pertanyaan di bawah.';

        if ($links !== []) {
            $text .= ' Kalau masih belum terjawab, admin siap membantu lewat tombol di bawah.';
        }

        return $this->result($text, 'fallback', links: $links, suggestions: $this->popularQuestions(), answered: false);
    }

    /** @return array<int, string> */
    private function popularQuestions(int $limit = 4): array
    {
        return Faq::where('is_active', true)
            ->orderByDesc('hits')
            ->orderBy('id')
            ->limit($limit)
            ->pluck('question')
            ->all();
    }

    private function result(
        string $reply,
        string $intent,
        ?int $faqId = null,
        array $links = [],
        array $suggestions = [],
        bool $answered = true,
    ): array {
        return [
            'reply' => $reply,
            'intent' => $intent,
            'answered' => $answered,
            'faq_id' => $faqId,
            'links' => $links,
            'suggestions' => $suggestions,
        ];
    }
}
