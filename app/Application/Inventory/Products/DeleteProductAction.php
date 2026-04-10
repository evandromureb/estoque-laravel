<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Products;

use App\Models\{Product, ProductImage};
use Illuminate\Support\Facades\Storage;

final class DeleteProductAction
{
    public function execute(Product $product): void
    {
        $product->load('images');

        if ($product->qr_code_path) {
            Storage::disk('public')->delete($product->qr_code_path);
        }

        foreach ($product->images as $image) {
            if (!ProductImage::isBundledPublicAsset($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        }

        $product->delete();
    }
}
