<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['sale_id','method','amount','notes'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function sale(){
        return $this->belongsTo(Sale::class); 
    }
}
