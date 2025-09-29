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

    /* =========================
     * Relations
     * ========================= */

    // 🔹 Simple product → linked Item
    public function linkedItem()
    {
        return $this->belongsTo(Item::class, 'linked_item_id');
    }

    // 🔹 Composite product → BOM lines
    public function bomLines()
    {
        return $this->hasMany(ProductBomLine::class);
    }

    // 🔹 Alias for admin/backward compatibility
    public function item()
    {
        return $this->belongsTo(Item::class, 'linked_item_id');
    }

    // 🔹 Alias for admin/backward compatibility
    public function bomComponents()
    {
        // Use product_bom_lines instead of product_bom
        return $this->belongsToMany(Item::class, 'product_bom_lines', 'product_id', 'item_id')
            ->withPivot('qty')
            ->withTimestamps();
    }

    // 🔹 If admin expects $product->price, map to selling_price
    public function getPriceAttribute(): float
    {
        return (float) ($this->selling_price ?? 0);
    }

    // 🔹 If admin expects $product->item_id, map to linked_item_id
    public function getItemIdAttribute()
    {
        return $this->linked_item_id;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /* =========================
     * Helpers
     * ========================= */
    public function isSimple(): bool
    {
        return $this->type === 'simple';
    }

    public function isComposite(): bool
    {
        return $this->type === 'composite';
    }

    /**
     * 🔹 Estimated cost (uses Item.cost_price)
     */
    public function estimatedCost(): float
    {
        if ($this->isSimple() && $this->linkedItem) {
            $cost = (float) ($this->linkedItem->cost_price ?? 0);
            $qty  = (float) ($this->per_sale_qty ?? 0);
            return $cost > 0 ? $qty * $cost : 0.0;
        }

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
