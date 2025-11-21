<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'type',
        'selling_price',
        'is_active',
        'linked_item_id',
        'per_sale_qty',
    ];

    protected $casts = [
        'is_active'     => 'bool',
        'selling_price' => 'decimal:2',
        'per_sale_qty'  => 'decimal:3',
    ];



    public function linkedItem()
    {
        return $this->belongsTo(Item::class, 'linked_item_id');
    }

    public function bomLines()
    {
        return $this->hasMany(ProductBomLine::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'linked_item_id');
    }

    public function bomComponents()
    {
        return $this->belongsToMany(Item::class, 'product_bom_lines', 'product_id', 'item_id')
            ->withPivot('qty')
            ->withTimestamps();
    }

    public function getPriceAttribute(): float
    {
        return (float) ($this->selling_price ?? 0);
    }

    public function getItemIdAttribute()
    {
        return $this->linked_item_id;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }



    public function isSimple(): bool
    {
        return $this->type === 'simple';
    }

    public function isComposite(): bool
    {
        return $this->type === 'composite';
    }



    public function estimatedCost(): float
    {

        // ini buat produk yang item satuan
        if ($this->isSimple() && $this->linkedItem) {
            $cost = (float) ($this->linkedItem->cost_price ?? 0);
            $qty  = (float) ($this->per_sale_qty ?? 0);
            return $cost > 0 ? $qty * $cost : 0.0;
        }

        // ini buat produk yang merupakan gabungan dari banyak item
        if ($this->isComposite()) {
            $total = 0.0;
            foreach ($this->bomLines()->with('item')->get() as $line) {
                $price = (float) ($line->item->cost_price ?? 0);
                $total += $price * (float) $line->qty;
            }
            return $total;
        }

        return 0.0;
    }
}
