<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Warehouses;

use App\Models\Warehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListWarehousesForIndexQuery
{
    /**
     * @return LengthAwarePaginator<int, Warehouse>
     */
    public function execute(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $query = Warehouse::query();

        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location_string', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
