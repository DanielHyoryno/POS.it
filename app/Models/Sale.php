<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_no','subtotal','discount','tax','total','paid','change','status','user_id'
    ];

    protected $casts = [
        'subtotal'=> 'decimal:2',
        'discount'=> 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid'=> 'decimal:2',
        'change'=> 'decimal:2',
    ];

    public function items(){ 
        return $this->hasMany(SaleItem::class); 
    }

    public function payments(){ 
        return $this->hasMany(Payment::class); 
    }
    
    public function user(){ 
        return $this->belongsTo(User::class); 
    }
}
