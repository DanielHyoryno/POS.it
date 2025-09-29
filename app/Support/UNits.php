<?php
namespace App\Support;

class Units
{
    // Normalize user input to base (g, ml, pcs)
    public static function toBase(string $unit, float $qty): float
    {
        return match ($unit) {
            'kg' => $qty * 1000,
            'l', 'L' => $qty * 1000,
            default => $qty, // g, ml, pcs
        };
    }
}
