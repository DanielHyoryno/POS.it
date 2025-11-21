<?php
namespace App\Support;

class Units
{
    // memastikan convert g ke kg dan satuan lainnya
    public static function toBase(string $unit, float $qty): float
    {
        return match ($unit) {
            'kg' => $qty * 1000,
            'l', 'L' => $qty * 1000,
            default => $qty, // g, ml, pcs
        };
    }
}
