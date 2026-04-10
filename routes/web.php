<?php

declare(strict_types = 1);

use App\Http\Controllers\{DashboardController, ProductImageController, ProfileController};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', function (): View|RedirectResponse {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('users/{user}/api-token', [\App\Http\Controllers\UserController::class, 'storeApiToken'])
        ->name('users.api-token.store');
    Route::delete('users/{user}/api-token', [\App\Http\Controllers\UserController::class, 'destroyApiToken'])
        ->name('users.api-token.destroy');
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['create', 'edit', 'show']);
    Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class)->except(['create', 'edit', 'show']);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class)->except(['create', 'edit', 'show']);
    Route::post('products/{product}/images', [ProductImageController::class, 'store'])->name('products.images.store');
    Route::delete('products/{product}/images/{productImage}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');
    Route::resource('products', \App\Http\Controllers\ProductController::class)->except(['create', 'edit']);
    Route::resource('product-locations', \App\Http\Controllers\ProductLocationController::class)->only(['store', 'update', 'destroy']);
});

require __DIR__ . '/auth.php';
