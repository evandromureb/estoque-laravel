<?php

declare(strict_types = 1);

use App\Application\Inventory\Warehouses\DeleteWarehouseAction;
use App\Domain\Inventory\Exceptions\WarehouseHasStockLocationsException;
use App\Models\{Product, ProductLocation, Warehouse};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('deletes a warehouse without stock locations', function (): void {
    $warehouse = Warehouse::factory()->create();
    $action    = app(DeleteWarehouseAction::class);

    $action->execute($warehouse);

    expect(Warehouse::query()->find($warehouse->id))->toBeNull();
});

it('throws when warehouse has product locations', function (): void {
    $warehouse = Warehouse::factory()->create();
    $product   = Product::factory()->create();
    ProductLocation::factory()->create([
        'product_id'   => $product->id,
        'warehouse_id' => $warehouse->id,
    ]);
    $action = app(DeleteWarehouseAction::class);

    $action->execute($warehouse);
})->throws(WarehouseHasStockLocationsException::class);
