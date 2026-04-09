<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Inventory\ProductLocations\{AllocateOrMergeProductLocationAction, UpdateProductLocationAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreProductLocationRequest, UpdateProductLocationRequest};
use App\Http\Resources\Api\V1\ProductLocationResource;
use App\Models\ProductLocation;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductLocationController extends Controller
{
    public function __construct(
        private readonly AllocateOrMergeProductLocationAction $allocateOrMergeProductLocationAction,
        private readonly UpdateProductLocationAction $updateProductLocationAction,
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ProductLocation::class);

        return ProductLocationResource::collection(
            ProductLocation::query()
                ->with(['warehouse', 'product'])
                ->orderByDesc('id')
                ->paginate(15),
        );
    }

    public function store(StoreProductLocationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->allocateOrMergeProductLocationAction->execute($validated);

        $location = ProductLocation::query()
            ->where('product_id', $validated['product_id'])
            ->where('warehouse_id', $validated['warehouse_id'])
            ->where('aisle', $validated['aisle'] ?? '')
            ->where('shelf', $validated['shelf'] ?? '')
            ->with(['warehouse', 'product'])
            ->firstOrFail();

        return (new ProductLocationResource($location))
            ->response()
            ->setStatusCode(201);
    }

    public function show(ProductLocation $productLocation): ProductLocationResource
    {
        $this->authorize('view', $productLocation);

        $productLocation->load(['warehouse', 'product']);

        return new ProductLocationResource($productLocation);
    }

    public function update(UpdateProductLocationRequest $request, ProductLocation $productLocation): ProductLocationResource
    {
        $this->updateProductLocationAction->execute($productLocation, $request->validated());

        $productLocation->refresh()->load(['warehouse', 'product']);

        return new ProductLocationResource($productLocation);
    }

    public function destroy(ProductLocation $productLocation): Response
    {
        $this->authorize('delete', $productLocation);
        $productLocation->delete();

        return response()->noContent();
    }
}
