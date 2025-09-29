<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Carbon;

class HistoryController extends Controller
{
    /**
     * Show sales history for a given day (default: today).
     * URL: /employee/sales/history?date=YYYY-MM-DD
     */
    public function index(Request $request)
    {
        // Parse date or default to today (Asia/Jakarta assumed in config/app.php)
        $dateStr = $request->query('date', now()->toDateString());
        $date = Carbon::parse($dateStr);
        $start = $date->copy()->startOfDay();
        $end   = $date->copy()->endOfDay();

        // Grab sales for that day (include draft unless you only want paid)
        $sales = Sale::query()
            ->with(['items.product', 'user']) // adjust relations if needed
            ->whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'void')   // hide voids
            ->orderByDesc('created_at')
            ->get();

        // Totals
        $totalRevenue = (float) $sales->sum('total');
        $orderCount   = (int) $sales->count();
        $avgOrder     = $orderCount ? $totalRevenue / $orderCount : 0;

        // Prev/next day links
        $prevDate = $date->copy()->subDay()->toDateString();
        $nextDate = $date->copy()->addDay()->toDateString();

        return view('employee.sales.history', [
            'date'         => $date->toDateString(),
            'sales'        => $sales,
            'totalRevenue' => $totalRevenue,
            'orderCount'   => $orderCount,
            'avgOrder'     => $avgOrder,
            'prevDate'     => $prevDate,
            'nextDate'     => $nextDate,
        ]);
    }
}
