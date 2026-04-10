<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Categories;

use App\Models\Category;

final class UpdateCategoryAction
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(Category $category, array $attributes): void
    {
        $category->update($attributes);
    }
}
