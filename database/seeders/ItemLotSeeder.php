<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Item, ItemLot, InventoryMovement};

class ItemLotSeeder extends Seeder
{
    public function run(): void
    {
        // Define lot plans per item (name => [ [qty, +daysToExpire|null], ... ])
        $plan = [
            'Milk' => [
                [500,  5],     // near expiry
                [1500, 30],
            ],
            'Almond' => [
                [800,  45],
                [400,  null],  // non-expiring lot
            ],
            'Banana' => [
                [1200, 6],     // near expiry
            ],
            'Chocolate Bar' => [
                [20,   365],   // long shelf-life
            ],
            'Bread Loaf' => [
                [10,   2],     // very near expiry
                [15,   7],
            ],
            'Coffee Bean' => [
                [1000, 120],
            ],
        ];

        DB::transaction(function () use ($plan) {
            foreach ($plan as $itemName => $lots) {
                $item = Item::where('name', $itemName)->first();
                if (!$item) continue;

                foreach ($lots as [$qty, $days]) {
                    $lot = ItemLot::create([
                        'item_id'     => $item->id,
                        'qty'         => $qty,
                        'expiry_date' => is_null($days) ? null : now()->addDays($days)->toDateString(),
                        'received_at' => now()->subDays(rand(0,5)),
                        'cost_price'  => $item->cost_price,
                        'note'        => 'Seed lot',
                    ]);

                    InventoryMovement::create([
                        'item_id'    => $item->id,
                        'lot_id'     => $lot->id,
                        'change_qty' => $qty,
                        'reason'     => 'restock',
                        'note'       => 'Seed restock',
                        'created_at' => $lot->received_at,
                        'updated_at' => $lot->received_at,
                    ]);
                }

                // keep items.current_qty in sync with sum(lots.qty)
                $item->resyncStockFromLots();
            }
        });
    }
}
