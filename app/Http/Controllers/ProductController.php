<?php

namespace App\Http\Controllers;

use App\Application\Inventory\Products\{CreateProductAction, DeleteProductAction, ListProductsForIndexQuery, UpdateProductAction};
use App\Http\Requests\{StoreProductRequest, UpdateProductRequest};
use App\Models\{Product, Warehouse};
use App\Support\Http\RequestUploadedFileList;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ListProductsForIndexQuery $listProductsForIndexQuery,
        private readonly CreateProductAction $createProductAction,
        private readonly UpdateProductAction $updateProductAction,
        private readonly DeleteProductAction $deleteProductAction,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', Product::class);

        $search = $request->filled('search') ? (string) $request->search : null;
        $data   = $this->listProductsForIndexQuery->execute($search, 10);

        if (($redirect = $this->redirectIfPaginatorPageEmpty($data->products)) instanceof \Illuminate\Http\RedirectResponse) {
            return $redirect;
        }

        return view('products.index', [
            'products'   => $data->products,
            'categories' => $data->categories,
            'warehouses' => $data->warehouses,
            'crudRoutes' => [
                'storeUrl'                 => route('products.store'),
                'baseUrl'                  => url('/products'),
                'productLocationsStoreUrl' => route('product-locations.store'),
            ],
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['images']);

        $imageFiles = RequestUploadedFileList::asList($request, 'images');

        $this->createProductAction->execute($validated, $imageFiles);

        return $this->redirectAfterCrud($request, 'products.index', [], [
            'success' => 'Produto criado com sucesso.',
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['images']);

        $imageFiles = RequestUploadedFileList::asList($request, 'images');

        $this->updateProductAction->execute($product, $validated, $imageFiles);

        return $this->redirectAfterCrud($request, 'products.index', [], [
            'success' => 'Produto atualizado com sucesso.',
        ]);
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->deleteProductAction->execute($product);

        return $this->redirectAfterCrud($request, 'products.index', [], [
            'success' => 'Produto removido com sucesso.',
        ]);
    }

    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        $product->load(['images', 'category', 'locations.warehouse']);

        $warehouses = Warehouse::query()->orderBy('name')->get();

        return view('products.show', ['product' => $product, 'warehouses' => $warehouses]);
    }
}
