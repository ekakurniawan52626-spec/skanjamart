<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'cost_price', 'stock', 'image', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return str_starts_with($this->image, 'http')
                ? $this->image
                : asset('storage/' . $this->image);
        }

        // Belum ada gambar diupload -> pakai foto placeholder otomatis,
        // konsisten per produk (seed dari slug) supaya tidak berubah-ubah tiap refresh.
        return 'https://picsum.photos/seed/' . $this->slug . '/600/600';
    }
}
