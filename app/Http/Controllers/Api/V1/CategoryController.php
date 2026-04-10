<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreCategoryRequest, UpdateCategoryRequest};
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Category::class);

        return CategoryResource::collection(
            Category::query()->orderBy('name')->paginate(15),
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::query()->create($request->validated());

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Category $category): CategoryResource
    {
        $this->authorize('view', $category);

        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        return new CategoryResource($category->fresh());
    }

    public function destroy(Category $category): Response
    {
        $this->authorize('delete', $category);
        $category->delete();

        return response()->noContent();
    }
}
