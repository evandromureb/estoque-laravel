<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class UpdateUserAction
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(User $user, array $attributes): void
    {
        if (!empty($attributes['password'])) {
            $attributes['password'] = Hash::make((string) $attributes['password']);
        } else {
            unset($attributes['password']);
        }

        $user->update($attributes);
    }
}
