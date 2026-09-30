<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classification extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'group', 'name', 'slug', 'sort_order', 'status'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    /** @return BelongsToMany<Product, $this> */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_classifications');
    }

    /** @return BelongsToMany<Fragrance, $this> */
    public function fragrances(): BelongsToMany
    {
        return $this->belongsToMany(Fragrance::class, 'fragrance_classifications');
    }

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group)->where('status', 'ACTIVE')->orderBy('sort_order');
    }
}
