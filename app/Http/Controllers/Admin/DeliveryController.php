<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Delivery;
use App\Models\Order;
use App\Support\Fmt;
use App\Support\Tracking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryController extends Controller
{
    /** Pantau semua pengiriman + peta kurir yang sedang jalan. */
    public function index(Request $request)
    {
        $filter = $request->query('status', 'aktif');

        $deliveries = Delivery::with(['order.user', 'courier'])
            ->when($filter === 'aktif', fn ($q) => $q->whereIn('status', [Delivery::ASSIGNED, Delivery::ON_THE_WAY]))
            ->when($filter === 'selesai', fn ($q) => $q->where('status', Delivery::DELIVERED))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $mapPoints = Delivery::with(['order', 'courier'])
            ->where('status', Delivery::ON_THE_WAY)
            ->whereNotNull('last_lat')
            ->get()
            ->map(fn (Delivery $d) => [
                'lat' => $d->last_lat,
                'lng' => $d->last_lng,
                'title' => $d->courier->name . ' — ' . $d->order->order_number,
                'ago' => Fmt::ago($d->last_ping_at),
                'url' => route('admin.orders.show', $d->order),
                'stale' => $d->isStale(),
            ])
            ->values();

        return view('admin.deliveries.index', compact('deliveries', 'filter', 'mapPoints'));
    }

    /** Tugaskan kurir ke pesanan. */
    public function store(Request $request, Order $order)
    {
        if ($order->delivery()->exists()) {
            return back()->with('error', 'Pesanan ini sudah punya kurir.');
        }

        if (in_array($order->status, ['cancelled', 'completed'], true)) {
            return back()->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak bisa dikirim lagi.');
        }

        $data = $this->validated($request, creating: true);

        Delivery::create($data + [
            'order_id' => $order->id,
            'token' => Delivery::newToken(),
            'status' => Delivery::ASSIGNED,
        ]);

        if ($order->status === 'pending') {
            $order->update(['status' => 'processing']);
        }

        return back()->with('success', 'Kurir ditugaskan. Kirim link kurir supaya kurir bisa mulai mengantar.');
    }

    /** Ubah interval kirim lokasi, tujuan, perkiraan tiba, catatan (dan kurir selama belum berangkat). */
    public function update(Request $request, Delivery $delivery)
    {
        if ($delivery->status === Delivery::DELIVERED) {
            return back()->with('error', 'Pengiriman yang sudah selesai tidak bisa diubah.');
        }

        // Hanya field yang benar-benar dikirim form yang diubah.
        $data = array_intersect_key($this->validated($request, creating: false), $request->all());

        // Ganti kurir hanya boleh sebelum kurir berangkat. Link kurir lama otomatis mati.
        if (isset($data['courier_id']) && (int) $data['courier_id'] !== $delivery->courier_id) {
            if ($delivery->status !== Delivery::ASSIGNED) {
                return back()->with('error', 'Kurir tidak bisa diganti setelah berangkat. Batalkan penugasan lalu tugaskan ulang.');
            }
            $data['token'] = Delivery::newToken();
        } else {
            unset($data['courier_id']);
        }

        $delivery->update($data);

        return back()->with('success', 'Pengaturan pengiriman diperbarui.');
    }

    /** Batalkan penugasan kurir (riwayat yang sudah terkirim tidak boleh dihapus). */
    public function destroy(Delivery $delivery)
    {
        if ($delivery->status === Delivery::DELIVERED) {
            return back()->with('error', 'Pengiriman yang sudah selesai tidak bisa dibatalkan.');
        }

        $order = $delivery->order;
        $delivery->delete();

        if ($order->status === 'shipped') {
            $order->update(['status' => 'processing']);
        }

        return back()->with('success', 'Penugasan kurir dibatalkan.');
    }

    /** Data terbaru untuk auto-refresh di halaman admin. */
    public function tracking(Delivery $delivery)
    {
        return response()->json(Tracking::payload($delivery->order, admin: true));
    }

    private function validated(Request $request, bool $creating): array
    {
        $min = (int) config('skanjamart.delivery.min_ping_minutes', 1);
        $max = (int) config('skanjamart.delivery.max_ping_minutes', 120);

        $data = $request->validate([
            'courier_id' => [
                $creating ? 'required' : 'sometimes',
                Rule::exists('couriers', 'id')->where('is_active', true),
            ],
            'ping_interval_minutes' => [$creating ? 'required' : 'sometimes', 'integer', "min:$min", "max:$max"],
            'estimated_arrival_at' => ['nullable', 'date'],
            'dest_lat' => ['nullable', 'numeric', 'between:-90,90', 'required_with:dest_lng'],
            'dest_lng' => ['nullable', 'numeric', 'between:-180,180', 'required_with:dest_lat'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ], [
            'courier_id.required' => 'Pilih kurir dulu.',
            'courier_id.exists' => 'Kurir tidak ditemukan atau sudah nonaktif.',
            'ping_interval_minutes.min' => "Interval minimal $min menit.",
            'ping_interval_minutes.max' => "Interval maksimal $max menit.",
        ]);

        $data['estimated_arrival_at'] = Fmt::fromInput($data['estimated_arrival_at'] ?? null);
        $data['dest_lat'] = $data['dest_lat'] ?? null;
        $data['dest_lng'] = $data['dest_lng'] ?? null;
        $data['admin_note'] = $data['admin_note'] ?? null;

        return $data;
    }
}
