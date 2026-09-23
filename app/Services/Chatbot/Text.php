<?php

namespace App\Services\Chatbot;

/**
 * Pembersih teks untuk chatbot: huruf kecil, buang tanda baca & kata umum,
 * lalu samakan kata yang searti (mis. "bayar", "pembayaran", "gopay" => "bayar").
 */
final class Text
{
    /** Kata yang tidak membawa makna untuk pencocokan. */
    private const STOPWORDS = [
        'apa', 'apakah', 'bagaimana', 'gimana', 'gmn', 'bgmn', 'bagaimanakah', 'cara', 'caranya', 'kah',
        'yang', 'di', 'ke', 'dari', 'dan', 'atau', 'dengan', 'untuk', 'buat', 'pada', 'dalam', 'oleh',
        'bisa', 'dapat', 'boleh', 'ya', 'yaa', 'dong', 'deh', 'sih', 'lah', 'nih', 'tuh', 'aja', 'saja', 'kok', 'kan',
        'itu', 'ini', 'saya', 'aku', 'gue', 'gw', 'ku', 'kamu', 'anda', 'kak', 'kakak', 'min', 'mas', 'mbak', 'bang',
        'mau', 'ingin', 'pengen', 'pingin', 'minta', 'tolong', 'mohon', 'tanya', 'nanya', 'bertanya',
        'kalau', 'kalo', 'jika', 'bila', 'apabila', 'agar', 'supaya', 'sudah', 'udah', 'sdh', 'lagi', 'sedang',
        'akan', 'pakai', 'pake', 'menggunakan', 'gunakan', 'toko', 'skanjamart', 'skanja', 'mart', 'website', 'web', 'situs',
        'aplikasi', 'app', 'berapa', 'seperti', 'kayak', 'apa', 'ada', 'adakah', 'sama', 'juga', 'pun', 'nya', 'lalu',
        'kenapa', 'mengapa', 'knp', 'kapankah', 'siapa', 'mana', 'nih', 'ya', 'pak', 'bu', 'ibu', 'bapak', 'sebuah', 'suatu',
        'bagi', 'tentang', 'soal', 'mohon', 'info', 'informasi', 'jelaskan', 'jelasin', 'kasih', 'tahu', 'tau',
    ];

    /** Frasa yang disatukan jadi satu kata sebelum dipecah. */
    private const PHRASES = [
        'kata sandi' => 'password',
        'lupa sandi' => 'lupa password',
        'cash on delivery' => 'cod',
        'bayar di tempat' => 'cod',
        'bayar ditempat' => 'cod',
        'biaya kirim' => 'ongkir',
        'ongkos kirim' => 'ongkir',
        'biaya pengiriman' => 'ongkir',
        'uang kembali' => 'refund',
        'e wallet' => 'ewallet',
        'virtual account' => 'va',
        'customer service' => 'cs',
        'jam buka' => 'jam',
        'jam operasional' => 'jam',
        'jam kerja' => 'jam',
        'sampai mana' => 'lacak',
        'di mana' => 'dimana',
        'berapa lama' => 'lama',
        'berapa hari' => 'lama',
        'ke mana' => 'dimana',
        'kirim ulang' => 'kirim',
        'tidak bisa' => 'gagal',
        'gak bisa' => 'gagal',
        'ga bisa' => 'gagal',
        'nggak bisa' => 'gagal',
        'tidak muncul' => 'gagal',
        'gak muncul' => 'gagal',
        'error' => 'gagal',
    ];

