<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Milk',          'base_unit' => 'ml',  'low_stock_threshold' => 1000, 'cost_price' => 0.50, 'is_active' => true],
            ['name' => 'Almond',        'base_unit' => 'g',   'low_stock_threshold' => 500,  'cost_price' => 0.07, 'is_active' => true],
            ['name' => 'Banana',        'base_unit' => 'g',   'low_stock_threshold' => 800,  'cost_price' => 0.02, 'is_active' => true],
            ['name' => 'Chocolate Bar', 'base_unit' => 'pcs', 'low_stock_threshold' => 10,   'cost_price' => 2000, 'is_active' => true],
            ['name' => 'Bread Loaf',    'base_unit' => 'pcs', 'low_stock_threshold' => 5,    'cost_price' => 8000, 'is_active' => true],
            ['name' => 'Coffee Bean',   'base_unit' => 'g',   'low_stock_threshold' => 200,  'cost_price' => 0.10, 'is_active' => true],
        ];

        foreach ($items as $data) {
            Item::firstOrCreate(
                ['name' => $data['name']],
                $data + ['current_qty' => 0] // will be resynced from lots
            );
        }
    }
}
