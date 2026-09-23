{{-- Panel kurir & tracking untuk admin (dipakai di admin/orders/show.blade.php) --}}
<div class="border-t border-forest-100 pt-5 mt-6">
    <h3 class="section-eyebrow mb-3">Kurir &amp; Pengiriman</h3>

    @if($order->delivery)
        @php($delivery = $order->delivery)
        <div
            x-data="skjTracking({ initial: @js($tracking), url: @js(route('admin.deliveries.tracking', $delivery)), admin: true, pollMs: 20000 })"
            x-init="init()"
            class="space-y-4"
        >
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <p class="font-medium text-ink">{{ $delivery->courier->name }} <span class="text-ink-faint font-normal">({{ $delivery->courier->phone }})</span></p>
                    <p class="text-sm text-ink-muted" x-text="data.delivery.status_label"></p>
                </div>
                <a :href="'https://wa.me/{{ $delivery->courier->whatsappNumber() }}'" target="_blank" class="btn-secondary !py-1.5 !px-3.5 text-sm">Chat WhatsApp Kurir</a>
            </div>

            <p class="text-sm text-ink" x-text="data.delivery.eta_text"></p>
            <p class="text-xs text-ink-faint" x-show="data.delivery.last" x-text="'Posisi diperbarui ' + (data.delivery.last ? data.delivery.last.ago : '') + (data.delivery.distance_km !== null ? (' · sekitar ' + km(data.delivery.distance_km) + ' km ke tujuan') : '')"></p>
            <p class="text-xs text-wine-500" x-show="data.delivery.stale">⚠️ Lokasi kurir sudah lama tidak diperbarui.</p>

            <template x-if="hasMapData">
                <div x-ref="map" style="height: 280px" class="rounded-xl overflow-hidden border border-forest-100"></div>
            </template>
            <p class="text-xs text-ink-faint" x-show="mapError">Peta belum bisa dimuat (periksa koneksi internet).</p>
            <p class="text-xs text-wine-500" x-show="offline">Gagal memuat data terbaru, akan dicoba lagi otomatis.</p>

            <details class="text-sm">
                <summary class="cursor-pointer text-forest-600 font-medium">Pengaturan pengiriman</summary>
                <form action="{{ route('admin.deliveries.update', $delivery) }}" method="POST" class="mt-3 space-y-3 max-w-sm">
                    @csrf @method('PATCH')
                    <div>
                        <label class="block text-xs font-medium text-ink mb-1">Interval kirim lokasi (menit)</label>
                        <input type="number" name="ping_interval_minutes" value="{{ $delivery->ping_interval_minutes }}" min="{{ config('skanjamart.delivery.min_ping_minutes') }}" max="{{ config('skanjamart.delivery.max_ping_minutes') }}" class="input-classic text-sm py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink mb-1">Perkiraan tiba manual (cadangan, dipakai kalau kurir belum berangkat)</label>
                        <input type="datetime-local" name="estimated_arrival_at" value="{{ \App\Support\Fmt::toInput($delivery->estimated_arrival_at) }}" class="input-classic text-sm py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink mb-1">Catatan (opsional)</label>
                        <textarea name="admin_note" rows="2" class="input-classic text-sm py-1.5">{{ $delivery->admin_note }}</textarea>
                    </div>
                    <button class="btn-secondary !py-1.5 !px-4 text-sm">Simpan Pengaturan</button>
                </form>

                @if($delivery->status !== 'delivered')
                    <form action="{{ route('admin.deliveries.destroy', $delivery) }}" method="POST" class="mt-3" onsubmit="return confirm('Batalkan penugasan kurir ini?')">
                        @csrf @method('DELETE')
                        <button class="text-wine-500 font-medium hover:text-wine-600 text-sm">Batalkan penugasan kurir</button>
                    </form>
                @endif
            </details>
        </div>
    @elseif(in_array($order->status, ['cancelled']))
        <p class="text-sm text-ink-muted">Pesanan ini dibatalkan, kurir tidak perlu ditugaskan.</p>
    @else
        <form action="{{ route('admin.deliveries.store', $order) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Kurir</label>
                    <select name="courier_id" required class="input-classic">
                        <option value="">- Pilih kurir -</option>
                        @foreach($couriers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                        @endforeach
                    </select>
                    @if($couriers->isEmpty())
                        <p class="text-xs text-wine-500 mt-1">Belum ada kurir aktif. <a href="{{ route('admin.couriers.index') }}" class="underline">Tambah kurir</a> dulu.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Interval kirim lokasi (menit)</label>
                    <input type="number" name="ping_interval_minutes" value="{{ config('skanjamart.delivery.default_ping_minutes') }}" min="{{ config('skanjamart.delivery.min_ping_minutes') }}" max="{{ config('skanjamart.delivery.max_ping_minutes') }}" required class="input-classic">
                </div>
            </div>

            <div
                x-data="skjPicker({ lat: null, lng: null, query: {{ Js::from($order->shipping_address) }}, center: { lat: {{ config('skanjamart.map.lat') }}, lng: {{ config('skanjamart.map.lng') }}, zoom: {{ config('skanjamart.map.zoom') }} } })"
                x-init="init()"
            >
                <label class="block text-sm font-medium text-ink mb-1.5">Titik tujuan (opsional, buat hitung perkiraan tiba otomatis)</label>
                <div class="flex gap-2 mb-2">
                    <input type="text" x-model="query" placeholder="Cari alamat..." class="input-classic text-sm">
                    <button type="button" @click="search()" :disabled="searching" class="btn-secondary !py-2 !px-4 text-sm whitespace-nowrap">Cari</button>
                </div>
                <div x-ref="map" style="height: 240px" class="rounded-xl overflow-hidden border border-forest-100 mb-2"></div>
                <p class="text-xs text-ink-faint" x-show="msg" x-text="msg"></p>
                <p class="text-xs text-ink-faint" x-show="!hasPoint">Klik di peta untuk menaruh titik tujuan, atau lewati langkah ini.</p>
                <button type="button" x-show="hasPoint" @click="clear()" class="text-xs text-wine-500 hover:text-wine-600 mt-1">Hapus titik</button>
                <input type="hidden" name="dest_lat" :value="lat">
                <input type="hidden" name="dest_lng" :value="lng">
            </div>

            <div>
                <label class="block text-sm font-medium text-ink mb-1.5">Perkiraan tiba manual (cadangan, dipakai sebelum kurir berangkat)</label>
                <input type="datetime-local" name="estimated_arrival_at" class="input-classic max-w-xs">
            </div>

            <button class="btn-primary !py-2">Tugaskan Kurir</button>
        </form>
    @endif
</div>
