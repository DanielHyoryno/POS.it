<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','slug','sort_order','is_active','icon','color'];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function products() { return $this->hasMany(Product::class); }
}
