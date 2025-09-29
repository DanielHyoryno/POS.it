<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBomLine extends Model
{
    protected $fillable = ['product_id','item_id','qty'];

    protected $casts = [
        'qty' => 'decimal:3',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function item()     { return $this->belongsTo(Item::class); }
}
