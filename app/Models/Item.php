<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name','base_unit','low_stock_threshold','cost_price','is_active'
    ];

    protected $casts = [
        'is_active' => 'bool',
        'current_qty' => 'decimal:3',
        'low_stock_threshold' => 'decimal:3',
        'cost_price' => 'decimal:2',
    ];

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function scopeLowStock($q)
    {
        return $q->whereColumn('current_qty', '<=', 'low_stock_threshold');
    }

    public function lots() { return $this->hasMany(ItemLot::class); }

    public function resyncStockFromLots(): void
    {
        $sum = (float) $this->lots()->sum('qty');
        $this->update(['current_qty' => $sum]);
    }

}

