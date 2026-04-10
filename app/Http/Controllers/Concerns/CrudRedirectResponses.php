<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\{RedirectResponse, Request};

trait CrudRedirectResponses
{
    /**
     * Redireciona para a URL de retorno segura (_return_url ou Referer) ou para a rota padrão.
     *
     * @param array<string, mixed> $routeParameters
     * @param array<string, mixed> $with
     */
    protected function redirectAfterCrud(Request $request, string $fallbackRoute, array $routeParameters = [], array $with = []): RedirectResponse
    {
        $url      = $this->resolveCrudReturnUrl($request);
        $redirect = $url !== null ? redirect()->to($url) : redirect()->route($fallbackRoute, $routeParameters);

        return $with === [] ? $redirect : $redirect->with($with);
    }

    protected function resolveCrudReturnUrl(Request $request): ?string
    {
        $candidates = [
            $request->input('_return_url'),
            $request->headers->get('Referer'),
        ];

        foreach ($candidates as $candidate) {
            if ($this->isAllowedReturnUrl($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    protected function isAllowedReturnUrl(mixed $url): bool
    {
        if (!is_string($url) || $url === '') {
            return false;
        }

        $trimmed = trim($url);

        if (preg_match('#^\s*https?://#i', $trimmed) === 1) {
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $urlHost = parse_url($trimmed, PHP_URL_HOST);

            return $appHost !== null && $urlHost === $appHost;
        }

        return str_starts_with($trimmed, '/') && !str_starts_with($trimmed, '//');
    }

    /**
     * Quando a página solicitada não tem itens (ex.: último item removido ou page > última página), volta uma página.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param LengthAwarePaginator<TKey, TValue> $paginator
     */
    protected function redirectIfPaginatorPageEmpty(LengthAwarePaginator $paginator): ?RedirectResponse
    {
        if ($paginator->currentPage() <= 1) {
            return null;
        }

        $beyondLastPage = $paginator->currentPage() > $paginator->lastPage();

        if ($beyondLastPage || $paginator->isEmpty()) {
            return redirect()->to($paginator->url(max(1, $paginator->currentPage() - 1)));
        }

        return null;
    }
}
