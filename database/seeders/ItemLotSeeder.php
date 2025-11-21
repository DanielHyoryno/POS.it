<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ItemLotSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $items = DB::table('items')->get();

        foreach($items as $item){
            $lotCount = rand(1,3);

            for($i=0; $i<$lotCount; $i++){
                $qty = $faker->numberBetween(100,2000);

                $hasExpiry = $faker->boolean(80);
                $daysToExpire = $hasExpiry ? $faker->numberBetween(1,180) : null;

                $receivedAt = now()->subDays($faker->numberBetween(0,5));

                $lotId = DB::table('item_lots')->insertGetId([
                    'item_id'=>$item->id,
                    'qty'=>$qty,
                    'expiry_date'=>$daysToExpire ? now()->addDays($daysToExpire)->toDateString() : null,
                    'received_at'=>$receivedAt,
                    'cost_price'=>$item->cost_price,
                    'note'=>'Seed lot',
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);

                DB::table('inventory_movements')->insert([
                    'item_id'=>$item->id,
                    'lot_id'=>$lotId,
                    'change_qty'=>$qty,
                    'reason'=>'restock',
                    'note'=>'Seed restock',
                    'created_at'=>$receivedAt,
                    'updated_at'=>$receivedAt,
                ]);
            }

            $sum = DB::table('item_lots')
                ->where('item_id',$item->id)
                ->sum('qty');

            DB::table('items')
                ->where('id',$item->id)
                ->update([
                    'current_qty'=>$sum,
                    'updated_at'=>now(),
                ]);
        }
    }
}
