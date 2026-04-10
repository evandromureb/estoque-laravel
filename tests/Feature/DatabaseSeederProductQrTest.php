<?php

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\Product;
use Database\Seeders\DatabaseSeeder;

it('assigns a qr code to every product created by the database seeder', function (): void {
    $this->mock(ProductQrCodeGenerator::class, function ($mock): void {
        $mock->shouldReceive('generateAndStore')
            ->andReturnUsing(function (Product $product): void {
                $product->forceFill(['qr_code_path' => 'qrcodes/seed-' . $product->id . '.png'])->save();
            });
    });

    $this->seed(DatabaseSeeder::class);

    expect(Product::query()->whereNull('qr_code_path')->exists())->toBeFalse();
});
