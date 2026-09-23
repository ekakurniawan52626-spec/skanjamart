<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Format tanggal/jam untuk tampilan (zona waktu toko, bahasa Indonesia).
 * Data di database tetap disimpan dengan zona waktu aplikasi (UTC).
 */
class Fmt
{
    public static function zone(): string
    {
        return config('skanjamart.timezone', 'Asia/Jakarta');
    }

    public static function zoneLabel(): string
    {
        return match (self::zone()) {
            'Asia/Jakarta' => 'WIB',
            'Asia/Makassar' => 'WITA',
            'Asia/Jayapura' => 'WIT',
            default => '',
        };
    }

    public static function local(?\DateTimeInterface $date): ?Carbon
    {
        if ($date === null) {
            return null;
        }

        return Carbon::instance($date)->timezone(self::zone())->locale('id');
    }

    /** contoh: "21 Sep 2026, 14:05 WIB" */
    public static function dateTime(?\DateTimeInterface $date): ?string
    {
        $local = self::local($date);

        return $local ? trim($local->translatedFormat('d M Y, H:i') . ' ' . self::zoneLabel()) : null;
    }

    /** contoh: "14:05 WIB" */
    public static function time(?\DateTimeInterface $date): ?string
    {
        $local = self::local($date);

        return $local ? trim($local->format('H:i') . ' ' . self::zoneLabel()) : null;
    }

    /** contoh: "5 menit yang lalu" */
    public static function ago(?\DateTimeInterface $date): ?string
    {
        $local = self::local($date);

        return $local ? $local->diffForHumans() : null;
    }

    /** Selisih menit (positif = sudah lewat) dari $date sampai sekarang. */
    public static function minutesSince(?\DateTimeInterface $date): ?float
    {
        if ($date === null) {
            return null;
        }

        return (now()->getTimestamp() - $date->getTimestamp()) / 60;
    }

    /** Ubah teks datetime-local (waktu toko) jadi Carbon dengan zona waktu aplikasi. */
    public static function fromInput(?string $value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return Carbon::parse($value, self::zone())->timezone(config('app.timezone'));
    }

    /** Untuk mengisi <input type="datetime-local">. */
    public static function toInput(?\DateTimeInterface $date): string
    {
        return $date ? Carbon::instance($date)->timezone(self::zone())->format('Y-m-d\TH:i') : '';
    }
}
