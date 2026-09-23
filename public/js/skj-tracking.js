/**
 * SKANJAMart - komponen Alpine untuk tracking pesanan.
 * Dipakai di halaman pesanan pelanggan dan halaman detail pesanan admin.
 * Membutuhkan Leaflet (window.L) untuk peta; tanpa Leaflet, bagian lain tetap jalan.
 */
document.addEventListener('alpine:init', function () {
    var TILE_URL = 'https://tile.openstreetmap.org/{z}/{x}/{y}.png';
    var TILE_ATTR = '&copy; kontributor OpenStreetMap';

    function pinIcon(emoji) {
        return L.divIcon({
            className: 'skj-pin',
            html: '<div style="font-size:26px;line-height:26px;filter:drop-shadow(0 1px 2px rgba(0,0,0,.35))">' + emoji + '</div>',
            iconSize: [26, 26],
            iconAnchor: [13, 22],
        });
    }

    window.Alpine.data('skjTracking', function (cfg) {
        // Objek Leaflet disimpan di luar state Alpine supaya tidak dibungkus Proxy.
        var map = null;
        var courierMarker = null;
        var destMarker = null;
        var lastFitKey = '';
        var timer = null;

        return {
            data: cfg.initial,
            url: cfg.url,
            admin: !!cfg.admin,
            pollMs: cfg.pollMs || 30000,
            offline: false,
            mapError: false,

            init: function () {
                var self = this;
                this.$nextTick(function () { self.drawMap(); });
                this.schedule();
            },

            destroy: function () {
                clearTimeout(timer);
                if (map) { map.remove(); map = null; }
            },

            get delivery() {
                return this.data.delivery;
            },

            get finished() {
                var d = this.data.delivery;
                return this.data.order.cancelled
                    || this.data.order.status === 'completed'
                    || (d && d.status === 'delivered');
            },

            get hasMapData() {
                var d = this.data.delivery;
                return !!d && !!(d.last || d.destination);
            },

            km: function (value) {
                return String(value).replace('.', ',');
            },

            schedule: function () {
                var self = this;
                clearTimeout(timer);
                if (this.finished) { return; }
                timer = setTimeout(function () { self.refresh(); }, this.pollMs);
            },

            refresh: function () {
                var self = this;
                return fetch(this.url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                }).then(function (res) {
                    if (!res.ok) { throw new Error('HTTP ' + res.status); }
                    return res.json();
                }).then(function (json) {
                    self.data = json;
                    self.offline = false;
                    self.$nextTick(function () { self.drawMap(); });
                }).catch(function () {
                    self.offline = true;
                }).then(function () {
                    self.schedule();
                });
            },

            drawMap: function () {
                var d = this.data.delivery;
                var el = this.$refs.map;

                if (!el || !d || !(d.last || d.destination)) { return; }

                if (typeof L === 'undefined') { this.mapError = true; return; }

                // Elemen peta dibuat ulang (x-if)? Buang peta lama.
                if (map && map.getContainer() !== el) {
                    map.remove();
                    map = null; courierMarker = null; destMarker = null; lastFitKey = '';
                }

                var first = d.last || d.destination;

                if (!map) {
                    map = L.map(el, { scrollWheelZoom: false }).setView([first.lat, first.lng], 15);
                    L.tileLayer(TILE_URL, { maxZoom: 19, attribution: TILE_ATTR }).addTo(map);
                }

                map.invalidateSize();

                var points = [];

                if (d.last) {
                    var c = [d.last.lat, d.last.lng];
                    if (!courierMarker) {
                        courierMarker = L.marker(c, { icon: pinIcon('\uD83D\uDEF5'), title: 'Kurir' }).addTo(map);
                    } else {
                        courierMarker.setLatLng(c);
                    }
                    points.push(c);
                }

                if (d.destination) {
                    var t = [d.destination.lat, d.destination.lng];
                    if (!destMarker) {
                        destMarker = L.marker(t, { icon: pinIcon('\uD83D\uDCCD'), title: 'Tujuan' }).addTo(map);
                    } else {
                        destMarker.setLatLng(t);
                    }
                    points.push(t);
                }

                // Zoom ulang hanya kalau posisi berubah, supaya pengguna tidak "ditarik" balik saat sedang menggeser peta.
                var key = JSON.stringify(points);
                if (key !== lastFitKey) {
                    lastFitKey = key;
                    if (points.length > 1) {
                        map.fitBounds(points, { padding: [40, 40], maxZoom: 17 });
                    } else {
                        map.setView(points[0], 16);
                    }
                }
            },
        };
    });
});
