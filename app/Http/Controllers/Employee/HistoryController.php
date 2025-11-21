<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Carbon;

class HistoryController extends Controller
{

    public function index(Request $request)
    {
        $dateStr = $request->query('date', now()->toDateString());
        $date = Carbon::parse($dateStr);
        $start = $date->copy()->startOfDay();
        $end   = $date->copy()->endOfDay();

        $sales = Sale::query()
            ->with(['items.product', 'user']) 
            ->whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'void')   
            ->orderByDesc('created_at')
            ->get();

        $totalRevenue = (float) $sales->sum('total');
        $orderCount   = (int) $sales->count();
        $avgOrder     = $orderCount ? $totalRevenue / $orderCount : 0;

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
