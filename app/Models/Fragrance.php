<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fragrance extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'slug', 'short_description', 'description', 'gender', 'image', 'status',
    ];

    /** @return HasMany<FragranceNote, $this> */
    public function noteLinks(): HasMany
    {
        return $this->hasMany(FragranceNote::class)->orderBy('sort_order');
    }

    /** @return HasMany<FragranceSizePrice, $this> */
    public function sizePrices(): HasMany
    {
        return $this->hasMany(FragranceSizePrice::class);
    }

    /** @return BelongsToMany<Classification, $this> */
    public function classifications(): BelongsToMany
    {
        return $this->belongsToMany(Classification::class, 'fragrance_classifications');
    }

    /** @return HasMany<Product, $this> */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function basePriceForSize(string $sizeId): ?float
    {
        $row = $this->sizePrices->firstWhere('size_id', $sizeId);

        return $row ? (float) $row->base_price : null;
    }
}
