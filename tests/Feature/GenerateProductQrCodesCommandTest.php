<?php

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\{Category, Product};
use Illuminate\Support\Facades\Storage;

it('gera qr codes para todos os produtos sem only-missing', function (): void {
    Storage::fake('public');

    $category = Category::factory()->create();

    $products = Product::factory()
        ->count(2)
        ->for($category)
        ->create(['qr_code_path' => null]);

    $this->artisan('inventory:generate-product-qr-codes', ['--chunk' => 1])
        ->assertSuccessful();

    foreach ($products as $product) {
        $product->refresh();
        expect($product->qr_code_path)->not->toBeNull()
            ->and($product->qr_code_path)->toContain('qrcodes/');
        Storage::disk('public')->assertExists($product->qr_code_path);
    }
});

it('com --only-missing ignora produtos que já têm arquivo no disco', function (): void {
    Storage::fake('public');

    $category = Category::factory()->create();

    $complete = Product::factory()->for($category)->create([
        'qr_code_path' => 'qrcodes/existing.svg',
    ]);
    Storage::disk('public')->put($complete->qr_code_path, '<svg></svg>');

    $missing = Product::factory()->for($category)->create(['qr_code_path' => null]);

    $this->artisan('inventory:generate-product-qr-codes', ['--only-missing' => true])
        ->assertSuccessful();

    $complete->refresh();
    $missing->refresh();

    expect($complete->qr_code_path)->toBe('qrcodes/existing.svg');
    Storage::disk('public')->assertExists('qrcodes/existing.svg');

    expect($missing->qr_code_path)->not->toBeNull();
    Storage::disk('public')->assertExists($missing->qr_code_path);
});

it('falha com código de saída quando a geração lança exceção', function (): void {
    Storage::fake('public');

    $category = Category::factory()->create();
    Product::factory()->for($category)->create(['qr_code_path' => null]);

    $this->mock(ProductQrCodeGenerator::class, function ($mock): void {
        $mock->shouldReceive('generateAndStore')
            ->once()
            ->andThrow(new \RuntimeException('falha simulada'));
    });

    $this->artisan('inventory:generate-product-qr-codes')
        ->assertFailed();
});

it('avisa quando public/storage não existe', function (): void {
    Storage::fake('public');

    $category = Category::factory()->create();
    Product::factory()->for($category)->create(['qr_code_path' => null]);

    $originalPublic = public_path();
    $tmpPublic      = sys_get_temp_dir() . '/pest-public-' . uniqid('', true);
    mkdir($tmpPublic, 0775, true);

    try {
        app()->usePublicPath($tmpPublic);

        $this->artisan('inventory:generate-product-qr-codes', ['--chunk' => 1])
            ->expectsOutputToContain('storage:link')
            ->assertSuccessful();
    } finally {
        app()->usePublicPath($originalPublic);
        rmdir($tmpPublic);
    }
});

it('avisa quando public/storage é arquivo e não symlink', function (): void {
    Storage::fake('public');

    $category = Category::factory()->create();
    Product::factory()->for($category)->create(['qr_code_path' => null]);

    $originalPublic = public_path();
    $tmpPublic      = sys_get_temp_dir() . '/pest-public-file-' . uniqid('', true);
    mkdir($tmpPublic, 0775, true);
    file_put_contents($tmpPublic . '/storage', 'not-a-symlink');

    try {
        app()->usePublicPath($tmpPublic);

        $this->artisan('inventory:generate-product-qr-codes', ['--chunk' => 1])
            ->expectsOutputToContain('public/storage')
            ->assertSuccessful();
    } finally {
        app()->usePublicPath($originalPublic);
        unlink($tmpPublic . '/storage');
        rmdir($tmpPublic);
    }
});
