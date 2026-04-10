<?php

namespace App\Http\Controllers;

use App\Application\Inventory\Users\{CreateUserAction, ListUsersForIndexQuery, UpdateUserAction};
use App\Http\Requests\{DestroyUserApiTokenRequest, StoreUserApiTokenRequest, StoreUserRequest, UpdateUserRequest};
use App\Models\User;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly ListUsersForIndexQuery $listUsersForIndexQuery,
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction,
    ) {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $search = $request->filled('search') ? (string) $request->search : null;
        $users  = $this->listUsersForIndexQuery->execute($search, 10);

        if (($redirect = $this->redirectIfPaginatorPageEmpty($users)) instanceof \Illuminate\Http\RedirectResponse) {
            return $redirect;
        }

        return view('users.index', [
            'users'      => $users,
            'crudRoutes' => [
                'storeUrl' => route('users.store'),
                'baseUrl'  => url('/users'),
            ],
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->createUserAction->execute($request->validated());

        return $this->redirectAfterCrud($request, 'users.index', [], [
            'success' => 'Usuário criado com sucesso.',
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->updateUserAction->execute($user, $request->validated());

        return $this->redirectAfterCrud($request, 'users.index', [], [
            'success' => 'Usuário atualizado com sucesso.',
        ]);
    }

    public function storeApiToken(StoreUserApiTokenRequest $request, User $user): RedirectResponse
    {
        $tokenName      = (string) $request->validated()['token_name'];
        $plainTextToken = $user->createToken($tokenName)->plainTextToken;

        return $this->redirectAfterCrud($request, 'users.index', [], [
            'success'         => 'Token de API criado. Copie-o agora; ele não será exibido novamente.',
            'api_token_plain' => $plainTextToken,
        ]);
    }

    public function destroyApiToken(DestroyUserApiTokenRequest $request, User $user): RedirectResponse
    {
        $deleted = $user->tokens()->where('name', User::DEFAULT_API_TOKEN_NAME)->delete();

        $message = $deleted > 0
            ? 'Token de API revogado com sucesso.'
            : 'Não havia token padrão para revogar.';

        return $this->redirectAfterCrud($request, 'users.index', [], [
            'success' => $message,
        ]);
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return $this->redirectAfterCrud($request, 'users.index', [], [
            'success' => 'Usuário removido com sucesso.',
        ]);
    }
}
