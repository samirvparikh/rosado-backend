<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bottle extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'code', 'image', 'layer_top', 'layer_left', 'layer_width', 'layer_z',
        'label_top', 'label_left', 'label_width', 'size_id', 'additional_price', 'stock', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'additional_price' => 'decimal:2',
            'stock' => 'integer',
            'sort_order' => 'integer',
            'layer_top' => 'float',
            'layer_left' => 'float',
            'layer_width' => 'float',
            'layer_z' => 'integer',
            'label_top' => 'float',
            'label_left' => 'float',
            'label_width' => 'float',
        ];
    }

    /** @return BelongsTo<Size, $this> */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /** @return HasMany<CustomizerLayerOverride, $this> */
    public function layerOverrides(): HasMany
    {
        return $this->hasMany(CustomizerLayerOverride::class);
    }

    /** @return HasOne<BottleInventory, $this> */
    public function inventory(): HasOne
    {
        return $this->hasOne(BottleInventory::class);
    }
}
