<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BottleController;
use App\Http\Controllers\Admin\CapController;
use App\Http\Controllers\Admin\ClassificationController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FragranceController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShippingMethodController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

$adminPrefix = config('app.admin_route_prefix');

if ($adminPrefix !== '') {
    // Root-mounted (e.g. local `php artisan serve`): bare "/" isn't a real
    // page here, send visitors to the actual storefront.
    Route::get('/', fn () => redirect(config('app.frontend_url')));
}

Route::prefix($adminPrefix)->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', ProductController::class)->except('show');
        Route::resource('fragrances', FragranceController::class)->except('show');
        Route::resource('bottles', BottleController::class)->except('show');
        Route::resource('caps', CapController::class)->except('show');
        Route::resource('sizes', SizeController::class)->except('show');
        Route::resource('classifications', ClassificationController::class)->except('show');
        Route::resource('offers', OfferController::class)->except('show');
        Route::resource('coupons', CouponController::class)->except('show')->parameters(['coupons' => 'coupon']);
        Route::resource('shipping-methods', ShippingMethodController::class)->except('show')
            ->parameters(['shipping-methods' => 'shippingMethod']);

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });
});
