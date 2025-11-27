<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $cats = [
            ['name'=>'Drinks',   'sort_order'=>1],
            ['name'=>'Food',     'sort_order'=>2],
            ['name'=>'Bakery',   'sort_order'=>3],
            ['name'=>'Specials', 'sort_order'=>4],
        ];

        foreach($cats as $c){
            $exists = DB::table('categories')
                ->where('name',$c['name'])
                ->first();

            if(!$exists){
                DB::table('categories')->insert([
                    'name'=>$c['name'],
                    'slug'=>Str::slug($c['name']),
                    'sort_order'=>$c['sort_order'],
                    'is_active'=>true,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ]);
            }
        }
    }
}