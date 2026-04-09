<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Warehouses;

use App\Models\Warehouse;

final class CreateWarehouseAction
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(array $attributes): Warehouse
    {
        return Warehouse::create($attributes);
    }
}
