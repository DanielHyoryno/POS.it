<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Str;

class POSController extends Controller
{

    public function catalog()
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employee.sales.index', compact('products'));
    }

    public function cartShow()
    {
        $cart = session()->get('cart', []);
        return view('employee.sales.cart', compact('cart'));
    }

    public function cartAdd(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] += 1;
        } else {
            $cart[$product->id] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->selling_price,
                'qty'   => 1,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('employee.sales.cart')->with('success', 'Product added to cart!');
    }

    public function cartUpdate(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['qty'] = (int) $request->qty;
            session()->put('cart', $cart);
        }

        return redirect()->route('employee.sales.cart')->with('success', 'Cart updated.');
    }

    public function cartRemove(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('employee.sales.cart')->with('success', 'Item removed.');
    }

    public function cartClear()
    {
        session()->forget('cart');
        return redirect()->route('employee.sales.cart')->with('success', 'Cart cleared.');
    }

    /* =========================
     * Checkout & Invoice
     * ========================= */

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('employee.sales.catalog')->with('error', 'Cart is empty.');
        }

        return view('employee.sales.checkout', compact('cart'));
    }

    public function checkoutStore(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('employee.sales.catalog')->with('error', 'Cart is empty.');
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['qty']);
        $discount = 0;
        $tax      = 0;
        $total    = $subtotal - $discount + $tax;
        $paid     = $request->input('paid', $total);
        $change   = $paid - $total;

        $sale = Sale::create([
            'invoice_no' => 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
            'subtotal'   => $subtotal,
            'discount'   => $discount,
            'tax'        => $tax,
            'total'      => $total,
            'paid'       => $paid,
            'change'     => $change,
            'status'     => 'paid',
            'user_id'    => auth()->id(),
        ]);

        foreach ($cart as $item) {
            SaleItem::create([
                'sale_id'    => $sale->id,
                'product_id' => $item['id'],
                'qty'        => $item['qty'],
                'price'      => $item['price'],
                'total'      => $item['price'] * $item['qty'],
            ]);

            $product = Product::find($item['id']);
            if ($product && $product->linkedItem) {
                $needed = $item['qty'] * ($product->per_sale_qty ?? 1);

                $product->linkedItem->decrement('current_qty', $needed);

                InventoryMovement::create([
                    'item_id'     => $product->linkedItem->id,
                    'change_qty'  => -$needed,
                    'note'        => "Sale {$sale->invoice_no} / {$product->name}",
                ]);
            }
        }

        session()->forget('cart');

        return redirect()->route('employee.sales.invoice', $sale->id)
            ->with('success', 'Sale completed!');
    }

    public function invoice(Sale $sale)
    {
        $sale->load('items.product');
        return view('employee.sales.invoice', compact('sale'));
    }

    public function history(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $sales = Sale::whereDate('created_at', $date)
            ->with('items.product')
            ->get();

        return view('employee.sales.history', compact('sales', 'date'));
    }
}
