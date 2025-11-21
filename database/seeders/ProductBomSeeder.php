<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductBomSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $milk = DB::table('items')->where('name','Milk')->first();
        $almond = DB::table('items')->where('name','Almond')->first();
        $banana = DB::table('items')->where('name','Banana')->first();
        $choco = DB::table('items')->where('name','Chocolate Bar')->first();
        $bread = DB::table('items')->where('name','Bread Loaf')->first();

        $gymRat = DB::table('products')->where('name','Gym Rat Drink')->first();
        $toast = DB::table('products')->where('name','Choco Banana Toast')->first();

        if($gymRat){
            DB::table('product_bom_lines')
                ->where('product_id',$gymRat->id)
                ->delete();

            if($milk){
                DB::table('product_bom_lines')->insert([
                    'product_id'=>$gymRat->id,
                    'item_id'=>$milk->id,
                    'qty'=>$faker->numberBetween(150,250),
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }

            if($almond){
                DB::table('product_bom_lines')->insert([
                    'product_id'=>$gymRat->id,
                    'item_id'=>$almond->id,
                    'qty'=>$faker->numberBetween(30,80),
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }

            if($banana){
                DB::table('product_bom_lines')->insert([
                    'product_id'=>$gymRat->id,
                    'item_id'=>$banana->id,
                    'qty'=>$faker->numberBetween(80,150),
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }

            if($choco){
                DB::table('product_bom_lines')->insert([
                    'product_id'=>$gymRat->id,
                    'item_id'=>$choco->id,
                    'qty'=>1,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }
        }

        if($toast){
            DB::table('product_bom_lines')
                ->where('product_id',$toast->id)
                ->delete();

            if($bread){
                DB::table('product_bom_lines')->insert([
                    'product_id'=>$toast->id,
                    'item_id'=>$bread->id,
                    'qty'=>1,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }

            if($banana){
                DB::table('product_bom_lines')->insert([
                    'product_id'=>$toast->id,
                    'item_id'=>$banana->id,
                    'qty'=>$faker->numberBetween(40,80),
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }

            if($choco){
                DB::table('product_bom_lines')->insert([
                    'product_id'=>$toast->id,
                    'item_id'=>$choco->id,
                    'qty'=>1,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }
        }
    }
}
