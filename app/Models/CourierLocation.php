<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourierLocation extends Model
{
    public $timestamps = false;

    protected $fillable = ['delivery_id', 'lat', 'lng', 'accuracy', 'note', 'recorded_at'];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'accuracy' => 'float',
            'recorded_at' => 'datetime',
        ];
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
}
