<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\Tracking;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        $order->load('items', 'delivery.courier');

        $tracking = Tracking::payload($order);

        return view('orders.show', compact('order', 'tracking'));
    }

    /** Data terbaru untuk auto-refresh halaman tracking (JSON). */
    public function tracking(Order $order)
    {
        $this->authorizeOrder($order);

        return response()->json(Tracking::payload($order));
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);
    }
}
