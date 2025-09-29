<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['sale_id','product_id','qty','price','discount','total'];

    protected $casts = [
        'price'   => 'decimal:2',
        'discount'=> 'decimal:2',
        'total'   => 'decimal:2',
    ];

    public function sale()   { return $this->belongsTo(Sale::class); }
    public function product(){ return $this->belongsTo(Product::class); }
}
