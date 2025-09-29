<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Product, Item, Category};

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $drinks  = Category::where('name','Drinks')->first();
        $food    = Category::where('name','Food')->first();
        $bakery  = Category::where('name','Bakery')->first();

        $milk   = Item::where('name','Milk')->first();
        $bread  = Item::where('name','Bread Loaf')->first();

        // Simple: Milk 200ml (sells raw material unit)
        Product::firstOrCreate(
            ['name' => 'Milk 200ml'],
            [
                'sku'             => 'MILK200',
                'type'            => 'simple',
                'category_id'     => optional($drinks)->id,
                'selling_price'   => 8000,
                'is_active'       => true,
                'linked_item_id'  => optional($milk)->id,
                'per_sale_qty'    => 200,
                'image_path'      => null,
            ]
        );

        // Simple: Bread Loaf (per pcs)
        Product::firstOrCreate(
            ['name' => 'Bread Loaf'],
            [
                'sku'             => 'BREAD01',
                'type'            => 'simple',
                'category_id'     => optional($bakery)->id,
                'selling_price'   => 12000,
                'is_active'       => true,
                'linked_item_id'  => optional($bread)->id,
                'per_sale_qty'    => 1,
                'image_path'      => null,
            ]
        );

        // Composite: Gym Rat Drink
        Product::firstOrCreate(
            ['name' => 'Gym Rat Drink'],
            [
                'sku'             => 'GYMRAT01',
                'type'            => 'composite',
                'category_id'     => optional($drinks)->id,
                'selling_price'   => 35000,
                'is_active'       => true,
                'image_path'      => null,
            ]
        );

        // Composite: Choco Banana Toast
        Product::firstOrCreate(
            ['name' => 'Choco Banana Toast'],
            [
                'sku'             => 'TOAST01',
                'type'            => 'composite',
                'category_id'     => optional($food)->id,
                'selling_price'   => 25000,
                'is_active'       => true,
                'image_path'      => null,
            ]
        );
    }
}
