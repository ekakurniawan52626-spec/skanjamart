<?php

namespace App\Models;

use App\Support\Fmt;
use App\Support\Geo;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Delivery extends Model
{
    public const ASSIGNED = 'assigned';
    public const ON_THE_WAY = 'on_the_way';
    public const DELIVERED = 'delivered';

    public const STATUS_LABELS = [
        self::ASSIGNED => 'Kurir ditugaskan',
        self::ON_THE_WAY => 'Dalam perjalanan',
        self::DELIVERED => 'Terkirim',
    ];

    protected $fillable = [
        'order_id', 'courier_id', 'token', 'status', 'ping_interval_minutes',
        'dest_lat', 'dest_lng', 'estimated_arrival_at',
        'last_lat', 'last_lng', 'last_note', 'last_ping_at',
        'started_at', 'delivered_at', 'admin_note',
    ];

    protected $hidden = ['token'];

    protected function casts(): array
    {
        return [
            'ping_interval_minutes' => 'integer',
            'dest_lat' => 'float',
            'dest_lng' => 'float',
            'last_lat' => 'float',
            'last_lng' => 'float',
            'estimated_arrival_at' => 'datetime',
            'last_ping_at' => 'datetime',
            'started_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public static function newToken(): string
    {
        do {
            $token = Str::random(48);
        } while (static::where('token', $token)->exists());

        return $token;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function locations()
    {
        return $this->hasMany(CourierLocation::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::ASSIGNED, self::ON_THE_WAY], true);
    }

    public function hasDestination(): bool
    {
        return $this->dest_lat !== null && $this->dest_lng !== null;
    }

    public function hasLastLocation(): bool
    {
        return $this->last_lat !== null && $this->last_lng !== null;
    }

    /** Jarak garis lurus kurir (posisi terakhir) ke tujuan, dalam km. */
    public function distanceKm(): ?float
    {
        if (! $this->hasDestination() || ! $this->hasLastLocation()) {
            return null;
        }

        return Geo::distanceKm($this->last_lat, $this->last_lng, $this->dest_lat, $this->dest_lng);
    }

    /**
     * Perkiraan tiba.
     * - Kalau posisi kurir & tujuan diketahui: dihitung dari jarak (live).
     * - Kalau tidak: pakai perkiraan manual dari admin (cadangan).
     *
     * @return array{at: ?CarbonInterface, source: ?string}
     */
    public function estimate(): array
    {
        if ($this->status === self::DELIVERED) {
            return ['at' => $this->delivered_at, 'source' => 'delivered'];
        }

        $km = $this->status === self::ON_THE_WAY ? $this->distanceKm() : null;

        if ($km !== null && $this->last_ping_at !== null) {
            $speed = max(5.0, (float) config('skanjamart.delivery.average_speed_kmh', 25));
            $factor = max(1.0, (float) config('skanjamart.delivery.road_factor', 1.3));
            $minutes = (int) ceil(($km * $factor / $speed) * 60);

            // Dihitung dari saat posisi terakhir diterima, karena kurir terus bergerak sesudahnya.
            return ['at' => $this->last_ping_at->copy()->addMinutes(max($minutes, 1)), 'source' => 'live'];
        }

        if ($this->estimated_arrival_at !== null) {
            return ['at' => $this->estimated_arrival_at, 'source' => 'manual'];
        }

        return ['at' => null, 'source' => null];
    }

    /** Kalimat perkiraan tiba yang siap ditampilkan. */
    public function etaText(): string
    {
        ['at' => $at, 'source' => $source] = $this->estimate();

        if ($source === 'delivered') {
            return 'Sudah tiba pada ' . Fmt::dateTime($at);
        }

        if ($at === null) {
            return $this->status === self::ASSIGNED
                ? 'Perkiraan tiba akan muncul setelah kurir berangkat'
                : 'Perkiraan tiba belum tersedia';
        }

        $minutesLeft = -1 * Fmt::minutesSince($at); // positif = masih ke depan

        if ($minutesLeft <= 0) {
            return $source === 'live'
                ? 'Segera tiba'
                : 'Melewati perkiraan awal (' . Fmt::time($at) . ')';
        }

        $left = $minutesLeft < 60
            ? (int) ceil($minutesLeft) . ' menit lagi'
            : floor($minutesLeft / 60) . ' jam ' . ((int) round($minutesLeft) % 60) . ' menit lagi';

        return 'Sekitar ' . Fmt::time($at) . ' (' . $left . ')';
    }

    /** Lokasi kurir sudah lama tidak diperbarui (lebih dari 2x interval + toleransi). */
    public function isStale(): bool
    {
        if ($this->status !== self::ON_THE_WAY) {
            return false;
        }

        $since = Fmt::minutesSince($this->last_ping_at ?? $this->started_at);

        if ($since === null) {
            return false;
        }

        return $since > ($this->ping_interval_minutes * 2 + 2);
    }
}
