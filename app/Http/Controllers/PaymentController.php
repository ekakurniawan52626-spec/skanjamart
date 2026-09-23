<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi Midtrans Snap.
 * Tambahkan MIDTRANS_SERVER_KEY, MIDTRANS_CLIENT_KEY, MIDTRANS_IS_PRODUCTION di .env
 * lalu install SDK: composer require midtrans/midtrans-php
 */
class PaymentController extends Controller
{
    public function pay(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => $order->phone,
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            ['snap_token' => $snapToken]
        );

        return view('checkout.pay', compact('order', 'snapToken'));
    }

    /**
     * Webhook notification handler dari Midtrans.
     * Daftarkan URL ini di dashboard Midtrans: /payment/notification
     */
    public function notification(Request $request)
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

        $notif = new \Midtrans\Notification();

        $order = Order::where('order_number', $notif->order_id)->first();

        if (! $order) {
            Log::warning('Midtrans notification: order not found', ['order_id' => $notif->order_id]);
            return response()->json(['message' => 'order not found'], 404);
        }

        $status = $notif->transaction_status;
        $fraud = $notif->fraud_status ?? null;

        if ($status === 'capture' || $status === 'settlement') {
            if ($fraud === 'accept' || $fraud === null) {
                // Jangan menurunkan status yang sudah lebih maju (mis. sudah dikirim kurir).
                $order->update([
                    'payment_status' => 'paid',
                    'status' => $order->status === 'pending' ? 'processing' : $order->status,
                ]);
            }
        } elseif ($status === 'pending') {
            $order->update(['payment_status' => 'unpaid']);
        } elseif (in_array($status, ['deny', 'expire', 'cancel'])) {
            $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
        }

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'transaction_id' => $notif->transaction_id ?? null,
                'transaction_status' => $status,
                'payment_type' => $notif->payment_type ?? null,
                'raw_response' => (array) $notif->getResponse(),
            ]
        );

        return response()->json(['message' => 'ok']);
    }
}
