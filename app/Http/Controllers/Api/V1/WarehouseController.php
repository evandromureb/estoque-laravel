<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\{StoreWarehouseRequest, UpdateWarehouseRequest};
use App\Http\Resources\Api\V1\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarehouseController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Warehouse::class);

        return WarehouseResource::collection(
            Warehouse::query()->orderBy('name')->paginate(15),
        );
    }

    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        $warehouse = Warehouse::query()->create($request->validated());

        return (new WarehouseResource($warehouse))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Warehouse $warehouse): WarehouseResource
    {
        $this->authorize('view', $warehouse);

        return new WarehouseResource($warehouse);
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): WarehouseResource
    {
        $warehouse->update($request->validated());

        return new WarehouseResource($warehouse->fresh());
    }

    public function destroy(Warehouse $warehouse): Response
    {
        $this->authorize('delete', $warehouse);
        $warehouse->delete();

        return response()->noContent();
    }
}
