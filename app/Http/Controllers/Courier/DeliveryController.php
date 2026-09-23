<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\CourierLocation;
use App\Models\Delivery;
use Illuminate\Http\Request;

/**
 * Halaman untuk kurir, tanpa login. Aksesnya lewat link rahasia /kurir/{token}
 * yang dibuat admin saat menugaskan kurir ke sebuah pesanan.
 */
class DeliveryController extends Controller
{
    public function show(Delivery $delivery)
    {
        $delivery->load('order.items', 'order.user', 'courier');

        return view('courier.show', compact('delivery'));
    }

    public function start(Delivery $delivery)
    {
        if ($delivery->status === Delivery::ASSIGNED) {
            $delivery->update([
                'status' => Delivery::ON_THE_WAY,
                'started_at' => now(),
            ]);

            $delivery->order->update(['status' => 'shipped']);
        }

        return redirect()->route('courier.show', $delivery->token);
    }

    public function location(Request $request, Delivery $delivery)
    {
        if ($delivery->status !== Delivery::ON_THE_WAY) {
            return response()->json([
                'ok' => false,
                'status' => $delivery->status,
                'message' => 'Pengiriman tidak sedang berjalan.',
            ], 409);
        }

        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0|max:100000',
            'note' => 'nullable|string|max:150',
        ]);

        $now = now();

        CourierLocation::create([
            'delivery_id' => $delivery->id,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'accuracy' => $data['accuracy'] ?? null,
            'note' => $data['note'] ?? null,
            'recorded_at' => $now,
        ]);

        $delivery->update([
            'last_lat' => $data['lat'],
            'last_lng' => $data['lng'],
            'last_note' => $data['note'] ?? null,
            'last_ping_at' => $now,
        ]);

        return response()->json([
            'ok' => true,
            'status' => $delivery->status,
            // Dikirim balik supaya perubahan interval dari admin langsung dipakai kurir.
            'interval' => $delivery->ping_interval_minutes,
        ]);
    }

    public function complete(Delivery $delivery)
    {
        if ($delivery->status === Delivery::ON_THE_WAY) {
            $delivery->update([
                'status' => Delivery::DELIVERED,
                'delivered_at' => now(),
            ]);

            $order = $delivery->order;
            $orderData = ['status' => 'completed'];

            // COD: uang diterima kurir saat barang tiba.
            if ($order->payment_method === 'cod' && $order->payment_status !== 'paid') {
                $orderData['payment_status'] = 'paid';
            }

            $order->update($orderData);
        }

        return redirect()->route('courier.show', $delivery->token);
    }
}
