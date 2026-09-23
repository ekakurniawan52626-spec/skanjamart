<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Delivery;
use App\Models\Order;
use App\Support\Tracking;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user', 'delivery.courier')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'user', 'payment', 'delivery.courier');

        $couriers = Courier::where('is_active', true)->orderBy('name')->get();
        $tracking = Tracking::payload($order, admin: true);

        return view('admin.orders.show', compact('order', 'couriers', 'tracking'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ]);

        $order->update($data);

        // Jaga supaya status pesanan dan status pengiriman tidak saling bertentangan.
        $delivery = $order->delivery;

        if ($delivery && $delivery->status !== Delivery::DELIVERED) {
            if ($data['status'] === 'cancelled') {
                $delivery->delete(); // link kurir otomatis tidak berlaku lagi
            } elseif ($data['status'] === 'completed') {
                $delivery->update(['status' => Delivery::DELIVERED, 'delivered_at' => now()]);
            } elseif ($data['status'] === 'shipped' && $delivery->status === Delivery::ASSIGNED) {
                $delivery->update(['status' => Delivery::ON_THE_WAY, 'started_at' => now()]);
            }
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
