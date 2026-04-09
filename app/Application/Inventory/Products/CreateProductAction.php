<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Products;

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\Product;
use Illuminate\Http\UploadedFile;

final class CreateProductAction
{
    public function __construct(
        private readonly ProductQrCodeGenerator $qrCodeGenerator,
    ) {
    }

    /**
     * @param array<string, mixed> $attributes
     * @param list<UploadedFile>   $imageFiles
     */
    public function execute(array $attributes, array $imageFiles): Product
    {
        $product = Product::create($attributes);

        $this->qrCodeGenerator->generateAndStore($product);

        foreach ($imageFiles as $file) {
            $path = $file->store('product_images', 'public');
            $product->images()->create([
                'path'       => $path,
                'is_primary' => false,
            ]);
        }

        return $product;
    }
}
