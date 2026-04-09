<?php

declare(strict_types = 1);

use App\Http\Controllers\Api\V1\{
    CategoryController,
    ProductController,
    ProductImageController,
    ProductLocationController,
    UserController,
    WarehouseController,
};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware('auth')
    ->group(function (): void {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('warehouses', WarehouseController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('product-locations', ProductLocationController::class);
        Route::apiResource('users', UserController::class);

        Route::post('products/{product}/images', [ProductImageController::class, 'store'])
            ->name('products.images.store');
        Route::delete('products/{product}/images/{productImage}', [ProductImageController::class, 'destroy'])
            ->name('products.images.destroy');
    });
