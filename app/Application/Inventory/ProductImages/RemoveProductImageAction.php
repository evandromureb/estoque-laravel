<?php

declare(strict_types = 1);

namespace App\Application\Inventory\ProductImages;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

final class RemoveProductImageAction
{
    public function execute(ProductImage $productImage): void
    {
        if (!ProductImage::isBundledPublicAsset($productImage->path)) {
            Storage::disk('public')->delete($productImage->path);
        }

        $productImage->delete();
    }
}
