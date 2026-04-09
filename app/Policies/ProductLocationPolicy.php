<?php

namespace App\Policies;

use App\Models\{ProductLocation, User};

class ProductLocationPolicy
{
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user, ProductLocation $productLocation): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, ProductLocation $productLocation): bool
    {
        return $user->is_admin;
    }
}
