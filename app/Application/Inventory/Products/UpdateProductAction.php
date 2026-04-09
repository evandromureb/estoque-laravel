<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Products;

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\Product;
use Illuminate\Http\UploadedFile;

final class UpdateProductAction
{
    public function __construct(
        private readonly ProductQrCodeGenerator $qrCodeGenerator,
    ) {
    }

    /**
     * @param array<string, mixed> $attributes
     * @param list<UploadedFile>   $imageFiles
     */
    public function execute(Product $product, array $attributes, array $imageFiles): void
    {
        $oldSku = $product->sku;

        $product->update($attributes);

        if ($oldSku !== $product->sku || !$product->qr_code_path) {
            $this->qrCodeGenerator->generateAndStore($product);
        }

        foreach ($imageFiles as $file) {
            $path = $file->store('product_images', 'public');
            $product->images()->create([
                'path' => $path,
            ]);
        }
    }
}
