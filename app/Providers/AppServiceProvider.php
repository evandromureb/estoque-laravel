<?php

declare(strict_types = 1);

namespace App\Providers;

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Infrastructure\Inventory\LaravelProductQrCodeGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductQrCodeGenerator::class, LaravelProductQrCodeGenerator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
