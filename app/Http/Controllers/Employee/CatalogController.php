<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Product;

class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->orderBy('is_active','desc')
            ->orderBy('name')
            ->get(['id','name','selling_price','type','is_active','linked_item_id']);


        // preload relations for availability calc
        $products->load(['item','bomComponents']);

        return view('employee.sales.index', compact('products'));
    }
}
