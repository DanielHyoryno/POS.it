<?php
namespace App\Support;

class Cart
{
    // Always use this key
    private const KEY = 'pos_cart';

    /** Get cart as ['lines' => [ product_id => line ]] */
    public static function get(): array
    {
        // Preferred shape
        $cart = session(self::KEY, null);

        // Fallback: migrate from old 'cart' session if present
        if ($cart === null) {
            $legacy = session('cart', null);
            if ($legacy instanceof \Illuminate\Support\Collection) {
                $legacy = $legacy->toArray();
            }

            if (is_array($legacy)) {
                // if legacy is already keyed by product_id
                if (isset($legacy['lines']) && is_array($legacy['lines'])) {
                    $cart = $legacy;
                } elseif (isset($legacy[0]) && is_array($legacy[0]) && isset($legacy[0]['product_id'])) {
                    $cart = ['lines' => collect($legacy)->keyBy('product_id')->toArray()];
                } elseif (!isset($legacy['lines'])) {
                    $cart = ['lines' => $legacy]; // assume keyed by product_id
                }
            }
        }

        if (!is_array($cart) || !isset($cart['lines']) || !is_array($cart['lines'])) {
            $cart = ['lines' => []];
        }

        return $cart;
    }

    /** Save */
    public static function put(array $cart): void
    {
        if (!isset($cart['lines']) || !is_array($cart['lines'])) {
            $cart = ['lines' => []];
        }
        session([self::KEY => $cart]);
    }

    /** Clear (and remove legacy) */
    public static function clear(): void
    {
        session()->forget(self::KEY);
        session()->forget('cart'); // legacy key, in case it existed
    }

    /** Totals for a Cart::get()-style cart */
    public static function totals(array $cart): array
    {
        $subtotal = 0.0;
        foreach ($cart['lines'] as $line) {
            $subtotal += ((float)$line['price']) * ((float)$line['qty']);
        }
        $discount = 0.0; $tax = 0.0; $total = $subtotal - $discount + $tax;
        return compact('subtotal','discount','tax','total');
    }
}
