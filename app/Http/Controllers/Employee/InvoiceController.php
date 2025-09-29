<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Sale;

class InvoiceController extends Controller
{
    public function show(Sale $sale)
    {
        $sale->load(['items.product','payments','user']);
        return view('employee.sales.invoice', compact('sale'));
    }
}
