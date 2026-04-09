<?php

declare(strict_types = 1);

namespace App\Application\Inventory\ProductLocations;

use App\Models\ProductLocation;

final class UpdateProductLocationAction
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(ProductLocation $productLocation, array $attributes): void
    {
        $productLocation->update($attributes);
    }
}
