<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Product;
use App\Models\ProductSize;

/** Server-authoritative pricing for ready-made (non-custom) cart lines. */
class ReadyMadePricer
{
    /**
     * @return array{id: string, productType: string, productId: string, productName: string, slug: string, image: ?string, sizeId: string, sizeName: string, quantity: int, unitPrice: float, lineTotal: float}
     *
     * @throws ApiException
     */
    public function price(string $productId, string $sizeId, int $quantity): array
    {
        $product = Product::where('id', $productId)->where('status', 'ACTIVE')->first();
        $sizeRow = ProductSize::where('product_id', $productId)->where('size_id', $sizeId)->where('status', 'ACTIVE')->first();

        if (! $product || ! $sizeRow) {
            throw new ApiException('This size is unavailable.', 422, 'UNKNOWN_ENTITY');
        }

        if ($sizeRow->stock < $quantity) {
            throw new ApiException('Insufficient stock for this size.', 422, 'OUT_OF_STOCK');
        }

        return [
            'id' => "{$product->id}-{$sizeRow->size_id}",
            'productType' => 'READY_MADE',
            'productId' => $product->id,
            'productName' => $product->name,
            'slug' => $product->slug,
            'image' => $product->primaryImageUrl(),
            'sizeId' => $sizeRow->size_id,
            'sizeName' => $sizeRow->size->display_name,
            'quantity' => $quantity,
            'unitPrice' => (float) $sizeRow->selling_price,
            'lineTotal' => (float) $sizeRow->selling_price * $quantity,
        ];
    }
}
