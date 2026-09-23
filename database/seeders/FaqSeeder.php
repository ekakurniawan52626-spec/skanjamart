<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Isi awal chatbot customer service.
 *
 * Aman dijalankan berulang: FAQ dicocokkan lewat teks pertanyaannya, jadi tidak dobel
 * dan jawaban yang sudah kamu ubah lewat Admin tidak ditimpa.
 * PENTING: bagian kebijakan toko (pembatalan, retur, ongkos kirim, jam layanan) ditulis
 * secara umum. Sesuaikan lewat Admin > Chatbot supaya cocok dengan aturan tokomu.
 *
 * Placeholder di jawaban:
 *   {jam}    -> jam layanan dari STORE_HOURS (.env)
 *   {kontak} -> kalimat kontak (WhatsApp/email) kalau diisi di .env; kosong kalau belum
 */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::defaults() as $faq) {
            // firstOrCreate: FAQ yang sudah pernah kamu edit lewat Admin tidak ditimpa lagi.
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                $faq + ['is_active' => true]
            );
        }
    }

    /** @return array<int, array<string, mixed>> */
    public static function defaults(): array
    {
        return [

            // ---------------------------------------------------------------- Pemesanan
            [
                'category' => 'pemesanan',
                'question' => 'Bagaimana cara memesan produk?',
                'keywords' => 'cara beli belanja order checkout pesan langkah pemesanan',
                'answer' => "Gampang kok:\n1. Pilih produk di beranda (atau cari lewat kolom pencarian).\n2. Klik \"Tambah ke Keranjang\".\n3. Buka Keranjang, cek jumlahnya, lalu klik \"Lanjut ke Checkout\".\n4. Isi alamat lengkap dan nomor HP yang aktif.\n5. Pilih metode pembayaran (Bayar Online lewat Midtrans atau Bayar di Tempat/COD), lalu selesaikan pesanan.\n\nUntuk checkout kamu perlu masuk (login) dulu.",
                'action_label' => 'Buka Keranjang',
                'action_url' => '/keranjang',
            ],
            [
                'category' => 'pemesanan',
                'question' => 'Apakah harus punya akun untuk berbelanja?',
                'keywords' => 'wajib daftar login akun tamu guest tanpa akun',
                'answer' => "Melihat-lihat produk bisa tanpa akun. Tapi untuk memasukkan produk ke keranjang dan checkout, kamu perlu daftar atau masuk dulu supaya pesananmu tersimpan dan bisa dilacak.",
                'action_label' => 'Daftar Akun',
                'action_url' => '/register',
            ],
            [
                'category' => 'pemesanan',
                'question' => 'Bagaimana cara melihat riwayat dan status pesanan?',
                'keywords' => 'riwayat pesanan saya daftar pesanan cek status pesanan lihat pesanan',
                'answer' => "Masuk ke akunmu, lalu buka menu \"Pesanan\". Di sana ada semua pesananmu. Klik \"Detail\" untuk melihat status tiap tahap, kurir, dan perkiraan tiba.\n\nKamu juga bisa ketik nomor pesananmu di chat ini (contoh: ORD-ABC123XYZ0), nanti aku cek langsung.",
                'action_label' => 'Lihat Pesanan Saya',
                'action_url' => '/pesanan',
            ],
            [
                'category' => 'pemesanan',
                'question' => 'Bagaimana cara mengubah alamat atau nomor HP pada pesanan?',
                'keywords' => 'salah alamat ganti alamat ubah nomor hp pesanan sudah dibuat edit alamat pengiriman',
                'answer' => "Saat ini alamat dan nomor HP pada pesanan yang sudah dibuat diubah oleh admin. Hubungi kami secepatnya sebelum pesanan dikirim, sebutkan nomor pesanan (ORD-...) beserta alamat/nomor yang benar. {kontak}",
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'category' => 'pemesanan',
                'question' => 'Produk yang aku mau stoknya habis, kapan tersedia lagi?',
                'keywords' => 'stok habis kosong restock tersedia lagi ready kapan',
                'answer' => "Stok yang tampil di halaman produk adalah stok saat ini dan berkurang otomatis setiap ada pesanan. Kalau tertulis \"Habis\", produk belum bisa dipesan. Coba cek lagi beberapa hari lagi, atau tanyakan ke admin soal jadwal restock. {kontak}",
                'action_label' => null,
                'action_url' => null,
            ],

            // -------------------------------------------------------------- Pembayaran
            [
                'category' => 'pembayaran',
                'question' => 'Metode pembayaran apa saja yang tersedia?',
                'keywords' => 'pembayaran bayar kartu va virtual account ewallet gopay ovo dana qris transfer bank cod',
                'answer' => "Ada dua pilihan saat checkout:\n1. Bayar Online lewat Midtrans (kartu, virtual account/transfer bank, atau e-wallet, sesuai pilihan yang muncul di jendela pembayaran).\n2. Bayar di Tempat (COD), bayar tunai ke kurir saat barang tiba.",
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'category' => 'pembayaran',
                'question' => 'Bagaimana cara membayar online lewat Midtrans?',
                'keywords' => 'midtrans bayar online kartu va transfer ewallet cara bayar snap',
                'answer' => "1. Saat checkout, pilih \"Bayar Online (Kartu / VA / E-wallet - Midtrans)\".\n2. Setelah pesanan dibuat, klik \"Bayar Sekarang\".\n3. Pilih metode yang kamu mau di jendela pembayaran, lalu ikuti petunjuknya.\n\nStatus pesanan akan diperbarui otomatis setelah pembayaran berhasil.",
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'category' => 'pembayaran',
                'question' => 'Bagaimana cara bayar di tempat (COD)?',
                'keywords' => 'cod cash on delivery tunai bayar ditempat bayar saat barang tiba',
                'answer' => "Pilih \"Bayar di Tempat (COD)\" saat checkout. Kamu bayar tunai langsung ke kurir saat barang tiba, jadi tidak perlu bayar di muka. Siapkan uang pas ya, dan pastikan nomor HP aktif supaya kurir mudah menghubungi.",
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'category' => 'pembayaran',
                'question' => 'Aku sudah bayar tapi status pesanan masih belum dibayar',
                'keywords' => 'sudah transfer status belum berubah belum lunas pembayaran belum terkonfirmasi bukti bayar',
                'answer' => "Konfirmasi dari Midtrans biasanya masuk dalam beberapa menit. Tunggu sebentar, lalu muat ulang halaman Pesanan.\n\nKalau lebih dari 1 jam status belum berubah, hubungi admin dan sebutkan nomor pesanan beserta bukti pembayarannya. {kontak}",
                'action_label' => 'Lihat Pesanan Saya',
                'action_url' => '/pesanan',
            ],
            [
                'category' => 'pembayaran',
                'question' => 'Pembayaran gagal atau jendela pembayaran tertutup, bagaimana?',
                'keywords' => 'gagal bayar popup tertutup expired kedaluwarsa lanjutkan pembayaran bayar ulang',
                'answer' => "Tenang, pesananmu masih tersimpan. Buka \"Pesanan\", masuk ke detail pesanan yang belum dibayar, lalu klik \"Lanjutkan Pembayaran\".\n\nKalau batas waktu pembayaran sudah habis, pesanan bisa otomatis berstatus Dibatalkan. Dalam kasus itu, silakan buat pesanan baru.",
                'action_label' => 'Lihat Pesanan Saya',
                'action_url' => '/pesanan',
            ],
            [
                'category' => 'pembayaran',
                'question' => 'Apakah pembayaran di SKANJAMart aman?',
                'keywords' => 'aman keamanan penipuan data kartu privasi terpercaya otp pin',
                'answer' => "Pembayaran online diproses oleh Midtrans, jadi data kartu atau akun pembayaranmu tidak disimpan di toko kami. Password akunmu juga disimpan dalam bentuk terenkripsi.\n\nJangan pernah membagikan PIN atau kode OTP ke siapa pun, termasuk yang mengaku sebagai admin.",
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'category' => 'pembayaran',
                'question' => 'Apakah ada ongkos kirim atau biaya tambahan?',
                'keywords' => 'ongkir biaya kirim gratis ongkir biaya admin biaya tambahan total bayar',
                'answer' => "Jumlah yang tertera di halaman Checkout adalah total yang perlu kamu bayar untuk pesanan itu. Kalau kamu ingin menanyakan biaya kirim untuk alamat tertentu, hubungi admin ya. {kontak}",
                'action_label' => null,
                'action_url' => null,
            ],

            // --------------------------------------------------- Pengiriman & pelacakan
            [
                'category' => 'pengiriman',
                'question' => 'Bagaimana cara melacak pesanan saya?',
                'keywords' => 'lacak tracking resi posisi kurir dimana pesanan sampai mana peta lokasi kurir',
                'answer' => "Buka menu \"Pesanan\" lalu klik \"Detail\" pada pesananmu. Di sana ada:\n• status tiap tahap (dibuat, dibayar, diproses, kurir ditugaskan, dalam perjalanan, tiba)\n• nama kurir dan tombol untuk menghubunginya\n• posisi terakhir kurir di peta (setelah kurir berangkat)\n• perkiraan waktu tiba\n\nAtau ketik nomor pesananmu di chat ini (contoh: ORD-ABC123XYZ0), nanti aku bantu cek.",
                'action_label' => 'Lihat Pesanan Saya',
                'action_url' => '/pesanan',
            ],
            [
                'category' => 'pengiriman',
                'question' => 'Berapa lama pesanan sampai?',
                'keywords' => 'estimasi perkiraan tiba lama pengiriman kapan sampai berapa hari waktu',
                'answer' => "Perkiraan tiba muncul di halaman detail pesanan setelah kurir ditugaskan. Begitu kurir berangkat, perkiraannya dihitung dari jarak kurir ke alamatmu, jadi bisa berubah mengikuti kondisi jalan.\n\nSebelum kurir ditugaskan, perkiraan belum tersedia karena pesananmu masih disiapkan.",
                'action_label' => 'Lihat Pesanan Saya',
                'action_url' => '/pesanan',
            ],
            [
                'category' => 'pengiriman',
                'question' => 'Apa arti status pesanan?',
                'keywords' => 'status pesanan arti menunggu diproses dikirim selesai dibatalkan pending processing shipped completed',
                'answer' => "• Menunggu: pesanan baru dibuat atau menunggu pembayaran.\n• Diproses: pesananmu sedang disiapkan penjual.\n• Dikirim: kurir sedang mengantar pesananmu.\n• Selesai: pesanan sudah tiba.\n• Dibatalkan: pesanan dibatalkan (misalnya pembayaran kedaluwarsa).",
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'category' => 'pengiriman',
                'question' => 'Kenapa posisi kurir di peta tidak bergerak?',
                'keywords' => 'lokasi kurir tidak berubah tidak update belum update posisi kurir diam peta loncat interval',
                'answer' => "Posisi kurir dikirim berkala sesuai interval yang diatur toko (misalnya tiap 15 menit), bukan real-time setiap detik. Jadi titik di peta bisa terlihat diam lalu berpindah agak jauh. Lihat keterangan \"diperbarui ... yang lalu\" di halaman detail pesanan.\n\nKalau sudah jauh lebih lama dari interval, mungkin kurir sedang tanpa sinyal. Kamu bisa menghubungi kurir dari halaman detail pesanan.",
                'action_label' => 'Lihat Pesanan Saya',
                'action_url' => '/pesanan',
            ],
            [
                'category' => 'pengiriman',
                'question' => 'Pesanan belum sampai padahal sudah lewat perkiraan',
                'keywords' => 'telat terlambat belum sampai lewat estimasi lama sekali belum datang belum tiba',
                'answer' => "Cek dulu posisi terakhir kurir dan perkiraan tiba di halaman detail pesanan. Kalau sudah lewat cukup lama, hubungi kurir lewat tombol di halaman itu. Kalau kurir tidak bisa dihubungi, kabari admin dengan menyebut nomor pesananmu. {kontak}",
                'action_label' => 'Lihat Pesanan Saya',
                'action_url' => '/pesanan',
            ],
            [
                'category' => 'pengiriman',
                'question' => 'Bagaimana cara menghubungi kurir?',
                'keywords' => 'kontak kurir nomor kurir telepon kurir hubungi kurir chat kurir',
                'answer' => "Setelah kurir ditugaskan, nama dan tombol \"Hubungi kurir\" muncul di halaman detail pesananmu. Kalau belum muncul, berarti kurir belum ditugaskan, pesananmu masih disiapkan.",
                'action_label' => 'Lihat Pesanan Saya',
                'action_url' => '/pesanan',
            ],
            [
                'category' => 'pengiriman',
                'question' => 'Kurir tidak menemukan alamatku, bagaimana?',
                'keywords' => 'kurir nyasar alamat tidak ketemu patokan alamat kurir telepon tidak diangkat',
                'answer' => "Pastikan nomor HP yang kamu isi di pesanan aktif dan bisa dihubungi. Kamu juga bisa menelepon kurir dari halaman detail pesanan dan menjelaskan patokan lokasimu (warna rumah, dekat bangunan apa).\n\nAlamat lengkap dengan patokan membantu banget untuk pesanan berikutnya.",
                'action_label' => 'Lihat Pesanan Saya',
                'action_url' => '/pesanan',
            ],

            // ------------------------------------------ Pembatalan & pengembalian
            [
                'category' => 'retur',
                'question' => 'Bagaimana cara membatalkan pesanan?',
                'keywords' => 'batal batalkan cancel pesanan salah pesan tidak jadi',
                'answer' => "Saat ini pembatalan dilakukan oleh admin. Hubungi kami secepatnya sebelum pesanan dikirim, sebutkan nomor pesanan (ORD-...) dan alasannya. {kontak}\n\nPesanan Bayar Online yang tidak dibayar sampai batas waktu juga akan otomatis dibatalkan.",
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'category' => 'retur',
                'question' => 'Barang yang diterima rusak atau tidak sesuai, bagaimana?',
                'keywords' => 'rusak cacat pecah salah barang tidak sesuai komplain keluhan kurang barang kualitas',
                'answer' => "Maaf banget ya soal itu. Langkahnya:\n1. Foto atau rekam kondisi barang dan kemasannya.\n2. Hubungi admin secepatnya, sebutkan nomor pesanan dan lampirkan fotonya.\n3. Admin akan memeriksa dan memberi solusi, misalnya penukaran atau pengembalian dana sesuai kebijakan toko.\n\n{kontak}",
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'category' => 'retur',
                'question' => 'Bagaimana kebijakan pengembalian barang dan uang (refund)?',
                'keywords' => 'retur refund pengembalian dana uang kembali tukar barang garansi',
                'answer' => "Pengajuan retur dan refund diproses lewat admin setelah barang diperiksa. Lama dan cara pengembalian dana mengikuti metode pembayaran yang kamu pakai. Hubungi admin dengan nomor pesanan dan alasan pengajuanmu. {kontak}",
                'action_label' => null,
                'action_url' => null,
            ],

            // ------------------------------------------------------------------ Akun
            [
                'category' => 'akun',
                'question' => 'Bagaimana cara mendaftar akun?',
                'keywords' => 'daftar akun baru registrasi buat akun sign up register',
                'answer' => "Klik \"Daftar\" di pojok kanan atas, isi nama, email, dan password, lalu kirim. Setelah itu kamu bisa langsung belanja.",
                'action_label' => 'Daftar Sekarang',
                'action_url' => '/register',
            ],
            [
                'category' => 'akun',
                'question' => 'Aku lupa password, bagaimana?',
                'keywords' => 'lupa password reset password sandi tidak bisa login ganti password lupa sandi',
                'answer' => "Di halaman Masuk, klik \"Lupa password\", masukkan emailmu, lalu ikuti petunjuk yang dikirim ke email itu. Kalau emailnya tidak masuk, cek folder spam atau hubungi admin. {kontak}",
                'action_label' => 'Reset Password',
                'action_url' => '/forgot-password',
            ],
            [
                'category' => 'akun',
                'question' => 'Bagaimana cara mengubah profil, nomor HP, atau password?',
                'keywords' => 'ubah profil edit akun ganti password ganti email ganti nama nomor hp profil',
                'answer' => "Klik namamu di bagian atas untuk membuka halaman Profil. Di sana kamu bisa mengubah nama, email, dan password.",
                'action_label' => 'Buka Profil',
                'action_url' => '/profile',
            ],
            [
                'category' => 'akun',
                'question' => 'Bagaimana cara menghapus akun?',
                'keywords' => 'hapus akun delete account tutup akun nonaktifkan akun',
                'answer' => "Buka halaman Profil, lalu cari bagian hapus akun di bagian bawah dan konfirmasi dengan password. Hati-hati: akun yang dihapus tidak bisa dikembalikan dan riwayat pesananmu ikut terhapus.",
                'action_label' => 'Buka Profil',
                'action_url' => '/profile',
            ],

            // ---------------------------------------------------------------- Produk
            [
                'category' => 'produk',
                'question' => 'Bagaimana cara mencari produk?',
                'keywords' => 'cari produk pencarian search kategori filter temukan barang',
                'answer' => "Ketik nama produk di kolom pencarian bagian atas halaman, atau pilih kategori di beranda untuk menyaring produk. Kamu juga bisa tanya aku langsung, misalnya \"ada kopi?\" atau \"harga sabun\", nanti aku carikan.",
                'action_label' => 'Ke Beranda',
                'action_url' => '/',
            ],

            // --------------------------------------------------------- Kontak & bantuan
            [
                'category' => 'kontak',
                'question' => 'Bagaimana cara menghubungi admin atau customer service?',
                'keywords' => 'kontak admin cs customer service hubungi whatsapp wa email telepon bantuan bicara manusia',
                'answer' => "Kamu bisa tanya aku dulu, aku bisa bantu soal pemesanan, pembayaran, pelacakan, dan produk. Untuk hal yang perlu ditangani langsung oleh admin (perubahan pesanan, pembatalan, komplain), hubungi admin ya. {kontak}",
                'action_label' => null,
                'action_url' => null,
            ],
            [
                'category' => 'kontak',
                'question' => 'Jam operasional layanan?',
                'keywords' => 'jam buka tutup operasional layanan admin libur hari kerja',
                'answer' => "Admin melayani {jam}. Chatbot ini bisa kamu tanya kapan saja.",
                'action_label' => null,
                'action_url' => null,
            ],
        ];
    }
}
