<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Inventory\Products\{CreateProductAction, DeleteProductAction, ListProductsForIndexQuery, UpdateProductAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use App\Support\Http\RequestUploadedFileList;
use Illuminate\Http\{JsonResponse, Request, Response};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly ListProductsForIndexQuery $listProductsForIndexQuery,
        private readonly CreateProductAction $createProductAction,
        private readonly UpdateProductAction $updateProductAction,
        private readonly DeleteProductAction $deleteProductAction,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Product::class);

        $search = $request->filled('search') ? (string) $request->query('search') : null;
        $data   = $this->listProductsForIndexQuery->execute($search, 15);

        return ProductResource::collection($data->products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        unset($validated['images']);

        $imageFiles = RequestUploadedFileList::asList($request, 'images');

        $product = $this->createProductAction->execute($validated, $imageFiles);
        $product->load(['category', 'images', 'locations.warehouse']);
        $product->loadSum('locations', 'quantity');

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Product $product): ProductResource
    {
        $this->authorize('view', $product);

        $product->load(['category', 'images', 'locations.warehouse']);
        $product->loadSum('locations', 'quantity');

        return new ProductResource($product);
    }

    public function update(UpdateProductRequest $request, Product $product): ProductResource
    {
        $validated = $request->validated();
        unset($validated['images']);

        $imageFiles = RequestUploadedFileList::asList($request, 'images');

        $this->updateProductAction->execute($product, $validated, $imageFiles);

        $product->refresh();
        $product->load(['category', 'images', 'locations.warehouse']);
        $product->loadSum('locations', 'quantity');

        return new ProductResource($product);
    }

    public function destroy(Product $product): Response
    {
        $this->authorize('delete', $product);

        $this->deleteProductAction->execute($product);

        return response()->noContent();
    }
}
