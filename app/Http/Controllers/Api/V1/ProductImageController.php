<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Inventory\ProductImages\{AddProductImagesAction, RemoveProductImageAction};
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductImagesRequest;
use App\Http\Resources\Api\V1\ProductImageResource;
use App\Models\{Product, ProductImage};
use App\Support\Http\RequestUploadedFileList;
use Illuminate\Http\{JsonResponse, Response};

class ProductImageController extends Controller
{
    public function __construct(
        private readonly AddProductImagesAction $addProductImagesAction,
        private readonly RemoveProductImageAction $removeProductImageAction,
    ) {
    }

    public function store(StoreProductImagesRequest $request, Product $product): JsonResponse
    {
        $this->addProductImagesAction->execute($product, RequestUploadedFileList::asList($request, 'images'));

        $product->load('images');

        return ProductImageResource::collection($product->images)
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Product $product, ProductImage $productImage): Response
    {
        if ($productImage->product_id !== $product->id) {
            abort(404);
        }

        $this->authorize('update', $product);

        $this->removeProductImageAction->execute($productImage);

        return response()->noContent();
    }
}
