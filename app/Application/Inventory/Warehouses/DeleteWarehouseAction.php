<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Warehouses;

use App\Domain\Inventory\Exceptions\WarehouseHasStockLocationsException;
use App\Models\Warehouse;

final class DeleteWarehouseAction
{
    public function execute(Warehouse $warehouse): void
    {
        if ($warehouse->productLocations()->count() > 0) {
            throw WarehouseHasStockLocationsException::make();
        }

        $warehouse->delete();
    }
}
