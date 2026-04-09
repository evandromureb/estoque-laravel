<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Products;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read model for the products index screen.
 */
final readonly class ProductsIndexViewData
{
    /**
     * @param LengthAwarePaginator<int, \App\Models\Product> $products
     * @param Collection<int, \App\Models\Category>          $categories
     * @param Collection<int, \App\Models\Warehouse>         $warehouses
     */
    public function __construct(
        public LengthAwarePaginator $products,
        public Collection $categories,
        public Collection $warehouses,
    ) {
    }
}
