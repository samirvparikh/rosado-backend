<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'name', 'size_ml', 'display_name', 'sort_order', 'status'];

    protected function casts(): array
    {
        return [
            'size_ml' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /** @return HasMany<Bottle, $this> */
    public function bottles(): HasMany
    {
        return $this->hasMany(Bottle::class);
    }

    /** @return HasMany<ProductSize, $this> */
    public function productSizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }
}
