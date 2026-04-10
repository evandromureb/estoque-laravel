<?php

declare(strict_types = 1);

namespace App\Providers;

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Infrastructure\Inventory\LaravelProductQrCodeGenerator;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductQrCodeGenerator::class, LaravelProductQrCodeGenerator::class);
    }

    public function boot(): void
    {
        Gate::define('viewApiDocs', function (?User $user = null): bool {
            if (app()->environment(['local', 'testing'])) {
                return true;
            }

            return $user instanceof \App\Models\User && $user->is_admin;
        });

        if (class_exists(\Dedoc\Scramble\Scramble::class)) {
            \Dedoc\Scramble\Scramble::afterOpenApiGenerated(function ($openApi): void {
                $sessionCookie = (string) config('session.cookie');

                $openApi->secure(
                    \Dedoc\Scramble\Support\Generator\SecurityScheme::apiKey('cookie', $sessionCookie)
                        ->as('laravel_session')
                        ->setDescription(
                            'Sessão web (cookie), para browser / SPA no mesmo host. Obtenha CSRF com GET /sanctum/csrf-cookie '
                            . 'e envie X-XSRF-TOKEN nas mutações.',
                        ),
                );

                $openApi->secure(
                    \Dedoc\Scramble\Support\Generator\SecurityScheme::http('bearer')
                        ->as('sanctum_token')
                        ->setDescription(
                            'Token pessoal Sanctum: Authorization: Bearer {token}. Crie com $user->createToken(\'nome\')->plainTextToken.',
                        ),
                );
            });
        }
    }
}
