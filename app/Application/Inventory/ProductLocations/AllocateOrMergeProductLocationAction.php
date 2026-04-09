<?php

declare(strict_types = 1);

namespace App\Application\Inventory\ProductLocations;

use App\Models\ProductLocation;

final class AllocateOrMergeProductLocationAction
{
    /**
     * @param array<string, mixed> $validated
     */
    public function execute(array $validated): void
    {
        $payload = [
            'product_id'   => (int) $validated['product_id'],
            'warehouse_id' => (int) $validated['warehouse_id'],
            'aisle'        => isset($validated['aisle']) ? (string) $validated['aisle'] : '',
            'shelf'        => isset($validated['shelf']) ? (string) $validated['shelf'] : '',
            'quantity'     => (int) $validated['quantity'],
        ];

        $existing = ProductLocation::query()
            ->where('product_id', $payload['product_id'])
            ->where('warehouse_id', $payload['warehouse_id'])
            ->where('aisle', $payload['aisle'])
            ->where('shelf', $payload['shelf'])
            ->first();

        if ($existing) {
            $existing->increment('quantity', $payload['quantity']);
        } else {
            ProductLocation::create($payload);
        }
    }
}
