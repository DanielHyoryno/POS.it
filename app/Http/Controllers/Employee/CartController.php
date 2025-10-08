<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function show()
    {
        $cart   = $this->getCart(); 
        $totals = $this->totals($cart['lines']);
        return view('employee.sales.cart', compact('cart','totals'));
    }

    public function add(Request $r)
    {
        $data = $r->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'nullable|numeric|min:0.001',
        ]);
        $qty = (float)($data['qty'] ?? 1);

        $p    = Product::findOrFail($data['product_id']);
        $cart = $this->getCart(); 

        if (isset($cart['lines'][$p->id])) {
            $cart['lines'][$p->id]['qty'] = (float)$cart['lines'][$p->id]['qty'] + $qty;
        } else {
            $cart['lines'][$p->id] = [
                'product_id' => $p->id,
                'name'       => $p->name,
                'price'      => (float)($p->selling_price ?? 0),
                'qty'        => $qty,
            ];
        }

        $this->putCart($cart);
        return redirect()->route('employee.sales.cart.show')->with('ok','Added to cart');
    }

    /**
     * Update quantities
     * - Single: product_id, qty
     * - Bulk : rows[][product_id], rows[][qty]
     */
    public function update(Request $r)
    {
        $cart = $this->getCart();

        if ($r->filled('rows')) {
            $rows = $r->validate([
                'rows' => 'required|array',
                'rows.*.product_id' => 'required|exists:products,id',
                'rows.*.qty'        => 'required|numeric|min:0',
            ])['rows'];

            foreach ($rows as $row) {
                $pid = (int)$row['product_id'];
                $qty = (float)$row['qty'];
                if (!isset($cart['lines'][$pid])) continue;

                if ($qty <= 0) unset($cart['lines'][$pid]);
                else          $cart['lines'][$pid]['qty'] = $qty;
            }
        } else {
            $data = $r->validate([
                'product_id' => 'required|exists:products,id',
                'qty'        => 'required|numeric|min:0',
            ]);
            $pid = (int)$data['product_id'];
            $qty = (float)$data['qty'];

            if (isset($cart['lines'][$pid])) {
                if ($qty <= 0) unset($cart['lines'][$pid]);
                else          $cart['lines'][$pid]['qty'] = $qty;
            }
        }

        $this->putCart($cart);
        return redirect()->route('employee.sales.cart.show')->with('ok','Cart updated');
    }

    /** Remove a product */
    public function remove(Request $r)
    {
        $pid = (int)$r->validate([
            'product_id' => 'required|exists:products,id'
        ])['product_id'];

        $cart = $this->getCart();
        if (isset($cart['lines'][$pid])) unset($cart['lines'][$pid]);
        $this->putCart($cart);

        return redirect()->route('employee.sales.cart.show')->with('ok','Item removed');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('employee.sales.cart.show')->with('ok','Cart cleared');
    }

    // Helper (dibuat GPT)
    private function getCart(): array
    {
        $raw = session()->get('cart', []);

        if (is_array($raw) && array_key_exists('lines', $raw) && is_array($raw['lines'])) {
            return $raw;
        }

        if ($raw instanceof \Illuminate\Support\Collection) {
            $raw = $raw->toArray();
        }

        if (isset($raw[0]) && is_array($raw[0]) && isset($raw[0]['product_id'])) {
            $raw = collect($raw)->keyBy('product_id')->toArray();
        }

        if (is_array($raw) && !isset($raw['lines'])) {
            return ['lines' => $raw]; 
        }

        return ['lines' => []];
    }

    private function putCart(array $cart): void
    {
        if (!isset($cart['lines']) || !is_array($cart['lines'])) {
            $cart = ['lines' => []];
        }
        session()->put('cart', $cart);
    }

    private function totals(array $lines): array
    {
        $subtotal = 0.0;
        foreach ($lines as $line) {
            $subtotal += ((float)$line['price']) * ((float)$line['qty']);
        }
        $discount = 0.0; $tax = 0.0; $total = $subtotal - $discount + $tax;
        return compact('subtotal','discount','tax','total');
    }
}
