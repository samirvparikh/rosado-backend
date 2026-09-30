<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bottle extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'code', 'image', 'size_id', 'additional_price', 'stock', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'additional_price' => 'decimal:2',
            'stock' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /** @return BelongsTo<Size, $this> */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /** @return HasOne<BottleInventory, $this> */
    public function inventory(): HasOne
    {
        return $this->hasOne(BottleInventory::class);
    }
}
