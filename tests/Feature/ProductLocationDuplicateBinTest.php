<?php

declare(strict_types = 1);

use App\Models\{Product, ProductLocation, User, Warehouse};

it('runs duplicate check only after base validation passes', function (): void {
    $admin    = User::factory()->admin()->create();
    $location = ProductLocation::factory()->create();

    $this->actingAs($admin)->put(route('product-locations.update', $location), [
        'warehouse_id' => 999_999,
        'aisle'        => 'A',
        'shelf'        => '1',
        'quantity'     => 1,
    ])->assertSessionHasErrors('warehouse_id');
});

it('rejects updating a location to duplicate bin of another row', function (): void {
    $admin     = User::factory()->admin()->create();
    $product   = Product::factory()->create();
    $warehouse = Warehouse::factory()->create();

    $first = ProductLocation::factory()->create([
        'product_id'   => $product->id,
        'warehouse_id' => $warehouse->id,
        'aisle'        => 'A',
        'shelf'        => '1',
        'quantity'     => 5,
    ]);

    $second = ProductLocation::factory()->create([
        'product_id'   => $product->id,
        'warehouse_id' => $warehouse->id,
        'aisle'        => 'B',
        'shelf'        => '2',
        'quantity'     => 3,
    ]);

    $response = $this->actingAs($admin)->put(route('product-locations.update', $second), [
        'warehouse_id' => $warehouse->id,
        'aisle'        => 'A',
        'shelf'        => '1',
        'quantity'     => 2,
    ]);

    $response->assertSessionHasErrors('warehouse_id');
    expect($second->fresh()->aisle)->toBe('B');
});
