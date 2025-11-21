<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemLot extends Model
{
    protected $fillable = ['item_id','qty','expiry_date','received_at','cost_price','note'];

    protected $casts = [
        'qty' => 'decimal:3',
        'cost_price' => 'decimal:2',
        'expiry_date' => 'date',
        'received_at' => 'datetime',
    ];

    public function item(){ 
        return $this->belongsTo(Item::class); 
    }

    public function scopeNotExpired($q){
        return $q->where(function($qq){
            $qq->whereNull('expiry_date')->orWhere('expiry_date', '>', now()->toDateString());
        });
    }

    public function isExpired(): bool{
        return $this->expiry_date && $this->expiry_date->lte(now()->startOfDay());
    }
}
