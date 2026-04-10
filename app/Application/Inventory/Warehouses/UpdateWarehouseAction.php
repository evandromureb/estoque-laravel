<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Warehouses;

use App\Models\Warehouse;

final class UpdateWarehouseAction
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(Warehouse $warehouse, array $attributes): void
    {
        $warehouse->update($attributes);
    }
}
