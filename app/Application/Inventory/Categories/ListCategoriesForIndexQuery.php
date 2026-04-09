<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Categories;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListCategoriesForIndexQuery
{
    /**
     * @return LengthAwarePaginator<int, Category>
     */
    public function execute(?string $search, int $perPage = 3): LengthAwarePaginator
    {
        $query = Category::query();

        if ($search !== null && $search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
