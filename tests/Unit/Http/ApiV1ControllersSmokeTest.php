<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

uses(TestCase::class);

it('registers api v1 inventory routes', function (): void {
    expect(Route::has('api.v1.categories.index'))->toBeTrue()
        ->and(Route::has('api.v1.warehouses.index'))->toBeTrue()
        ->and(Route::has('api.v1.products.index'))->toBeTrue()
        ->and(Route::has('api.v1.product-locations.index'))->toBeTrue()
        ->and(Route::has('api.v1.users.index'))->toBeTrue()
        ->and(Route::has('api.v1.products.images.store'))->toBeTrue()
        ->and(Route::has('api.v1.products.images.destroy'))->toBeTrue();
});
