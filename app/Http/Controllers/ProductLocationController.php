<?php

namespace App\Http\Controllers;

use App\Application\Inventory\ProductLocations\{AllocateOrMergeProductLocationAction, UpdateProductLocationAction};
use App\Http\Requests\{StoreProductLocationRequest, UpdateProductLocationRequest};
use App\Models\ProductLocation;
use Illuminate\Http\{RedirectResponse, Request};

class ProductLocationController extends Controller
{
    public function __construct(
        private readonly AllocateOrMergeProductLocationAction $allocateOrMergeProductLocationAction,
        private readonly UpdateProductLocationAction $updateProductLocationAction,
    ) {
    }

    public function store(StoreProductLocationRequest $request): RedirectResponse
    {
        $this->allocateOrMergeProductLocationAction->execute($request->validated());

        return $this->redirectAfterCrud($request, 'products.index', [], [
            'success' => 'Produto alocado com sucesso.',
        ]);
    }

    public function update(UpdateProductLocationRequest $request, ProductLocation $productLocation): RedirectResponse
    {
        $this->updateProductLocationAction->execute($productLocation, $request->validated());

        return $this->redirectAfterCrud($request, 'products.index', [], [
            'success' => 'Localização de estoque atualizada com sucesso.',
        ]);
    }

    public function destroy(Request $request, ProductLocation $productLocation): RedirectResponse
    {
        $this->authorize('delete', $productLocation);

        $productLocation->delete();

        return $this->redirectAfterCrud($request, 'products.index', [], [
            'success' => 'Localização removida com sucesso.',
        ]);
    }
}
