<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BottleController;
use App\Http\Controllers\Api\CapController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ClassificationController;
use App\Http\Controllers\Api\CustomPerfumeController;
use App\Http\Controllers\Api\CustomizerController;
use App\Http\Controllers\Api\FragranceController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShippingMethodController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Masters (spec section 33)
Route::get('/sizes', [SizeController::class, 'index']);
Route::get('/audiences', [ClassificationController::class, 'audiences']);
Route::get('/fragrance-families', [ClassificationController::class, 'fragranceFamilies']);
Route::get('/occasions', [ClassificationController::class, 'occasions']);
Route::get('/seasons', [ClassificationController::class, 'seasons']);
Route::get('/time-of-day', [ClassificationController::class, 'timeOfDay']);
Route::get('/intensities', [ClassificationController::class, 'intensities']);
Route::get('/longevities', [ClassificationController::class, 'longevities']);
Route::get('/scent-characters', [ClassificationController::class, 'scentCharacters']);
Route::get('/collections', [ClassificationController::class, 'collections']);
Route::get('/shop-filters', [ClassificationController::class, 'shopFilters']);

Route::get('/fragrances', [FragranceController::class, 'index']);
Route::get('/fragrances/{id}', [FragranceController::class, 'show']);

// Bottle/Cap are custom-perfume components, never standalone products (Rules 1-4).
Route::get('/bottles', [BottleController::class, 'index']);
Route::get('/caps', [CapController::class, 'index']);

// Homepage offer header marquee -- managed in admin under Content > Offer Header.
Route::get('/offers', [OfferController::class, 'index']);

Route::get('/shipping-methods', [ShippingMethodController::class, 'index']);

// Products -- specific routes must be registered before the {slug} wildcard.
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/best-sellers', [ProductController::class, 'bestSellers']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/{id}/recommendations', [ProductController::class, 'recommendations']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Custom Perfume Builder -- backend is the pricing/compatibility authority (Rules 13-17).
Route::get('/perfume-customizer/{product?}', [CustomizerController::class, 'show']);
Route::post('/custom-perfume/validate', [CustomPerfumeController::class, 'validateConfiguration']);
Route::post('/custom-perfume/price', [CustomPerfumeController::class, 'price']);

// Cart pricing/quoting -- never trust client-supplied totals.
Route::post('/cart/price-ready-made', [CartController::class, 'priceReadyMade']);
Route::post('/cart/price-custom', [CartController::class, 'priceCustom']);
Route::post('/cart/quote', [CartController::class, 'quote']);

// Orders -- guest checkout allowed; linked to the account when a token is sent.
Route::post('/orders', [OrderController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);

    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);
});

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
