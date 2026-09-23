<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    public const CATEGORIES = [
        'pemesanan' => 'Pemesanan',
        'pembayaran' => 'Pembayaran',
        'pengiriman' => 'Pengiriman & Pelacakan',
        'retur' => 'Pembatalan & Pengembalian',
        'akun' => 'Akun',
        'produk' => 'Produk',
        'kontak' => 'Kontak & Bantuan',
        'umum' => 'Umum',
    ];

    protected $fillable = [
        'category', 'question', 'keywords', 'answer', 'action_label', 'action_url', 'is_active', 'hits',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'hits' => 'integer',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }
}
