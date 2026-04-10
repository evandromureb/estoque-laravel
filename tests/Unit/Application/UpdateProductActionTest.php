<?php

declare(strict_types = 1);

use App\Application\Inventory\Products\UpdateProductAction;
use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('does not regenerate qr when sku is unchanged and qr path exists', function (): void {
    Storage::fake('public');

    $this->mock(ProductQrCodeGenerator::class, function ($mock): void {
        $mock->shouldReceive('generateAndStore')->never();
    });

    $product = Product::factory()->create([
        'sku'          => 'FIXED-SKU',
        'qr_code_path' => 'qrcodes/exists.png',
    ]);

    app(UpdateProductAction::class)->execute($product, [
        'category_id'   => $product->category_id,
        'name'          => 'Novo nome',
        'sku'           => 'FIXED-SKU',
        'price'         => 10,
        'minimum_stock' => 0,
    ], []);
});

it('regenerates qr when sku changes', function (): void {
    Storage::fake('public');

    $this->mock(ProductQrCodeGenerator::class, function ($mock): void {
        $mock->shouldReceive('generateAndStore')->once();
    });

    $product = Product::factory()->create([
        'sku'          => 'OLD',
        'qr_code_path' => 'qrcodes/x.png',
    ]);

    app(UpdateProductAction::class)->execute($product, [
        'category_id'   => $product->category_id,
        'name'          => $product->name,
        'sku'           => 'NEW-SKU',
        'price'         => $product->price,
        'minimum_stock' => $product->minimum_stock,
    ], []);
});

it('regenerates qr when qr path is missing', function (): void {
    Storage::fake('public');

    $this->mock(ProductQrCodeGenerator::class, function ($mock): void {
        $mock->shouldReceive('generateAndStore')->once();
    });

    $product = Product::factory()->create([
        'sku'          => 'S',
        'qr_code_path' => null,
    ]);

    app(UpdateProductAction::class)->execute($product, [
        'category_id'   => $product->category_id,
        'name'          => $product->name,
        'sku'           => 'S',
        'price'         => $product->price,
        'minimum_stock' => $product->minimum_stock,
    ], []);
});

it('stores new images when provided', function (): void {
    Storage::fake('public');

    $this->mock(ProductQrCodeGenerator::class, function ($mock): void {
        $mock->shouldReceive('generateAndStore')->never();
    });

    $product = Product::factory()->create([
        'sku'          => 'IMG',
        'qr_code_path' => 'qrcodes/has.png',
    ]);

    $file = UploadedFile::fake()->image('n.jpg', 10, 10);

    app(UpdateProductAction::class)->execute($product, [
        'category_id'   => $product->category_id,
        'name'          => $product->name,
        'sku'           => 'IMG',
        'price'         => $product->price,
        'minimum_stock' => $product->minimum_stock,
    ], [$file]);

    expect($product->images()->count())->toBe(1);
});
