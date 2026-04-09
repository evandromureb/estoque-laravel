<?php

declare(strict_types = 1);

use App\Application\Inventory\Dashboard\GetDashboardDataQuery;
use App\Models\{Product, ProductLocation, User, Warehouse};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('aggregates dashboard metrics and low stock lists', function (): void {
    Product::factory()->count(2)->create();
    Warehouse::factory()->count(3)->create();
    User::factory()->count(4)->create();

    $low = Product::factory()->create([
        'name'          => 'Dash Low Stock',
        'sku'           => 'DASH-LOW',
        'minimum_stock' => 50,
    ]);
    $warehouse = Warehouse::factory()->create();
    ProductLocation::factory()->create([
        'product_id'   => $low->id,
        'warehouse_id' => $warehouse->id,
        'quantity'     => 5,
    ]);

    Product::factory()->create([
        'additional_info' => 'Nota importante para o dashboard',
    ]);

    $data = app(GetDashboardDataQuery::class)->execute();

    expect($data->totalProducts)->toBeGreaterThanOrEqual(3)
        ->and($data->totalWarehouses)->toBeGreaterThanOrEqual(4)
        ->and($data->totalUsers)->toBeGreaterThanOrEqual(4)
        ->and($data->lowStockProductsCount)->toBeGreaterThanOrEqual(1)
        ->and($data->belowMinimumStockProducts->pluck('sku')->contains('DASH-LOW'))->toBeTrue()
        ->and($data->recentProductNotes->isNotEmpty())->toBeTrue();
});
