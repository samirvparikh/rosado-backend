<?php

namespace App\Support;

use App\Models\Bottle;
use App\Models\Cap;
use App\Models\Classification;
use App\Models\Fragrance;
use App\Models\Note;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\Size;

/**
 * Maps Eloquent models onto the exact camelCase JSON shapes the storefront's
 * TypeScript types (src/types/*.ts) already expect, so the frontend service
 * layer needs no reshaping beyond swapping the mock call for a fetch call.
 */
class Presenters
{
    public static function size(Size $size): array
    {
        return [
            'id' => $size->id,
            'name' => $size->name,
            'sizeML' => $size->size_ml,
            'displayName' => $size->display_name,
            'sortOrder' => $size->sort_order,
            'status' => $size->status,
        ];
    }

    public static function bottle(Bottle $bottle): array
    {
        return [
            'id' => $bottle->id,
            'name' => $bottle->name,
            'code' => $bottle->code,
            'image' => $bottle->image,
            'sizeId' => $bottle->size_id,
            'additionalPrice' => (float) $bottle->additional_price,
            'stock' => $bottle->inventory?->available_stock ?? 0,
            'status' => $bottle->status,
            'sortOrder' => $bottle->sort_order,
        ];
    }

    public static function cap(Cap $cap): array
    {
        return [
            'id' => $cap->id,
            'name' => $cap->name,
            'code' => $cap->code,
            'image' => $cap->image,
            'additionalPrice' => (float) $cap->additional_price,
            'stock' => $cap->inventory?->available_stock ?? 0,
            'status' => $cap->status,
            'sortOrder' => $cap->sort_order,
        ];
    }

    public static function classification(Classification $classification): array
    {
        return [
            'id' => $classification->id,
            'name' => $classification->name,
            'slug' => $classification->slug,
            'status' => $classification->status,
            'sortOrder' => $classification->sort_order,
        ];
    }

    public static function note(Note $note): array
    {
        return [
            'id' => $note->id,
            'name' => $note->name,
            'noteType' => $note->note_type,
            'image' => null,
            'status' => $note->status,
        ];
    }

    /** Expects $fragrance to have `noteLinks.note` and `classifications` eager loaded. */
    public static function fragranceWithNotes(Fragrance $fragrance): array
    {
        $byType = fn (string $type) => $fragrance->noteLinks
            ->where('note_type', $type)
            ->sortBy('sort_order')
            ->map(fn ($link) => self::note($link->note))
            ->values()
            ->all();

        return [
            'id' => $fragrance->id,
            'name' => $fragrance->name,
            'slug' => $fragrance->slug,
            'shortDescription' => $fragrance->short_description,
            'description' => $fragrance->description,
            'gender' => $fragrance->gender,
            'image' => $fragrance->image,
            'status' => $fragrance->status,
            'familyIds' => $fragrance->classifications->where('group', 'FRAGRANCE_FAMILY')->pluck('id')->values()->all(),
            'characterIds' => $fragrance->classifications->where('group', 'SCENT_CHARACTER')->pluck('id')->values()->all(),
            'notes' => [
                'top' => $byType('TOP'),
                'heart' => $byType('HEART'),
                'base' => $byType('BASE'),
            ],
            'families' => $fragrance->classifications->where('group', 'FRAGRANCE_FAMILY')->pluck('name')->values()->all(),
            'sizePrices' => $fragrance->sizePrices->map(fn ($row) => [
                'fragranceId' => $row->fragrance_id,
                'sizeId' => $row->size_id,
                'basePrice' => (float) $row->base_price,
            ])->values(),
        ];
    }

    public static function productBase(Product $product): array
    {
        return [
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'slug' => $product->slug,
            'productType' => $product->product_type,
            'shortDescription' => $product->short_description,
            'description' => $product->description,
            'brand' => $product->brand,
            'status' => $product->status,
            'basePrice' => $product->base_price === null ? null : (float) $product->base_price,
            'salePrice' => $product->sale_price === null ? null : (float) $product->sale_price,
            'costPrice' => $product->cost_price === null ? null : (float) $product->cost_price,
            'mrp' => $product->mrp === null ? null : (float) $product->mrp,
            'taxRate' => (float) $product->tax_rate,
            'discountType' => $product->discount_type,
            'discountValue' => (float) $product->discount_value,
            'rating' => (float) $product->rating,
            'reviewCount' => $product->review_count,
            'isNewArrival' => $product->is_new_arrival,
            'isBestSeller' => $product->is_best_seller,
            'isFeatured' => $product->is_featured,
            'isLimitedEdition' => $product->is_limited_edition,
            'isTrending' => $product->is_trending,
            'isSale' => $product->is_sale,
        ];
    }

