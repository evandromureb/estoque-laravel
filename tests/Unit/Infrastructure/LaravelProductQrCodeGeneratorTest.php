<?php

declare(strict_types = 1);

use App\Infrastructure\Inventory\LaravelProductQrCodeGenerator;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use SimpleSoftwareIO\QrCode\Generator;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * @return Generator&\Mockery\MockInterface
 */
function createQrGeneratorMock(): Generator
{
    $generator = \Mockery::mock(Generator::class);
    $generator->shouldReceive('format')->with('svg')->andReturnSelf();
    $generator->shouldReceive('size')->with(300)->andReturnSelf();
    $generator->shouldReceive('margin')->with(2)->andReturnSelf();
    $generator->shouldReceive('generate')
        ->once()
        ->with(\Mockery::type('string'), \Mockery::type('string'))
        ->andReturnUsing(function (string $content, string $fullPath): void {
            $dir = dirname($fullPath);

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($fullPath, '<svg></svg>');
        });

    return $generator;
}

afterEach(function (): void {
    QrCode::clearResolvedInstances();
});

it('generates a qr file and stores the path on the product', function (): void {
    Storage::fake('public');
    QrCode::swap(createQrGeneratorMock());

    $product = Product::factory()->create([
        'sku'          => 'SKU-QR-1',
        'qr_code_path' => null,
    ]);

    app(LaravelProductQrCodeGenerator::class)->generateAndStore($product);

    $product->refresh();
    expect($product->qr_code_path)->toBeString()->toContain('qrcodes/product-SKU-QR-1');
    Storage::disk('public')->assertExists($product->qr_code_path);
});

it('removes the previous qr file before generating a new one', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('qrcodes/old.png', 'old');

    QrCode::swap(createQrGeneratorMock());

    $product = Product::factory()->create([
        'sku'          => 'SKU-QR-2',
        'qr_code_path' => 'qrcodes/old.png',
    ]);

    app(LaravelProductQrCodeGenerator::class)->generateAndStore($product);

    Storage::disk('public')->assertMissing('qrcodes/old.png');
    expect($product->fresh()->qr_code_path)->not->toBe('qrcodes/old.png');
});
