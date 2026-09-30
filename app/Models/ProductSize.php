<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSize extends Model
{
    protected $fillable = ['product_id', 'size_id', 'sku', 'mrp', 'selling_price', 'cost_price', 'stock', 'status'];

    protected function casts(): array
    {
        return [
            'mrp' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    /** @return BelongsTo<Size, $this> */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
