<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Products;

use App\Models\{Category, Product, Warehouse};

final class ListProductsForIndexQuery
{
    public function execute(?string $search, int $perPage = 10): ProductsIndexViewData
    {
        $query = Product::query()
            ->with(['category', 'images', 'locations.warehouse'])
            ->withSum('locations', 'quantity')
            ->withCount(['locations', 'images']);

        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate($perPage)->withQueryString();

        $categories = Category::query()->orderBy('name')->get();
        $warehouses = Warehouse::query()->orderBy('name')->get();

        return new ProductsIndexViewData($products, $categories, $warehouses);
    }
}
