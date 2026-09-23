<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Zona waktu tampilan
    |--------------------------------------------------------------------------
    | Database tetap menyimpan waktu sesuai config('app.timezone') (UTC).
    | Zona ini hanya dipakai untuk MENAMPILKAN jam pengiriman / perkiraan tiba.
    */
    'timezone' => env('STORE_TIMEZONE', 'Asia/Jakarta'),

    /*
    |--------------------------------------------------------------------------
    | Kontak toko (dipakai chatbot)
    |--------------------------------------------------------------------------
    | Isi lewat .env, contoh: STORE_WHATSAPP=6281234567890
    */
    'contact' => [
        'whatsapp' => env('STORE_WHATSAPP'),
        'email' => env('STORE_EMAIL'),
        'hours' => env('STORE_HOURS', 'setiap hari pukul 08.00 - 21.00 WIB'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pengiriman & tracking kurir
    |--------------------------------------------------------------------------
    */
    'delivery' => [
        // Interval kirim lokasi (menit). Admin bisa ubah per pengiriman.
        'default_ping_minutes' => 15,
        'min_ping_minutes' => 1,
        'max_ping_minutes' => 120,

        // Dipakai untuk menghitung perkiraan tiba dari jarak kurir ke tujuan.
        'average_speed_kmh' => (float) env('DELIVERY_AVG_SPEED_KMH', 25),
        // Jarak lurus (garis udara) dikali angka ini supaya mendekati jarak jalan.
        'road_factor' => 1.3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Peta
    |--------------------------------------------------------------------------
    | Titik tengah awal peta saat admin memilih lokasi tujuan.
    | Default: tengah Indonesia. Isi lokasi tokomu di .env supaya lebih pas.
    */
    'map' => [
        'lat' => (float) env('STORE_MAP_LAT', -2.5),
        'lng' => (float) env('STORE_MAP_LNG', 118.0),
        'zoom' => (int) env('STORE_MAP_ZOOM', 5),
    ],

];
