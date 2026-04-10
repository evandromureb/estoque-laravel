<?php

declare(strict_types = 1);

it('serves a valid OpenAPI document for the REST API', function (): void {
    $response = $this->getJson('/docs/api.json');

    $response->assertSuccessful()
        ->assertJsonStructure(['openapi', 'info', 'paths']);

    $paths = $response->json('paths');

    $pathKeys = array_keys($paths ?? []);

    expect($pathKeys)->not->toBeEmpty();
    expect($pathKeys)->toContain('/v1/products');
    expect($pathKeys)->toContain('/v1/categories');
    expect($pathKeys)->toContain('/v1/products/{product}/images');

    $schemes = $response->json('components.securitySchemes');

    expect($schemes)->toHaveKey('laravel_session');
    expect($schemes)->toHaveKey('sanctum_token');
});
