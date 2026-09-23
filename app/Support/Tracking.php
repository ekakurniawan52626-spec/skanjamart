<?php

namespace App\Support;

use App\Models\Delivery;
use App\Models\Order;

/**
 * Menyusun data tracking (untuk halaman pelanggan, admin, dan polling JSON).
 * Token link kurir TIDAK PERNAH dimasukkan ke sini.
 */
class Tracking
{
    public static function payload(Order $order, bool $admin = false): array
    {
        $order->loadMissing('delivery.courier');

        $delivery = $order->delivery;

        return [
            'order' => [
                'number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'payment_status' => $order->payment_status,
                'payment_label' => $order->payment_status_label,
                'payment_method' => $order->payment_method,
                'cancelled' => $order->status === 'cancelled',
            ],
            'steps' => array_map(fn (array $step) => [
                'key' => $step['key'],
                'label' => $step['label'],
                'hint' => $step['hint'],
                'done' => $step['done'],
                'current' => $step['current'],
                'at' => Fmt::dateTime($step['at']),
            ], $order->trackingSteps()),
            'delivery' => $delivery ? self::delivery($delivery, $admin) : null,
            'refreshed_at' => Fmt::time(now()),
        ];
    }

    public static function delivery(Delivery $delivery, bool $admin = false): array
    {
        $delivery->loadMissing('courier');

        $distance = $delivery->distanceKm();
        ['at' => $etaAt, 'source' => $etaSource] = $delivery->estimate();

        $data = [
            'status' => $delivery->status,
            'status_label' => $delivery->status_label,
            'courier' => [
                'name' => $delivery->courier?->name,
                'phone' => $delivery->courier?->phone,
                'vehicle' => $delivery->courier?->vehicle,
            ],
            'interval_minutes' => $delivery->ping_interval_minutes,
            'last' => $delivery->hasLastLocation() ? [
                'lat' => $delivery->last_lat,
                'lng' => $delivery->last_lng,
                'note' => $delivery->last_note,
                'at' => Fmt::dateTime($delivery->last_ping_at),
                'ago' => Fmt::ago($delivery->last_ping_at),
            ] : null,
            'destination' => $delivery->hasDestination() ? [
                'lat' => $delivery->dest_lat,
                'lng' => $delivery->dest_lng,
            ] : null,
            'distance_km' => $distance !== null ? round($distance, 1) : null,
            'eta_text' => $delivery->etaText(),
            'eta_source' => $etaSource,
            'eta_iso' => $etaAt?->toIso8601String(),
            'stale' => $delivery->isStale(),
            'started_at' => Fmt::dateTime($delivery->started_at),
            'delivered_at' => Fmt::dateTime($delivery->delivered_at),
        ];

        if ($admin) {
            $data['recent'] = $delivery->locations()
                ->orderByDesc('recorded_at')
                ->orderByDesc('id')
                ->limit(8)
                ->get()
                ->map(fn ($loc) => [
                    'lat' => $loc->lat,
                    'lng' => $loc->lng,
                    'accuracy' => $loc->accuracy !== null ? (int) round($loc->accuracy) : null,
                    'note' => $loc->note,
                    'at' => Fmt::dateTime($loc->recorded_at),
                ])
                ->all();
        }

        return $data;
    }
}
