<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $items = [
            ['name'=>'Milk',          'base_unit'=>'ml'],
            ['name'=>'Almond',        'base_unit'=>'g'],
            ['name'=>'Banana',        'base_unit'=>'g'],
            ['name'=>'Chocolate Bar', 'base_unit'=>'pcs'],
            ['name'=>'Bread Loaf',    'base_unit'=>'pcs'],
            ['name'=>'Coffee Bean',   'base_unit'=>'g'],
        ];

        foreach($items as $data){
            $exists = DB::table('items')
                ->where('name',$data['name'])
                ->first();

            if(!$exists){
                DB::table('items')->insert([
                    'name'=>$data['name'],
                    'base_unit'=>$data['base_unit'],
                    'low_stock_threshold'=>$faker->numberBetween(200,2000),
                    'cost_price'=>$faker->numberBetween(1000,15000),
                    'current_qty'=>0,
                    'is_active'=>true,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }
        }
    }
}
