<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class CreateUserAction
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(array $attributes): User
    {
        $attributes['password'] = Hash::make((string) $attributes['password']);

        return User::create($attributes);
    }
}
