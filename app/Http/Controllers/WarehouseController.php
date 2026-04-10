<?php

namespace App\Http\Controllers;

use App\Application\Inventory\Warehouses\{CreateWarehouseAction, DeleteWarehouseAction, ListWarehousesForIndexQuery, UpdateWarehouseAction};
use App\Domain\Inventory\Exceptions\WarehouseHasStockLocationsException;
use App\Http\Requests\{StoreWarehouseRequest, UpdateWarehouseRequest};
use App\Models\Warehouse;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function __construct(
        private readonly ListWarehousesForIndexQuery $listWarehousesForIndexQuery,
        private readonly CreateWarehouseAction $createWarehouseAction,
        private readonly UpdateWarehouseAction $updateWarehouseAction,
        private readonly DeleteWarehouseAction $deleteWarehouseAction,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', Warehouse::class);

        $search     = $request->filled('search') ? (string) $request->search : null;
        $warehouses = $this->listWarehousesForIndexQuery->execute($search, 10);

        if (($redirect = $this->redirectIfPaginatorPageEmpty($warehouses)) instanceof \Illuminate\Http\RedirectResponse) {
            return $redirect;
        }

        return view('warehouses.index', [
            'warehouses' => $warehouses,
            'crudRoutes' => [
                'storeUrl' => route('warehouses.store'),
                'baseUrl'  => url('/warehouses'),
            ],
        ]);
    }

    public function store(StoreWarehouseRequest $request): RedirectResponse
    {
        $this->createWarehouseAction->execute($request->validated());

        return $this->redirectAfterCrud($request, 'warehouses.index', [], [
            'success' => 'Armazém criado com sucesso.',
        ]);
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): RedirectResponse
    {
        $this->updateWarehouseAction->execute($warehouse, $request->validated());

        return $this->redirectAfterCrud($request, 'warehouses.index', [], [
            'success' => 'Armazém atualizado com sucesso.',
        ]);
    }

    public function destroy(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $this->authorize('delete', $warehouse);

        try {
            $this->deleteWarehouseAction->execute($warehouse);
        } catch (WarehouseHasStockLocationsException $e) {
            return $this->redirectAfterCrud($request, 'warehouses.index', [], [])->withErrors([
                'error' => $e->getMessage(),
            ]);
        }

        return $this->redirectAfterCrud($request, 'warehouses.index', [], [
            'success' => 'Armazém removido com sucesso.',
        ]);
    }
}
