<?php

declare(strict_types = 1);

namespace App\Application\Inventory\ProductImages;

use App\Models\Product;
use Illuminate\Http\UploadedFile;

final class AddProductImagesAction
{
    /**
     * @param list<UploadedFile> $files
     */
    public function execute(Product $product, array $files): void
    {
        foreach ($files as $file) {
            $path = $file->store('product_images', 'public');
            $product->images()->create([
                'path'       => $path,
                'is_primary' => false,
            ]);
        }
    }
}
