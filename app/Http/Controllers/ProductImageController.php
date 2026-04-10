<?php

namespace App\Http\Controllers;

use App\Application\Inventory\ProductImages\{AddProductImagesAction, RemoveProductImageAction};
use App\Http\Requests\StoreProductImagesRequest;
use App\Models\{Product, ProductImage};
use App\Support\Http\RequestUploadedFileList;
use Illuminate\Http\{RedirectResponse, Request};

class ProductImageController extends Controller
{
    public function __construct(
        private readonly AddProductImagesAction $addProductImagesAction,
        private readonly RemoveProductImageAction $removeProductImageAction,
    ) {
    }

    public function store(StoreProductImagesRequest $request, Product $product): RedirectResponse
    {
        $this->addProductImagesAction->execute($product, RequestUploadedFileList::asList($request, 'images'));

        return $this->redirectAfterCrud($request, 'products.index', [], [
            'success' => 'Imagens adicionadas com sucesso.',
        ]);
    }

    public function destroy(Request $request, Product $product, ProductImage $productImage): RedirectResponse
    {
        if ($productImage->product_id !== $product->id) {
            abort(404);
        }

        $this->authorize('update', $product);

        $this->removeProductImageAction->execute($productImage);

        return $this->redirectAfterCrud($request, 'products.index', [], [
            'success' => 'Imagem removida com sucesso.',
        ]);
    }
}
