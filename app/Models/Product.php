<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'sku', 'name', 'slug', 'product_type', 'short_description', 'description', 'brand', 'tags',
        'status', 'in_stock', 'base_price', 'sale_price', 'cost_price', 'mrp', 'tax_rate', 'discount_type',
        'discount_value', 'rating', 'review_count', 'is_new_arrival', 'is_best_seller', 'is_featured',
        'is_limited_edition', 'is_trending', 'is_sale', 'fragrance_id',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'mrp' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'rating' => 'decimal:1',
            'review_count' => 'integer',
            'is_new_arrival' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_featured' => 'boolean',
            'is_limited_edition' => 'boolean',
            'is_trending' => 'boolean',
            'is_sale' => 'boolean',
            'tags' => 'array',
            'in_stock' => 'boolean',
        ];
    }

    /** @return HasMany<ProductImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /** @return HasMany<ProductSize, $this> */
    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    /** @return BelongsToMany<Classification, $this> */
    public function classifications(): BelongsToMany
    {
        return $this->belongsToMany(Classification::class, 'product_classifications');
    }

    /** @return BelongsTo<Fragrance, $this> */
    public function fragrance(): BelongsTo
    {
        return $this->belongsTo(Fragrance::class);
    }

    public function primaryImageUrl(): ?string
    {
        $image = $this->images->firstWhere('is_primary', true) ?? $this->images->first();

        return $image?->image_url;
    }

    /** Ordered by gallery position, the image after the primary one -- shown on card hover. */
    public function secondaryImageUrl(): ?string
    {
        $primary = $this->primaryImageUrl();

        return $this->images->first(fn ($image) => $image->image_url !== $primary)?->image_url;
    }

    public function fromPrice(): ?float
    {
        $prices = $this->sizes->where('status', 'ACTIVE')->pluck('selling_price');

        return $prices->isEmpty() ? null : (float) $prices->min();
    }

    /** Sold out when the admin switched In Stock off, or no active size has stock left. */
    public function isSoldOut(): bool
    {
        return ! $this->in_stock || ! $this->sizes->contains(fn ($row) => $row->status === 'ACTIVE' && $row->stock > 0);
    }

    /** Cheapest purchasable size, falling back to the cheapest active one when everything is sold out. */
    public function defaultSizeId(): string
    {
        $active = $this->sizes->where('status', 'ACTIVE')->sortBy('selling_price');

        return ($active->firstWhere(fn ($row) => $row->stock > 0) ?? $active->first())?->size_id ?? '';
    }

    /** @return list<string> */
    public function tagList(): array
    {
        return array_values(array_filter(array_map('trim', $this->tags ?? []), fn ($tag) => $tag !== ''));
    }

    public function badges(): array
    {
        $badges = [];
        if ($this->is_new_arrival) {
            $badges[] = 'New';
        }
        if ($this->is_best_seller) {
            $badges[] = 'Best Seller';
        }
        if ($this->is_featured) {
            $badges[] = 'Featured';
        }
        if ($this->is_limited_edition) {
            $badges[] = 'Limited Edition';
        }
        if ($this->is_trending) {
            $badges[] = 'Trending';
        }
        if ($this->is_sale) {
            $badges[] = 'Sale';
        }

        return $badges;
    }

    public function classificationIdsByGroup(string $group): array
    {
        return $this->classifications->where('group', $group)->pluck('id')->values()->all();
    }
}
