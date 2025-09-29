<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name' => 'Drinks',   'sort_order' => 1],
            ['name' => 'Food',     'sort_order' => 2],
            ['name' => 'Bakery',   'sort_order' => 3],
            ['name' => 'Specials', 'sort_order' => 4],
        ];

        foreach ($cats as $c) {
            Category::firstOrCreate(
                ['name' => $c['name']],
                [
                    'slug' => Str::slug($c['name']),
                    'sort_order' => $c['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
