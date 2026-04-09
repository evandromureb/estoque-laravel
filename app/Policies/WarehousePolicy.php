<?php

namespace App\Policies;

use App\Models\{User, Warehouse};

class WarehousePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return $user->is_admin;
    }
}