    /** Kelompok kata searti. Kata pertama = bentuk baku. */
    private const GROUPS = [
        'pesan' => ['pesan', 'pesanan', 'pemesanan', 'memesan', 'dipesan', 'order', 'orderan', 'pesen', 'mesan'],
        'bayar' => ['bayar', 'bayaran', 'pembayaran', 'membayar', 'dibayar', 'bayarnya', 'transfer', 'tf', 'va', 'qris',
            'gopay', 'ovo', 'dana', 'shopeepay', 'linkaja', 'midtrans', 'kartu', 'kredit', 'debit', 'ewallet', 'wallet',
            'minimarket', 'indomaret', 'alfamart', 'bank', 'bca', 'bni', 'bri', 'mandiri', 'permata', 'lunas', 'tagihan', 'invoice'],
        'cod' => ['cod', 'tunai', 'cash'],
        'kirim' => ['kirim', 'kirimkan', 'dikirim', 'mengirim', 'pengiriman', 'ekspedisi', 'kurir', 'shipping', 'delivery',
            'antar', 'diantar', 'mengantar', 'pengantaran', 'paket', 'jne', 'jnt', 'sicepat'],
        'ongkir' => ['ongkir', 'ongkos'],
        'lacak' => ['lacak', 'melacak', 'pelacakan', 'tracking', 'track', 'resi', 'posisi', 'lokasi', 'dimana', 'nyampe', 'tiba',
            'estimasi', 'perkiraan', 'sampai', 'lama', 'durasi'],
        'batal' => ['batal', 'batalkan', 'pembatalan', 'membatalkan', 'dibatalkan', 'cancel'],
        'retur' => ['retur', 'return', 'kembalikan', 'pengembalian', 'refund', 'tukar', 'penukaran', 'rusak', 'cacat',
            'komplain', 'keluhan', 'garansi', 'salah', 'kurang', 'pecah'],
        'stok' => ['stok', 'stock', 'persediaan', 'tersedia', 'ready', 'habis', 'kosong', 'restock'],
        'harga' => ['harga', 'murah', 'mahal', 'diskon', 'promo', 'potongan', 'voucher', 'kupon'],
        'akun' => ['akun', 'account', 'daftar', 'registrasi', 'register', 'mendaftar', 'login', 'masuk', 'logout', 'keluar', 'profil', 'profile'],
        'password' => ['password', 'sandi', 'katasandi'],
        'alamat' => ['alamat', 'address'],
        'kontak' => ['kontak', 'hubungi', 'menghubungi', 'cs', 'admin', 'customer', 'wa', 'whatsapp', 'telepon', 'telp', 'email', 'bantuan', 'help', 'bantu'],
        'jam' => ['jam', 'buka', 'tutup', 'operasional', 'libur'],
        'aman' => ['aman', 'keamanan', 'penipuan', 'terpercaya', 'privasi'],
        'keranjang' => ['keranjang', 'cart', 'troli'],
        'produk' => ['produk', 'barang', 'item', 'jual', 'dijual'],
        'cari' => ['cari', 'mencari', 'pencarian', 'search'],
        'status' => ['status', 'proses', 'diproses', 'pemrosesan'],
        'beli' => ['beli', 'membeli', 'belanja', 'berbelanja', 'dibeli', 'checkout'],
        'ubah' => ['ubah', 'mengubah', 'edit', 'ganti', 'mengganti', 'update', 'perbarui'],
        'hapus' => ['hapus', 'menghapus', 'delete'],
        'tidak' => ['tidak', 'gak', 'ga', 'nggak', 'enggak', 'tak', 'tdk', 'belum'],
        'gagal' => ['gagal', 'masalah', 'kendala', 'trouble'],
    ];

    private static ?array $canon = null;
    private static ?array $stop = null;

    /** Kata mentah (huruf kecil, tanpa tanda baca), stopword belum dibuang. */
    public static function words(string $text): array
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text);
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if ($text === '') {
            return [];
        }

        $text = ' ' . $text . ' ';
        foreach (self::PHRASES as $phrase => $replacement) {
            $text = str_replace(' ' . $phrase . ' ', ' ' . $replacement . ' ', $text);
        }

        return array_values(array_filter(explode(' ', trim($text)), fn ($w) => $w !== ''));
    }

    /** Kata penting yang sudah dibakukan (untuk mencocokkan pertanyaan dengan FAQ). */
    public static function tokens(string $text): array
    {
        $stop = self::stopSet();
        $canon = self::canonMap();
        $out = [];

        foreach (self::words($text) as $word) {
            if (isset($stop[$word])) {
                continue;
            }

            $out[] = $canon[$word] ?? self::stem($word);
        }

        return array_values(array_unique($out));
    }

    /** Kata mentah tanpa stopword (dipakai untuk mencari nama produk). */
    public static function keywords(string $text): array
    {
        $stop = self::stopSet();

        return array_values(array_unique(array_filter(
            self::words($text),
            fn ($w) => ! isset($stop[$w]) && mb_strlen($w) >= 2
        )));
    }

    private static function stem(string $word): string
    {
        // Pengupasan ringan saja: imbuhan lengkap sengaja tidak dipakai supaya tidak salah kupas.
        foreach (['nya', 'kah', 'lah'] as $suffix) {
            if (mb_strlen($word) > mb_strlen($suffix) + 3 && str_ends_with($word, $suffix)) {
                return mb_substr($word, 0, -mb_strlen($suffix));
            }
        }

        return $word;
    }

    private static function stopSet(): array
    {
        return self::$stop ??= array_fill_keys(self::STOPWORDS, true);
    }

    private static function canonMap(): array
    {
        if (self::$canon === null) {
            self::$canon = [];
            foreach (self::GROUPS as $canonical => $variants) {
                foreach ($variants as $variant) {
                    self::$canon[$variant] = $canonical;
                }
            }
        }

        return self::$canon;
    }
}
