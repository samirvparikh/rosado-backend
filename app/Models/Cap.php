<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cap extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'code', 'image', 'layer_top', 'layer_left', 'layer_width', 'layer_z', 'additional_price', 'stock', 'status', 'sort_order'];

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
        ];
    }

    /** @return HasOne<CapInventory, $this> */
    public function inventory(): HasOne
    {
        return $this->hasOne(CapInventory::class);
    }

    /** @return HasMany<CapSizeMapping, $this> */
    public function sizeMappings(): HasMany
    {
        return $this->hasMany(CapSizeMapping::class);
    }

    /** Empty mappings (V1 default) mean the cap is available for every size. */
    public function isCompatibleWithSize(string $sizeId): bool
    {
        $active = $this->sizeMappings->where('status', 'ACTIVE');

        return $active->isEmpty() || $active->contains('size_id', $sizeId);
    }
}
