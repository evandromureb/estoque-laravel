<?php

declare(strict_types = 1);

use App\Application\Inventory\Products\ListProductsForIndexQuery;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns paginated products without search', function (): void {
    Product::factory()->count(2)->create();

    $data = app(ListProductsForIndexQuery::class)->execute(null, 10);

    expect($data->products->total())->toBe(2);
});

it('filters products by name or sku when search is non-empty', function (): void {
    Product::factory()->create(['name' => 'Alpha Unique', 'sku' => 'SKU-A']);
    Product::factory()->create(['name' => 'Beta', 'sku' => 'SKU-B']);

    $byName = app(ListProductsForIndexQuery::class)->execute('Unique', 10);
    expect($byName->products->total())->toBe(1)
        ->and($byName->products->first()->name)->toContain('Unique');

    $bySku = app(ListProductsForIndexQuery::class)->execute('SKU-B', 10);
    expect($bySku->products->total())->toBe(1)
        ->and($bySku->products->first()->sku)->toBe('SKU-B');
});
