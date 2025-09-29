<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'item_id','change_qty','reason','reference_type','reference_id','note'
    ];

    protected $casts = [
        'change_qty' => 'decimal:3',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

