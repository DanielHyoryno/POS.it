<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $drinks = DB::table('categories')->where('name','Drinks')->first();
        $food = DB::table('categories')->where('name','Food')->first();
        $bakery = DB::table('categories')->where('name','Bakery')->first();

        $milk = DB::table('items')->where('name','Milk')->first();
        $bread = DB::table('items')->where('name','Bread Loaf')->first();

        $this->seedProduct(
            'Milk 200ml',
            strtoupper($faker->bothify('MIL###')),
            'simple',
            $drinks ? $drinks->id : null,
            $faker->numberBetween(6000,12000),
            $milk ? $milk->id : null,
            200
        );

        $this->seedProduct(
            'Bread Loaf',
            strtoupper($faker->bothify('BRD###')),
            'simple',
            $bakery ? $bakery->id : null,
            $faker->numberBetween(10000,18000),
            $bread ? $bread->id : null,
            1
        );

        $this->seedProduct(
            'Gym Rat Drink',
            strtoupper($faker->bothify('GYM###')),
            'composite',
            $drinks ? $drinks->id : null,
            $faker->numberBetween(25000,40000)
        );

        $this->seedProduct(
            'Choco Banana Toast',
            strtoupper($faker->bothify('TST###')),
            'composite',
            $food ? $food->id : null,
            $faker->numberBetween(20000,35000)
        );
    }

    private function seedProduct($name,$sku,$type,$categoryId,$price,$linkedItemId=null,$perSaleQty=null)
    {
        $exists = DB::table('products')->where('name',$name)->first();

        if($exists){
            return;
        }

        DB::table('products')->insert([
            'name'=>$name,
            'sku'=>$sku,
            'type'=>$type,
            'category_id'=>$categoryId,
            'selling_price'=>$price,
            'is_active'=>true,
            'linked_item_id'=>$type==='simple' ? $linkedItemId : null,
            'per_sale_qty'=>$type==='simple' ? $perSaleQty : null,
            'image_path'=>null,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
    }
}
