<?php

declare(strict_types = 1);

namespace App\Domain\Inventory\Contracts;

use App\Models\Product;

interface ProductQrCodeGenerator
{
    /**
     * Generate a QR code for the product and persist its path on the model.
     */
    public function generateAndStore(Product $product): void;
}
