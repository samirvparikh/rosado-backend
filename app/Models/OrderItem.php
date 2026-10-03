<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_type', 'product_name', 'size_name', 'fragrance_name', 'bottle_name',
        'cap_name', 'remarks', 'label_line1', 'label_line2', 'quantity', 'base_price', 'bottle_price', 'cap_price', 'discount', 'tax',
        'final_price', 'image',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'base_price' => 'decimal:2',
            'bottle_price' => 'decimal:2',
            'cap_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'final_price' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
