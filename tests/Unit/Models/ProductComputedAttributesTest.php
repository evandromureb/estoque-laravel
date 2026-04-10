<?php

declare(strict_types = 1);

use App\Models\{Product, ProductLocation, Warehouse};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('sums stock from loaded locations relation', function (): void {
    $product   = Product::factory()->create(['minimum_stock' => 0]);
    $warehouse = Warehouse::factory()->create();
    ProductLocation::factory()->create(['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'quantity' => 4]);
    ProductLocation::factory()->create(['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'quantity' => 5]);

    $product->load('locations');

    expect($product->total_stock_quantity)->toBe(9);
});

it('uses locations_sum_quantity attribute when present', function (): void {
    $product = Product::factory()->make();
    $product->setRawAttributes(array_merge($product->getAttributes(), ['locations_sum_quantity' => 7]));

    expect($product->total_stock_quantity)->toBe(7);
});

it('queries sum when locations are not loaded and sum attribute is absent', function (): void {
    $product   = Product::factory()->create();
    $warehouse = Warehouse::factory()->create();
    ProductLocation::factory()->create(['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'quantity' => 11]);

    $fresh = Product::query()->findOrFail($product->id);

    expect($fresh->total_stock_quantity)->toBe(11);
});

it('returns false for below minimum when minimum is zero or negative', function (): void {
    $product = Product::factory()->create(['minimum_stock' => 0]);

    expect($product->isBelowMinimumStock())->toBeFalse();
});

it('returns true when total stock is below positive minimum', function (): void {
    $product   = Product::factory()->create(['minimum_stock' => 100]);
    $warehouse = Warehouse::factory()->create();
    ProductLocation::factory()->create(['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'quantity' => 5]);

    expect($product->fresh()->isBelowMinimumStock())->toBeTrue();
});

it('returns false when total stock meets minimum', function (): void {
    $product   = Product::factory()->create(['minimum_stock' => 10]);
    $warehouse = Warehouse::factory()->create();
    ProductLocation::factory()->create(['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'quantity' => 10]);

    expect($product->fresh()->isBelowMinimumStock())->toBeFalse();
});

it('scopes products where aggregate stock is below minimum_stock', function (): void {
    $warehouse = Warehouse::factory()->create();
    $below     = Product::factory()->create(['minimum_stock' => 20, 'sku' => 'SCOPE-BELOW']);
    ProductLocation::factory()->create(['product_id' => $below->id, 'warehouse_id' => $warehouse->id, 'quantity' => 3]);

    $ok = Product::factory()->create(['minimum_stock' => 5, 'sku' => 'SCOPE-OK']);
    ProductLocation::factory()->create(['product_id' => $ok->id, 'warehouse_id' => $warehouse->id, 'quantity' => 10]);

    $ids = Product::query()->whereTotalStockBelowMinimum()->pluck('id');

    expect($ids->contains($below->id))->toBeTrue()
        ->and($ids->contains($ok->id))->toBeFalse();
});
