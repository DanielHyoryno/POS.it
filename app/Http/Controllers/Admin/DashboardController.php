<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Item, InventoryMovement, Product, Sale};
use App\Models\ItemLot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems = Item::count();
        $activeItems = Item::where('is_active', true)->count();
        $lowStockCount = Item::whereColumn('current_qty', '<=', 'low_stock_threshold')->count();

        $lowStockItems = Item::whereColumn('current_qty', '<=', 'low_stock_threshold')
            ->orderBy('current_qty')
            ->take(8)
            ->get(['id', 'name', 'base_unit', 'current_qty', 'low_stock_threshold']);

        $recentMovements = InventoryMovement::with('item:id,name,base_unit')
            ->latest()
            ->take(10)
            ->get();

        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $simpleCount = Product::where('type', 'simple')->count();
        $compositeCount = Product::where('type', 'composite')->count();

        $compositeWithoutBom = Product::where('type', 'composite')
            ->whereDoesntHave('bomLines')
            ->count();

        $simpleWithInactiveItem = Product::where('type', 'simple')
            ->whereHas('linkedItem', fn($q) => $q->where('is_active', false))
            ->count();

        $productsNeedingCostData = Product::where(function ($q) {
                $q->where(function ($q) {
                    $q->where('type', 'simple')
                        ->whereHas('linkedItem', fn($qq) => $qq->whereNull('cost_price'));
                })->orWhere(function ($q) {
                    $q->where('type', 'composite')
                        ->whereHas('bomLines.item', fn($qqq) => $qqq->whereNull('cost_price'));
                });
            })
            ->count();

        $sampleProducts = Product::with([
                'linkedItem:id,cost_price,base_unit',
                'bomLines.item:id,cost_price,base_unit'
            ])
            ->latest('id')
            ->take(10)
            ->get();

        $sample = $sampleProducts->map(function (Product $p) {
            $cost = $p->estimatedCost();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'type' => $p->type,
                'price' => $p->selling_price,
                'cost' => $cost,
                'margin' => $p->selling_price - $cost,
                'is_active' => $p->is_active,
                'needs_bom' => $p->type === 'composite' && $p->bomLines->count() === 0,
                'simple_item_inactive' => $p->type === 'simple' && optional($p->linkedItem)->is_active === false,
                'missing_cost' => $p->type === 'simple'
                    ? is_null(optional($p->linkedItem)->cost_price)
                    : $p->bomLines->contains(fn($l) => is_null(optional($l->item)->cost_price)),
            ];
        });

        $nearDays = 7;

        $nearExpiryLots = ItemLot::with('item:id,name,base_unit')
            ->whereNotNull('expiry_date')
            ->where('qty', '>', 0)
            ->whereDate('expiry_date', '<=', now()->addDays($nearDays))
            ->orderBy('expiry_date')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalItems',
            'activeItems',
            'lowStockCount',
            'lowStockItems',
            'recentMovements',
            'totalProducts',
            'activeProducts',
            'simpleCount',
            'compositeCount',
            'compositeWithoutBom',
            'simpleWithInactiveItem',
            'productsNeedingCostData',
            'sample',
            'nearExpiryLots'
        ));
    }

    public function salesSeries(Request $request)
    {
        $range = $request->query('range', '1w');
        $status = $request->query('status');

        abort_unless(in_array($range, ['1d', '1w', '1m', '1y']), 400, 'Invalid range');

        $now = now();

        switch ($range) {
            case '1d':
                $start = $now->copy()->startOfDay();
                $step = 'hour';
                $label = fn(Carbon $c) => $c->format('H:00');
                $period = CarbonPeriod::create($start, '1 hour', $now->copy()->endOfDay());
                break;
            case '1w':
                $start = $now->copy()->subDays(6)->startOfDay();
                $step = 'day';
                $label = fn(Carbon $c) => $c->format('D');
                $period = CarbonPeriod::create($start, '1 day', $now->copy()->endOfDay());
                break;
            case '1m':
                $start = $now->copy()->subDays(29)->startOfDay();
                $step = 'day';
                $label = fn(Carbon $c) => $c->format('M j');
                $period = CarbonPeriod::create($start, '1 day', $now->copy()->endOfDay());
                break;
            case '1y':
            default:
                $start = $now->copy()->subMonthsNoOverflow(11)->startOfMonth();
                $step = 'month';
                $label = fn(Carbon $c) => $c->format('M Y');
                $period = CarbonPeriod::create($start, '1 month', $now->copy()->startOfMonth());
                break;
        }

        // langsung pakai waktu app, nggak usah di-convert ke UTC
        $salesQuery = Sale::whereBetween('created_at', [$start, $now]);

        if ($status) {
            $salesQuery->where('status', $status);
        } else {
            $salesQuery->where('status', '!=', 'void');
        }

        $sales = $salesQuery->get(['id', 'total', 'status', 'created_at']);

        $purchases = InventoryMovement::with('item:id,cost_price')
            ->where('reason', 'purchase')
            ->whereBetween('created_at', [$start, $now])
            ->get(['id', 'item_id', 'change_qty', 'created_at']);

        $buckets = [];

        foreach ($period as $tick) {
            $key = match ($step) {
                'hour' => $tick->format('Y-m-d H:00:00'),
                'day' => $tick->format('Y-m-d 00:00:00'),
                'month' => $tick->copy()->startOfMonth()->format('Y-m-01 00:00:00'),
            };

            $buckets[$key] = [
                'revenue' => 0.0,
                'orders' => 0,
                'expenses' => 0.0,
                'label' => $label($tick),
            ];
        }

        foreach ($sales as $s) {
            $t = $s->created_at;

            $key = match ($step) {
                'hour' => $t->format('Y-m-d H:00:00'),
                'day' => $t->format('Y-m-d 00:00:00'),
                'month' => $t->copy()->startOfMonth()->format('Y-m-01 00:00:00'),
            };

            if (isset($buckets[$key])) {
                $buckets[$key]['revenue'] += (float) $s->total;
                $buckets[$key]['orders'] += 1;
            }
        }

        foreach ($purchases as $p) {
            $t = $p->created_at;

            $key = match ($step) {
                'hour' => $t->format('Y-m-d H:00:00'),
                'day' => $t->format('Y-m-d 00:00:00'),
                'month' => $t->copy()->startOfMonth()->format('Y-m-01 00:00:00'),
            };

            if (isset($buckets[$key])) {
                $cost = (float) (optional($p->item)->cost_price ?? 0);
                $buckets[$key]['expenses'] += abs((float) $p->change_qty) * $cost;
            }
        }

        $labels = [];
        $revenue = [];
        $orders = [];
        $expenses = [];
        $profit = [];

        foreach ($buckets as $row) {
            $labels[] = $row['label'];
            $revenue[] = round($row['revenue'], 2);
            $orders[] = (int) $row['orders'];
            $expenses[] = round($row['expenses'], 2);
            $profit[] = round($row['revenue'] - $row['expenses'], 2);
        }

        return response()->json([
            'labels' => $labels,
            'revenue' => $revenue,
            'orders' => $orders,
            'expenses' => $expenses,
            'profit' => $profit,
        ]);
    }
}
