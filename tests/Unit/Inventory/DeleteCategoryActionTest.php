<?php

declare(strict_types = 1);

use App\Application\Inventory\Categories\DeleteCategoryAction;
use App\Domain\Inventory\Exceptions\CategoryHasProductsException;
use App\Models\{Category, Product};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('deletes a category without products', function (): void {
    $category = Category::factory()->create();
    $action   = app(DeleteCategoryAction::class);

    $action->execute($category);

    expect(Category::query()->find($category->id))->toBeNull();
});

it('throws when category has products', function (): void {
    $category = Category::factory()->create();
    Product::factory()->create(['category_id' => $category->id]);
    $action = app(DeleteCategoryAction::class);

    $action->execute($category);
})->throws(CategoryHasProductsException::class);
