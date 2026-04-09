<?php

declare(strict_types = 1);

use App\Models\{Product, ProductImage};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('exposes the owning product relation', function (): void {
    $product = Product::factory()->create();
    $image   = ProductImage::factory()->for($product)->create();

    expect($image->product)->toBeInstanceOf(Product::class)
        ->and($image->product->is($product))->toBeTrue();
});

it('resolves public url for bundled assets vs storage paths', function (): void {
    expect(ProductImage::isBundledPublicAsset('assets/images/products/demo-1.png'))->toBeTrue()
        ->and(ProductImage::isBundledPublicAsset('product_images/x.jpg'))->toBeFalse();

    expect(ProductImage::publicUrlForPath('assets/images/products/demo-1.png'))
        ->toBe(asset('assets/images/products/demo-1.png'))
        ->and(ProductImage::publicUrlForPath('product_images/x.jpg'))
        ->toBe(asset('storage/product_images/x.jpg'));
});
