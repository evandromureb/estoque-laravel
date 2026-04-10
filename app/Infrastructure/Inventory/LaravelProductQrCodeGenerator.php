<?php

declare(strict_types = 1);

namespace App\Infrastructure\Inventory;

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

final class LaravelProductQrCodeGenerator implements ProductQrCodeGenerator
{
    public function generateAndStore(Product $product): void
    {
        if ($product->qr_code_path) {
            Storage::disk('public')->delete($product->qr_code_path);
        }

        $disk = Storage::disk('public');
        // SVG não exige extensão Imagick (PNG do bacon/bacon-qr-code usa ImagickImageBackEnd).
        $fileName = 'qrcodes/product-' . $product->sku . '-' . Str::random(5) . '.svg';

        $disk->makeDirectory(dirname($fileName));

        $content = route('products.show', $product);

        QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($content, $disk->path($fileName));

        $product->update(['qr_code_path' => $fileName]);
    }
}