    /** Expects images/sizes/classifications eager loaded on $product. */
    public static function productListItem(Product $product): array
    {
        return [
            ...self::productBase($product),
            'primaryImage' => $product->primaryImageUrl() ?? '',
            'fromPrice' => $product->fromPrice() ?? 0,
            'defaultSizeId' => $product->sizes->where('status', 'ACTIVE')->sortBy('selling_price')->first()->size_id ?? '',
            'badges' => $product->badges(),
            'audienceIds' => $product->classificationIdsByGroup('AUDIENCE'),
            'familyNames' => $product->classifications->where('group', 'FRAGRANCE_FAMILY')->pluck('name')->values()->all(),
        ];
    }

    public static function productSize(ProductSize $row): array
    {
        return [
            'id' => (string) $row->id,
            'productId' => $row->product_id,
            'sizeId' => $row->size_id,
            'sku' => $row->sku,
            'mrp' => (float) $row->mrp,
            'sellingPrice' => (float) $row->selling_price,
            'costPrice' => (float) $row->cost_price,
            'stock' => $row->stock,
            'status' => $row->status,
        ];
    }

    public static function productImage(ProductImage $image): array
    {
        return [
            'id' => (string) $image->id,
            'productId' => $image->product_id,
            'imageUrl' => $image->image_url,
            'imageType' => $image->image_type,
            'sortOrder' => $image->sort_order,
            'isPrimary' => $image->is_primary,
            'status' => $image->status,
            'alt' => $image->alt,
        ];
    }

    /** Expects images/sizes/classifications/fragrance eager loaded on $product. */
    public static function productDetail(Product $product): array
    {
        return [
            ...self::productBase($product),
            'sizes' => $product->sizes->map(fn ($row) => self::productSize($row))->values()->all(),
            'images' => $product->images->map(fn ($image) => self::productImage($image))->values()->all(),
            'classifications' => [
                'audienceIds' => $product->classificationIdsByGroup('AUDIENCE'),
                'fragranceFamilyIds' => $product->classificationIdsByGroup('FRAGRANCE_FAMILY'),
                'occasionIds' => $product->classificationIdsByGroup('OCCASION'),
                'seasonIds' => $product->classificationIdsByGroup('SEASON'),
                'timeOfDayIds' => $product->classificationIdsByGroup('TIME_OF_DAY'),
                'intensityIds' => $product->classificationIdsByGroup('INTENSITY'),
                'longevityIds' => $product->classificationIdsByGroup('LONGEVITY'),
                'scentCharacterIds' => $product->classificationIdsByGroup('SCENT_CHARACTER'),
                'collectionIds' => $product->classificationIdsByGroup('COLLECTION'),
            ],
            'fragranceId' => $product->fragrance_id,
        ];
    }

    public static function orderItem(OrderItem $item): array
    {
        return [
            'productType' => $item->product_type,
            'productName' => $item->product_name,
            'sizeName' => $item->size_name,
            'fragranceName' => $item->fragrance_name,
            'bottleName' => $item->bottle_name,
            'capName' => $item->cap_name,
            'quantity' => $item->quantity,
            'basePrice' => (float) $item->base_price,
            'bottlePrice' => (float) $item->bottle_price,
            'capPrice' => (float) $item->cap_price,
            'discount' => (float) $item->discount,
            'tax' => (float) $item->tax,
            'finalPrice' => (float) $item->final_price,
            'image' => $item->image,
        ];
    }

    /** Expects `items` eager loaded on $order. */
    public static function order(Order $order): array
    {
        return [
            'id' => (string) $order->id,
            'orderNumber' => $order->order_number,
            'status' => $order->status,
            'createdAt' => $order->created_at?->toIso8601String(),
            'customer' => [
                'fullName' => $order->customer_full_name,
                'mobile' => $order->customer_mobile,
                'email' => $order->customer_email,
                'address' => $order->customer_address,
                'city' => $order->customer_city,
                'state' => $order->customer_state,
                'pincode' => $order->customer_pincode,
            ],
            'shippingMethod' => $order->shipping_method_id,
            'shippingMethodLabel' => $order->shipping_method_label,
            'paymentMethod' => $order->payment_method,
            'couponCode' => $order->coupon_code,
            'items' => $order->items->map(fn ($item) => self::orderItem($item))->values(),
            'subtotal' => (float) $order->subtotal,
            'discount' => (float) $order->discount,
            'tax' => (float) $order->tax,
            'shipping' => (float) $order->shipping,
            'finalPrice' => (float) $order->final_price,
        ];
    }
}
