<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_LABELS = [
        'pending' => 'Menunggu',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    public const PAYMENT_LABELS = [
        'unpaid' => 'Belum dibayar',
        'paid' => 'Lunas',
        'failed' => 'Gagal',
    ];

    protected $fillable = [
        'order_number', 'user_id', 'total', 'status', 'payment_status',
        'payment_method', 'shipping_address', 'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return self::PAYMENT_LABELS[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    /**
     * Tahapan pesanan untuk ditampilkan sebagai timeline ke pelanggan.
     * Setiap tahap: key, label, hint, done, current, at (Carbon|null).
     */
    public function trackingSteps(): array
    {
        $delivery = $this->delivery;
        $isCod = $this->payment_method === 'cod';
        $paid = $this->payment_status === 'paid';

        $processed = in_array($this->status, ['processing', 'shipped', 'completed'], true) || $delivery !== null;
        $onTheWay = in_array($this->status, ['shipped', 'completed'], true)
            || ($delivery && in_array($delivery->status, [Delivery::ON_THE_WAY, Delivery::DELIVERED], true));
        $delivered = $this->status === 'completed' || ($delivery && $delivery->status === Delivery::DELIVERED);

        $steps = [
            [
                'key' => 'created',
                'label' => 'Pesanan dibuat',
                'hint' => null,
                'done' => true,
                'at' => $this->created_at,
            ],
            [
                'key' => 'payment',
                'label' => $isCod ? 'Bayar di tempat (COD)' : ($paid ? 'Pembayaran diterima' : 'Menunggu pembayaran'),
                'hint' => $isCod ? 'Bayar tunai ke kurir saat barang tiba' : null,
                'done' => $isCod || $paid,
                'at' => null,
            ],
            [
                'key' => 'processing',
                'label' => 'Pesanan diproses',
                'hint' => 'Penjual menyiapkan pesananmu',
                'done' => $processed,
                'at' => null,
            ],
            [
                'key' => 'courier',
                'label' => 'Kurir ditugaskan',
                'hint' => $delivery?->courier?->name,
                'done' => $delivery !== null,
                'at' => $delivery?->created_at,
            ],
            [
                'key' => 'on_the_way',
                'label' => 'Dalam perjalanan',
                'hint' => null,
                'done' => $onTheWay,
                'at' => $delivery?->started_at,
            ],
            [
                'key' => 'delivered',
                'label' => 'Pesanan tiba',
                'hint' => null,
                'done' => $delivered,
                'at' => $delivery?->delivered_at,
            ],
        ];

        $currentSet = false;
        foreach ($steps as &$step) {
            $step['current'] = false;
            if (! $step['done'] && ! $currentSet && $this->status !== 'cancelled') {
                $step['current'] = true;
                $currentSet = true;
            }
        }
        unset($step);

        return $steps;
    }
}
