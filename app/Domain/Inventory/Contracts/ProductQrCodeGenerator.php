<?php

declare(strict_types = 1);

namespace App\Domain\Inventory\Contracts;

use App\Models\Product;

interface ProductQrCodeGenerator
{
    /**
     * Gera um QR code (arquivo SVG no disco public) e persiste o caminho em {@see Product::$qr_code_path}.
     */
    public function generateAndStore(Product $product): void;
}
