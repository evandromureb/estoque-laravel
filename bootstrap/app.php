<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};
use Illuminate\Http\{JsonResponse, Request};
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, \Throwable $e): bool => $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->respond(function (SymfonyResponse $response, \Throwable $e, Request $request): SymfonyResponse {
            if (!$request->is('api/*')) {
                return $response;
            }

            if ($response instanceof JsonResponse) {
                $payload = $response->getData(true);

                if (!is_array($payload)) {
                    return response()->json(
                        ['message' => __('Erro na requisição.')],
                        $response->getStatusCode(),
                        [],
                        JSON_UNESCAPED_UNICODE
                    );
                }

                $clean = [
                    'message' => (string) ($payload['message'] ?? __('Erro na requisição.')),
                ];

                if (isset($payload['errors']) && is_array($payload['errors'])) {
                    $clean['errors'] = $payload['errors'];
                }

                return response()->json(
                    $clean,
                    $response->getStatusCode(),
                    [],
                    JSON_UNESCAPED_UNICODE
                );
            }

            $status = $response->getStatusCode();
            $text   = SymfonyResponse::$statusTexts[$status] ?? __('Erro na requisição.');

            return response()->json(
                ['message' => $status >= 500 ? __('Erro interno do servidor.') : $text],
                $status,
                [],
                JSON_UNESCAPED_UNICODE
            );
        });
    })->create();
