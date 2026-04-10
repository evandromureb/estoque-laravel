<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, User $model): bool
    {
        if (!$user->is_admin) {
            return false;
        }

        return $user->id !== $model->id;
    }

    /**
     * Apenas administradores podem gerar tokens de API para outros usuários.
     */
    public function createApiToken(User $user, User $model): bool
    {
        return $user->is_admin;
    }

    /**
     * Apenas administradores podem revogar o token de API padrão de outros usuários.
     */
    public function revokeApiToken(User $user, User $model): bool
    {
        return $user->is_admin;
    }
}
