<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Bottle;
use App\Models\Cap;
use App\Models\Fragrance;
use App\Models\Product;
use App\Models\Size;
use App\Support\CustomizerLayers;
use Illuminate\Support\Collection;

/**
 * What a CUSTOM_PERFUME product offers in the customizer, and at what price.
 *
 * - Sizes + base price come from the product's own size rows (Products >
 *   Sizes & Pricing). A product with no size rows offers every active size
 *   at base ₹0, so the fragrance's per-size price is the whole perfume cost.
 * - Fragrances/bottles/caps come from product_customizer_options; a type
 *   with no rows offers every active component of that type.
 * - With no CUSTOM_PERFUME product at all, a default "virtual" product with
 *   those same open rules keeps /custom-perfume working.
 */
class CustomizerCatalog
{
    public const DEFAULT_NAME = 'CUSTOM ROSADO PERFUME';

    /**
     * Null $reference = the default custom product (featured first). Returns
     * null when no custom product exists, which callers treat as "virtual".
     *
     * @throws ApiException when a specific product was asked for and isn't a live custom product
     */
    public function resolveProduct(?string $reference): ?Product
    {
        $query = Product::with(['sizes', 'customizerOptions', 'images'])
            ->where('product_type', 'CUSTOM_PERFUME')
            ->where('status', 'ACTIVE');

        if ($reference === null || $reference === '') {
            return $query->orderByDesc('is_featured')->orderBy('name')->first();
        }

        $product = $query->where(fn ($q) => $q->where('id', $reference)->orWhere('slug', $reference))->first();
        if (! $product) {
            throw new ApiException('This perfume cannot be customised.', 404, 'UNKNOWN_ENTITY');
        }

        return $product;
    }

    /** @return list<string>|null null = no restriction */
    public function allowedIds(?Product $product, string $type): ?array
    {
        $ids = $product?->customizerOptions->where('option_type', $type)->pluck('option_id')->values()->all() ?? [];

        return $ids === [] ? null : $ids;
    }

    /** @return Collection<int, array{size: Size, basePrice: float}> */
    public function sizes(?Product $product): Collection
    {
        $activeSizes = Size::where('status', 'ACTIVE')->orderBy('sort_order')->get();
        $rows = $product?->sizes->where('status', 'ACTIVE') ?? collect();

        if ($rows->isEmpty()) {
            return $activeSizes->map(fn (Size $size) => ['size' => $size, 'basePrice' => 0.0])->values();
        }

        return $activeSizes
            ->filter(fn (Size $size) => $rows->contains('size_id', $size->id))
            ->map(fn (Size $size) => ['size' => $size, 'basePrice' => (float) $rows->firstWhere('size_id', $size->id)->selling_price])
            ->values();
    }

    /** Null when the product doesn't offer this size. */
    public function basePriceFor(?Product $product, string $sizeId): ?float
    {
        return $this->sizes($product)->first(fn ($row) => $row['size']->id === $sizeId)['basePrice'] ?? null;
    }

    public function offers(?Product $product, string $type, string $id): bool
    {
        $allowed = $this->allowedIds($product, $type);

        return $allowed === null || in_array($id, $allowed, true);
    }

    /** GET /api/perfume-customizer/{product?} payload. */
    public function payload(?Product $product): array
    {
        $sizes = $this->sizes($product);
        $sizeIds = $sizes->map(fn ($row) => $row['size']->id)->all();

        $fragrances = Fragrance::with(['sizePrices', 'classifications', 'noteLinks.note'])
            ->where('status', 'ACTIVE')
            ->when($this->allowedIds($product, 'FRAGRANCE'), fn ($q, $ids) => $q->whereIn('id', $ids))
            ->orderBy('name')
            ->get();

        $bottles = Bottle::with(['inventory', 'layerOverrides'])
            ->where('status', 'ACTIVE')
            ->whereIn('size_id', $sizeIds)
            ->when($this->allowedIds($product, 'BOTTLE'), fn ($q, $ids) => $q->whereIn('id', $ids))
            ->orderBy('sort_order')
            ->get();

        $caps = Cap::with(['inventory', 'sizeMappings'])
            ->where('status', 'ACTIVE')
            ->when($this->allowedIds($product, 'CAP'), fn ($q, $ids) => $q->whereIn('id', $ids))
            ->orderBy('sort_order')
            ->get();

        $box = fn ($override) => CustomizerLayers::box($override);

        return [
            'product' => [
                'id' => $product?->id,
                'name' => $product?->name ?? self::DEFAULT_NAME,
                'slug' => $product?->slug,
                'shortDescription' => $product?->short_description ?? 'Compose your own perfume: fragrance, bottle and cap.',
                'image' => $product?->primaryImageUrl(),
            ],
            'canvas' => ['aspect' => CustomizerLayers::CANVAS_ASPECT],
            'sizes' => $sizes->map(fn ($row) => [
                'id' => $row['size']->id,
                'displayName' => $row['size']->display_name,
                'sizeML' => $row['size']->size_ml,
                'basePrice' => $row['basePrice'],
            ])->values(),
            'fragrances' => $fragrances
                // Only sizes with a defined price are purchasable for a fragrance.
                ->map(fn (Fragrance $f) => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'shortDescription' => $f->short_description,
                    'description' => $f->description,
                    'image' => $f->image,
                    'families' => $f->classifications->where('group', 'FRAGRANCE_FAMILY')->pluck('name')->values(),
                    'notes' => [
                        'top' => $f->noteLinks->where('note_type', 'TOP')->map(fn ($link) => $link->note?->name)->filter()->values(),
                        'heart' => $f->noteLinks->where('note_type', 'HEART')->map(fn ($link) => $link->note?->name)->filter()->values(),
                        'base' => $f->noteLinks->where('note_type', 'BASE')->map(fn ($link) => $link->note?->name)->filter()->values(),
                    ],
                    'prices' => (object) $f->sizePrices
                        ->whereIn('size_id', $sizeIds)
                        ->mapWithKeys(fn ($row) => [$row->size_id => (float) $row->base_price])
                        ->all(),
                    'layer' => CustomizerLayers::fragranceLayer($f),
                ])
                ->filter(fn ($f) => count((array) $f['prices']) > 0)
                ->values(),
            'bottles' => $bottles->map(fn (Bottle $b) => [
                'id' => $b->id,
                'name' => $b->name,
                'image' => $b->image,
                'sizeId' => $b->size_id,
                'price' => (float) $b->additional_price,
                'inStock' => ($b->inventory?->available_stock ?? 0) > 0,
                'layer' => CustomizerLayers::bottleLayer($b),
                'label' => CustomizerLayers::labelBox($b),
                'overrides' => [
                    'caps' => (object) $b->layerOverrides->where('layer_type', 'CAP')->mapWithKeys(fn ($o) => [$o->layer_id => $box($o)])->all(),
                    'fragrances' => (object) $b->layerOverrides->where('layer_type', 'FRAGRANCE')->mapWithKeys(fn ($o) => [$o->layer_id => $box($o)])->all(),
                ],
            ])->values(),
            'caps' => $caps->map(fn (Cap $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'image' => $c->image,
                'price' => (float) $c->additional_price,
                'inStock' => ($c->inventory?->available_stock ?? 0) > 0,
                // Empty = fits every size.
                'sizeIds' => $c->sizeMappings->where('status', 'ACTIVE')->pluck('size_id')->values(),
                'layer' => CustomizerLayers::capLayer($c),
            ])->values(),
        ];
    }
}
