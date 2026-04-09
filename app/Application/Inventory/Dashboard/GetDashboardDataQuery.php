<?php

declare(strict_types = 1);

namespace App\Application\Inventory\Dashboard;

use App\Models\{Product, User, Warehouse};

final class GetDashboardDataQuery
{
    public function execute(): DashboardViewData
    {
        $totalProducts   = Product::query()->count();
        $totalWarehouses = Warehouse::query()->count();
        $totalUsers      = User::query()->count();

        $lowStockProductsCount = Product::query()
            ->whereTotalStockBelowMinimum()
            ->count();

        $belowMinimumStockProducts = Product::query()
            ->whereTotalStockBelowMinimum()
            ->with(['category', 'images'])
            ->withSum('locations', 'quantity')
            ->orderBy('name')
            ->limit(15)
            ->get();

        $recentProducts = Product::query()
            ->with('images')
            ->latest()
            ->take(5)
            ->get();

        $recentProductNotes = Product::query()
            ->whereNotNull('additional_info')
            ->latest()
            ->take(3)
            ->get();

        return new DashboardViewData(
            totalProducts: $totalProducts,
            totalWarehouses: $totalWarehouses,
            totalUsers: $totalUsers,
            lowStockProductsCount: $lowStockProductsCount,
            belowMinimumStockProducts: $belowMinimumStockProducts,
            recentProducts: $recentProducts,
            recentProductNotes: $recentProductNotes,
        );
    }
}
