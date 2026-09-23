<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = ['name', 'phone', 'vehicle', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    /** Nomor untuk link wa.me (62xxxxxxxx). */
    public function whatsappNumber(): string
    {
        $digits = preg_replace('/\D+/', '', $this->phone);

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        return $digits;
    }
}
