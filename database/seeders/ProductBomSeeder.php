<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Product, Item, ProductBomLine};

class ProductBomSeeder extends Seeder
{
    public function run(): void
    {
        $milk   = Item::where('name','Milk')->first();
        $almond = Item::where('name','Almond')->first();
        $banana = Item::where('name','Banana')->first();
        $choco  = Item::where('name','Chocolate Bar')->first();
        $bread  = Item::where('name','Bread Loaf')->first();

        $gymRat = Product::where('name','Gym Rat Drink')->first();
        $toast  = Product::where('name','Choco Banana Toast')->first();

        if ($gymRat) {
            $gymRat->bomLines()->delete();

            ProductBomLine::create(['product_id'=>$gymRat->id, 'item_id'=>$milk?->id,   'qty'=>200]);
            ProductBomLine::create(['product_id'=>$gymRat->id, 'item_id'=>$almond?->id, 'qty'=>50]);
            ProductBomLine::create(['product_id'=>$gymRat->id, 'item_id'=>$banana?->id, 'qty'=>100]);
            ProductBomLine::create(['product_id'=>$gymRat->id, 'item_id'=>$choco?->id,  'qty'=>1]);
        }

        if ($toast) {
            $toast->bomLines()->delete();

            ProductBomLine::create(['product_id'=>$toast->id,  'item_id'=>$bread?->id,  'qty'=>1]);
            ProductBomLine::create(['product_id'=>$toast->id,  'item_id'=>$banana?->id, 'qty'=>50]);
            ProductBomLine::create(['product_id'=>$toast->id,  'item_id'=>$choco?->id,  'qty'=>1]);
        }
    }
}
