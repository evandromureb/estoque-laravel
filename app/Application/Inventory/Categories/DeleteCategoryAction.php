<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Categories;

use App\Domain\Inventory\Exceptions\CategoryHasProductsException;
use App\Models\Category;

final class DeleteCategoryAction
{
    public function execute(Category $category): void
    {
        if ($category->products()->count() > 0) {
            throw CategoryHasProductsException::make();
        }

        $category->delete();
    }
}
