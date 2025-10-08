<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Item, InventoryMovement};
use Illuminate\Http\Request;
use App\Support\Units;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\ItemLot; 



class ItemController extends Controller
{
    public function index(Request $r)
    {
        $items = Item::query()
            ->when($r->boolean('low'), fn($q) => $q->lowStock())
            ->latest('id')->paginate(15);

        return view('admin/items/index', compact('items'));
    }

    public function create()
    {
        return view('admin/items/create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required','string','max:255','unique:items,name'],
            'base_unit' => ['required', Rule::in(['g','ml','pcs'])],
            'low_stock_threshold' => ['required','numeric','min:0'],
            'cost_price' => ['nullable','numeric','min:0'],
            'is_active' => ['boolean'],
        ]);

        $item = Item::create($data);
        return redirect()->route('admin.items.show', $item)->with('ok','Item created');
    }

    public function show(Item $item)
    {
        $movements = $item->movements()->latest()->paginate(20);
        return view('admin/items/show', compact('item','movements'));
    }

    public function edit(Item $item)
    {
        return view('admin/items/edit', compact('item'));
    }

    public function update(Request $r, Item $item)
    {
        $data = $r->validate([
            'name' => ['required','string','max:255', Rule::unique('items','name')->ignore($item->id)],
            'low_stock_threshold' => ['required','numeric','min:0'],
            'cost_price' => ['nullable','numeric','min:0'],
            'is_active' => ['boolean'],
        ]);
        $item->update($data);
        return back()->with('ok','Item updated');
    }

    public function toggle(Item $item)
    {
        $item->update(['is_active' => ! $item->is_active]);
        return back()->with('ok','Item status updated');
    }

    public function restock(Request $r, Item $item)
    {
        $this->authorizeWrite($item);

        $data = $r->validate([
            'qty'  => ['required','numeric','gt:0'],
            'unit' => ['required', Rule::in(['g','kg','ml','L','pcs'])],
            'note' => ['nullable','string','max:1000'],
            'expiry_date' => ['nullable','date','after:today'],
        ]);

        if (!$item->is_active) {
            return back()->withErrors('Item is inactive.');
        }

        $baseQty = Units::toBase($data['unit'], (float)$data['qty']);

        \DB::transaction(function () use ($item, $baseQty, $data) {
            $lot = ItemLot::create([
                'item_id'     => $item->id,
                'qty'         => $baseQty,
                'expiry_date' => $data['expiry_date'] ?? null,
                'received_at' => now(),
                'cost_price'  => $item->cost_price,
                'note'        => $data['note'] ?? null,
            ]);

            InventoryMovement::create([
                'item_id'    => $item->id,
                'lot_id'     => $lot->id,
                'change_qty' => $baseQty,
                'reason'     => 'restock',
                'note'       => $data['note'] ?? null,
            ]);

            $item->resyncStockFromLots();
        });

        return back()->with('ok','Restocked');
    }


    public function adjust(Request $r, Item $item)
    {
        $this->authorizeWrite($item);

        $data = $r->validate([
            'qty'  => ['required','numeric','not_in:0'],
            'unit' => ['required', Rule::in(['g','kg','ml','L','pcs'])],
            'note' => ['nullable','string','max:1000'],
            'expiry_date' => ['nullable','date','after:today'], 
        ]);

        $baseQty = Units::toBase($data['unit'], (float)$data['qty']);

        if (!$item->is_active) {
            return back()->withErrors('Item is inactive.');
        }

        \DB::transaction(function () use ($item, $baseQty, $data) {

            if ($baseQty > 0) {

                $lot = ItemLot::create([
                    'item_id'     => $item->id,
                    'qty'         => $baseQty,
                    'expiry_date' => $data['expiry_date'] ?? null,
                    'received_at' => now(),
                    'cost_price'  => $item->cost_price,
                    'note'        => $data['note'] ?? 'Adjustment (+)',
                ]);

                InventoryMovement::create([
                    'item_id'    => $item->id,
                    'lot_id'     => $lot->id,
                    'change_qty' => $baseQty,
                    'reason'     => 'adjust',
                    'note'       => $data['note'] ?? 'Adjustment (+)',
                ]);
            } else {
                $needed = abs($baseQty);

                $lots = $item->lots()
                    ->where(function($q){
                        $q->whereNull('expiry_date')->orWhere('expiry_date', '>', now()->toDateString());
                    })
                    ->orderByRaw('expiry_date IS NULL') 
                    ->orderBy('expiry_date')            
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                $available = (float) $lots->sum('qty');
                if ($available < $needed) {
                    throw new \RuntimeException('Adjustment would make stock negative across lots.');
                }

                foreach ($lots as $lot) {
                    if ($needed <= 0) break;

                    $take = min((float)$lot->qty, $needed);
                    if ($take <= 0) continue;

                    $lot->decrement('qty', $take);

                    InventoryMovement::create([
                        'item_id'    => $item->id,
                        'lot_id'     => $lot->id,
                        'change_qty' => -$take,
                        'reason'     => 'adjust',
                        'note'       => $data['note'] ?? 'Adjustment (-)',
                    ]);

                    $needed -= $take;
                }
            }

            $item->resyncStockFromLots();
        });

        return back()->with('ok','Adjusted');
    }


    private function authorizeWrite(Item $item)
    {
        return true;
    }
}
