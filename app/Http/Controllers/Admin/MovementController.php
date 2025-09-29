<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryMovement;

class MovementController extends Controller
{
    // JSON feed for recent movements with pagination
    public function feed(Request $request)
    {
        $perPage = (int) $request->query('per', 6); // how many per "page"
        $movements = InventoryMovement::with('item:id,name,base_unit')
            ->latest()
            ->simplePaginate($perPage);

        // Render just the rows (partial)
        $html = view('admin.movements._rows', [
            'rows' => $movements->items(),
        ])->render();

        $nextUrl = $movements->hasMorePages()
            ? route('admin.movements.feed', ['page' => $movements->currentPage() + 1, 'per' => $perPage])
            : null;

        return response()->json([
            'html'     => $html,
            'next_url' => $nextUrl,
        ]);
    }
}
