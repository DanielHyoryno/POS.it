<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class ReceiptController extends Controller
{
    public function show(Sale $sale)
    {
        $sale->load(['items.product','payments','user']);
        return view('admin.pos.receipt', compact('sale'));
    }
}
