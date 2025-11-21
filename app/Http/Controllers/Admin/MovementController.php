<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryMovement;

class MovementController extends Controller
{
    public function feed(Request $request)
    {
        $perPage = $request->query('per', 6);
        $movements = InventoryMovement::with('item:id,name,base_unit')
            ->latest()
            ->simplePaginate($perPage);

        $html = view('admin.movements._rows', [
            'rows' => $movements->items(),
        ])->render();

        $nextUrl = $movements->hasMorePages()
            ? route('admin.movements.feed', 
            ['page' => $movements->currentPage() + 1, 'per' => $perPage]) : null;

        return response()->json([
            'html'     => $html,
            'next_url' => $nextUrl,
        ]);
    }
}
