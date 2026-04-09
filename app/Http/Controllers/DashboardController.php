<?php

namespace App\Http\Controllers;

use App\Application\Inventory\Dashboard\GetDashboardDataQuery;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GetDashboardDataQuery $getDashboardDataQuery,
    ) {
    }

    public function __invoke(): View
    {
        $data = $this->getDashboardDataQuery->execute();

        return view('dashboard', [
            'totalProducts'             => $data->totalProducts,
            'totalWarehouses'           => $data->totalWarehouses,
            'totalUsers'                => $data->totalUsers,
            'lowStockProductsCount'     => $data->lowStockProductsCount,
            'belowMinimumStockProducts' => $data->belowMinimumStockProducts,
            'recentProducts'            => $data->recentProducts,
            'recentProductNotes'        => $data->recentProductNotes,
        ]);
    }
}
