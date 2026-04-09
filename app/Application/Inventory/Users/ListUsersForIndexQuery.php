<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListUsersForIndexQuery
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function execute(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query();

        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
