<?php

namespace App\Http\Controllers;

use App\Application\Inventory\Categories\{CreateCategoryAction, DeleteCategoryAction, ListCategoriesForIndexQuery, UpdateCategoryAction};
use App\Domain\Inventory\Exceptions\CategoryHasProductsException;
use App\Http\Requests\{StoreCategoryRequest, UpdateCategoryRequest};
use App\Models\Category;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ListCategoriesForIndexQuery $listCategoriesForIndexQuery,
        private readonly CreateCategoryAction $createCategoryAction,
        private readonly UpdateCategoryAction $updateCategoryAction,
        private readonly DeleteCategoryAction $deleteCategoryAction,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', Category::class);

        $search     = $request->filled('search') ? (string) $request->search : null;
        $categories = $this->listCategoriesForIndexQuery->execute($search, 3);

        if (($redirect = $this->redirectIfPaginatorPageEmpty($categories)) instanceof \Illuminate\Http\RedirectResponse) {
            return $redirect;
        }

        return view('categories.index', [
            'categories' => $categories,
            'crudRoutes' => [
                'storeUrl' => route('categories.store'),
                'baseUrl'  => url('/categories'),
            ],
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->createCategoryAction->execute($request->validated());

        return $this->redirectAfterCrud($request, 'categories.index', [], [
            'success' => 'Categoria criada com sucesso.',
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->updateCategoryAction->execute($category, $request->validated());

        return $this->redirectAfterCrud($request, 'categories.index', [], [
            'success' => 'Categoria atualizada com sucesso.',
        ]);
    }

    public function destroy(Request $request, Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        try {
            $this->deleteCategoryAction->execute($category);
        } catch (CategoryHasProductsException $e) {
            return $this->redirectAfterCrud($request, 'categories.index', [], [])->withErrors([
                'error' => $e->getMessage(),
            ]);
        }

        return $this->redirectAfterCrud($request, 'categories.index', [], [
            'success' => 'Categoria removida com sucesso.',
        ]);
    }
}
