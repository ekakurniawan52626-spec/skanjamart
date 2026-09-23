/**
 * SKANJAMart - halaman kurir.
 * Saat pengiriman berstatus "on_the_way", halaman ini mengirim lokasi HP kurir
 * tiap N menit (N diatur admin dan ikut diperbarui dari respons server).
 */
document.addEventListener('alpine:init', function () {
    window.Alpine.data('skjCourier', function (cfg) {
        var timer = null;
        var wakeLock = null;
        var nextAt = 0;

        function clock(ms) {
            return new Date(ms).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }

        return {
            status: cfg.status,
            interval: cfg.interval,
            url: cfg.url,
            lastSent: cfg.lastSent || '',
            running: false,
            busy: false,
            error: '',
            note: '',
            sentCount: 0,
            nextText: '',
            secure: window.isSecureContext !== false,

            init: function () {
                var self = this;

                if (this.status === 'on_the_way') {
                    this.begin();
                }

                document.addEventListener('visibilitychange', function () {
                    if (document.visibilityState !== 'visible' || !self.running) { return; }
                    self.requestWakeLock();
                    // Timer sering ditahan browser saat layar mati; kalau sudah lewat jadwal, kirim sekarang.
                    if (nextAt && Date.now() >= nextAt) { self.tick(); }
                });
            },

            begin: function () {
                this.running = true;
                this.requestWakeLock();
                this.tick();
            },

            tick: function () {
                var self = this;
                clearTimeout(timer);

                return this.sendNow().then(function (result) {
                    if (!self.running) { return; }

                    // Kalau gagal (bukan karena izin ditolak), coba lagi lebih cepat dari interval normal.
                    var ms = (!result.ok && !result.denied) ? 60000 : Math.max(1, self.interval) * 60000;

                    nextAt = Date.now() + ms;
                    self.nextText = clock(nextAt);
                    timer = setTimeout(function () { self.tick(); }, ms);
                });
            },

            position: function () {
                return new Promise(function (resolve, reject) {
                    if (!('geolocation' in navigator)) {
                        reject({ code: 0, message: 'unsupported' });
                        return;
                    }
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 25000,
                        maximumAge: 0,
                    });
                });
            },

            sendNow: function () {
                var self = this;

                if (this.busy) { return Promise.resolve({ ok: true }); }

                this.busy = true;
                this.error = '';

                return this.position().then(function (pos) {
                    return fetch(self.url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({
                            lat: pos.coords.latitude,
                            lng: pos.coords.longitude,
                            accuracy: pos.coords.accuracy,
                            note: self.note ? self.note : null,
                        }),
                    });
                }).then(function (res) {
                    return res.json().catch(function () { return {}; }).then(function (json) {
                        if (res.status === 409) {
                            self.running = false;
                            self.status = json.status || self.status;
                            self.error = json.message || 'Pengiriman tidak sedang berjalan.';
                            clearTimeout(timer);
                            return { ok: true };
                        }
                        if (!res.ok) { throw { code: -1 }; }

                        if (json.interval) { self.interval = json.interval; }
                        self.sentCount += 1;
                        self.lastSent = clock(Date.now());
                        return { ok: true };
                    });
                }).catch(function (err) {
                    self.error = self.explain(err);
                    return { ok: false, denied: !!(err && err.code === 1) };
                }).then(function (result) {
                    self.busy = false;
                    return result;
                });
            },

            explain: function (err) {
                if (err && err.code === 1) {
                    return 'Izin lokasi ditolak. Aktifkan izin lokasi untuk halaman ini di pengaturan browser, lalu muat ulang halaman.';
                }
                if (err && err.code === 2) {
                    return 'Lokasi tidak tersedia. Pastikan GPS / lokasi di HP menyala. Akan dicoba lagi sebentar lagi.';
                }
                if (err && err.code === 3) {
                    return 'Mencari lokasi terlalu lama. Coba pindah ke tempat terbuka. Akan dicoba lagi sebentar lagi.';
                }
                if (err && err.message === 'unsupported') {
                    return 'Browser ini tidak mendukung GPS. Coba buka link di Chrome atau Safari.';
                }
                if (!window.isSecureContext) {
                    return 'Browser hanya mengizinkan GPS di halaman HTTPS. Minta admin memberikan link dengan alamat https.';
                }
                return 'Lokasi belum terkirim (koneksi bermasalah). Akan dicoba lagi sebentar lagi.';
            },

            requestWakeLock: function () {
                // Menjaga layar tetap menyala supaya timer tidak dihentikan browser. Tidak semua browser mendukung.
                if (!('wakeLock' in navigator)) { return; }
                navigator.wakeLock.request('screen').then(function (lock) { wakeLock = lock; }).catch(function () {});
            },
        };
    });
});
