/**
 * SKANJAMart - pemilih titik tujuan pengiriman (admin).
 * Klik peta untuk menaruh pin, seret pin untuk menggeser, atau cari alamat.
 */
document.addEventListener('alpine:init', function () {
    window.Alpine.data('skjPicker', function (cfg) {
        var map = null;
        var marker = null;

        return {
            lat: cfg.lat === null || cfg.lat === undefined ? '' : String(cfg.lat),
            lng: cfg.lng === null || cfg.lng === undefined ? '' : String(cfg.lng),
            query: cfg.query || '',
            searching: false,
            msg: '',

            init: function () {
                var self = this;
                this.$nextTick(function () { self.mount(); });
            },

            destroy: function () {
                if (map) { map.remove(); map = null; }
            },

            get hasPoint() {
                return this.lat !== '' && this.lng !== '';
            },

            mount: function () {
                var self = this;

                if (typeof L === 'undefined') {
                    this.msg = 'Peta belum bisa dimuat (periksa koneksi internet). Titik tujuan bersifat opsional.';
                    return;
                }

                var center = this.hasPoint
                    ? [parseFloat(this.lat), parseFloat(this.lng)]
                    : [cfg.center.lat, cfg.center.lng];

                map = L.map(this.$refs.map, { scrollWheelZoom: false })
                    .setView(center, this.hasPoint ? 16 : cfg.center.zoom);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; kontributor OpenStreetMap',
                }).addTo(map);

                if (this.hasPoint) {
                    this.place(center[0], center[1], false);
                }

                map.on('click', function (e) {
                    self.place(e.latlng.lat, e.latlng.lng, false);
                });
            },

            place: function (lat, lng, zoomIn) {
                var self = this;

                this.lat = lat.toFixed(6);
                this.lng = lng.toFixed(6);
                this.msg = '';

                if (!map) { return; }

                if (!marker) {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    marker.on('dragend', function () {
                        var p = marker.getLatLng();
                        self.lat = p.lat.toFixed(6);
                        self.lng = p.lng.toFixed(6);
                    });
                } else {
                    marker.setLatLng([lat, lng]);
                }

                if (zoomIn) { map.setView([lat, lng], 16); }
            },

            clear: function () {
                this.lat = '';
                this.lng = '';
                if (marker && map) { map.removeLayer(marker); marker = null; }
            },

            search: function () {
                var self = this;
                var q = (this.query || '').trim();

                if (q === '') { return; }
                if (!map) { this.msg = 'Peta belum siap.'; return; }

                this.searching = true;
                this.msg = '';

                return fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=id&q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' },
                }).then(function (res) {
                    if (!res.ok) { throw new Error('HTTP ' + res.status); }
                    return res.json();
                }).then(function (rows) {
                    if (!rows.length) {
                        self.msg = 'Alamat tidak ditemukan. Coba persingkat (mis. nama jalan + kota), atau klik langsung di peta.';
                        return;
                    }
                    self.place(parseFloat(rows[0].lat), parseFloat(rows[0].lon), true);
                }).catch(function () {
                    self.msg = 'Pencarian alamat gagal. Klik langsung di peta untuk menaruh pin.';
                }).then(function () {
                    self.searching = false;
                });
            },
        };
    });
});
