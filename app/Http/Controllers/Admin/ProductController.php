<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Models\Fragrance;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\Size;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const CLASSIFICATION_GROUPS = [
        'AUDIENCE' => 'Audience',
        'FRAGRANCE_FAMILY' => 'Fragrance Family',
        'OCCASION' => 'Occasion',
        'SEASON' => 'Season',
        'TIME_OF_DAY' => 'Time of Day',
        'INTENSITY' => 'Intensity',
        'LONGEVITY' => 'Longevity',
        'SCENT_CHARACTER' => 'Scent Character',
        'COLLECTION' => 'Collection',
    ];

    private const IMAGE_SLOTS = 6;

    public function index(): View
    {
        $products = Product::with('images')->orderBy('name')->get();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.form', ['product' => null, ...$this->formData()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);

        DB::transaction(function () use ($data) {
            $product = Product::create($data['product']);
            $this->syncRelations($product, $data);
        });

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $product->load(['images', 'sizes', 'classifications']);

        return view('admin.products.form', ['product' => $product, ...$this->formData()]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, false);

        DB::transaction(function () use ($product, $data) {
            $product->update($data['product']);
            $this->syncRelations($product, $data);
        });

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }

    private function formData(): array
    {
        return [
            'classificationGroups' => self::CLASSIFICATION_GROUPS,
            'classificationsByGroup' => Classification::orderBy('sort_order')->get()->groupBy('group'),
            'sizes' => Size::orderBy('sort_order')->get(),
            'fragrances' => Fragrance::orderBy('name')->pluck('name', 'id'),
            'imageSlots' => self::IMAGE_SLOTS,
        ];
    }

    private function syncRelations(Product $product, array $data): void
    {
        $product->classifications()->sync($data['classificationIds']);

        foreach ($data['sizes'] as $sizeId => $row) {
            if (empty($row['sku'])) {
                ProductSize::where('product_id', $product->id)->where('size_id', $sizeId)->delete();

                continue;
            }
            ProductSize::updateOrCreate(
                ['product_id' => $product->id, 'size_id' => $sizeId],
                [
                    'sku' => $row['sku'],
                    'mrp' => $row['mrp'] ?? 0,
                    'selling_price' => $row['selling_price'] ?? 0,
                    'cost_price' => $row['cost_price'] ?? 0,
                    'stock' => $row['stock'] ?? 0,
                    'status' => $row['status'] ?? 'ACTIVE',
                ],
            );
        }

        ProductImage::where('product_id', $product->id)->delete();
        foreach ($data['images'] as $index => $image) {
            if (empty($image['image_url'])) {
                continue;
            }
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $image['image_url'],
                'image_type' => $image['image_type'] ?? 'GALLERY',
                'sort_order' => $index,
                'is_primary' => ! empty($image['is_primary']),
                'status' => 'ACTIVE',
                'alt' => $image['alt'] ?? '',
            ]);
        }
    }

    private function validated(Request $request, bool $isCreate): array
    {
        $rules = [
            'sku' => ['required', 'string', 'max:60'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150'],
            'product_type' => ['required', 'in:READY_MADE,CUSTOM_PERFUME,GIFT_SET,BUNDLE,LIMITED_EDITION'],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'brand' => ['required', 'string', 'max:100'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_type' => ['required', 'in:PERCENT,FLAT,NONE'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'rating' => ['required', 'numeric', 'min:0', 'max:5'],
            'review_count' => ['required', 'integer', 'min:0'],
            'fragrance_id' => ['nullable', 'exists:fragrances,id'],
            'is_new_arrival' => ['sometimes', 'boolean'],
            'is_best_seller' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'is_limited_edition' => ['sometimes', 'boolean'],
            'is_trending' => ['sometimes', 'boolean'],
            'is_sale' => ['sometimes', 'boolean'],
            'classifications' => ['array'],
            'sizes' => ['array'],
            'sizes.*.sku' => ['nullable', 'string', 'max:60'],
            'sizes.*.mrp' => ['nullable', 'numeric', 'min:0'],
            'sizes.*.selling_price' => ['nullable', 'numeric', 'min:0'],
            'sizes.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'sizes.*.stock' => ['nullable', 'integer', 'min:0'],
            'sizes.*.status' => ['nullable', 'in:ACTIVE,INACTIVE'],
            'images' => ['array'],
            'images.*.image_url' => ['nullable', 'string', 'max:500'],
            'images.*.image_type' => ['nullable', 'in:MAIN,GALLERY,LIFESTYLE,PACKAGING,DETAIL'],
            'images.*.alt' => ['nullable', 'string', 'max:255'],
            'images.*.is_primary' => ['sometimes'],
        ];

        if ($isCreate) {
            $rules['id'] = ['required', 'string', 'max:40', 'unique:products,id'];
            $rules['sku'][] = 'unique:products,sku';
            $rules['slug'][] = 'unique:products,slug';
        }

        $validated = $request->validate($rules);

        $product = collect($validated)->only([
            'id', 'sku', 'name', 'slug', 'product_type', 'short_description', 'description', 'brand',
            'status', 'tax_rate', 'discount_type', 'discount_value', 'rating', 'review_count', 'fragrance_id',
        ])->all();

        foreach (['is_new_arrival', 'is_best_seller', 'is_featured', 'is_limited_edition', 'is_trending', 'is_sale'] as $flag) {
            $product[$flag] = $request->boolean($flag);
        }

        return [
            'product' => $product,
            'classificationIds' => $validated['classifications'] ?? [],
            'sizes' => $validated['sizes'] ?? [],
            'images' => array_values($validated['images'] ?? []),
        ];
    }
}
