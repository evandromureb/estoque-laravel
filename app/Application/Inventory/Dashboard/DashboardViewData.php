<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Dashboard;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read model for the authenticated dashboard.
 */
final readonly class DashboardViewData
{
    /**
     * @param Collection<int, Product> $belowMinimumStockProducts
     * @param Collection<int, Product> $recentProducts
     * @param Collection<int, Product> $recentProductNotes
     */
    public function __construct(
        public int $totalProducts,
        public int $totalWarehouses,
        public int $totalUsers,
        public int $lowStockProductsCount,
        public Collection $belowMinimumStockProducts,
        public Collection $recentProducts,
        public Collection $recentProductNotes,
    ) {
    }
}
