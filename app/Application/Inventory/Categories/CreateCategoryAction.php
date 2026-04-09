<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Categories;

use App\Models\Category;

final class CreateCategoryAction
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(array $attributes): Category
    {
        return Category::create($attributes);
    }
}
