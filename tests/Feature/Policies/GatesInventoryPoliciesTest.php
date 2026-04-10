<?php

declare(strict_types = 1);

use App\Models\{Category, ProductLocation, User, Warehouse};
use Illuminate\Support\Facades\Gate;

beforeEach(function (): void {
    $this->admin  = User::factory()->admin()->create();
    $this->member = User::factory()->create();
});

it('allows only admins to view a category instance', function (): void {
    $category = Category::factory()->create();

    expect(Gate::forUser($this->admin)->allows('view', $category))->toBeTrue()
        ->and(Gate::forUser($this->member)->allows('view', $category))->toBeFalse();
});

it('allows only admins to update a category', function (): void {
    $category = Category::factory()->create();

    expect(Gate::forUser($this->admin)->allows('update', $category))->toBeTrue()
        ->and(Gate::forUser($this->member)->allows('update', $category))->toBeFalse();
});

it('allows only admins to view a warehouse instance', function (): void {
    $warehouse = Warehouse::factory()->create();

    expect(Gate::forUser($this->admin)->allows('view', $warehouse))->toBeTrue()
        ->and(Gate::forUser($this->member)->allows('view', $warehouse))->toBeFalse();
});

it('allows only admins to update a warehouse', function (): void {
    $warehouse = Warehouse::factory()->create();

    expect(Gate::forUser($this->admin)->allows('update', $warehouse))->toBeTrue()
        ->and(Gate::forUser($this->member)->allows('update', $warehouse))->toBeFalse();
});

it('allows only admins to list and view a product location', function (): void {
    $location = ProductLocation::factory()->create();

    expect(Gate::forUser($this->admin)->allows('viewAny', ProductLocation::class))->toBeTrue()
        ->and(Gate::forUser($this->member)->allows('viewAny', ProductLocation::class))->toBeFalse()
        ->and(Gate::forUser($this->admin)->allows('view', $location))->toBeTrue()
        ->and(Gate::forUser($this->member)->allows('view', $location))->toBeFalse();
});

it('allows only admins to view another user', function (): void {
    $other = User::factory()->create();

    expect(Gate::forUser($this->admin)->allows('view', $other))->toBeTrue()
        ->and(Gate::forUser($this->member)->allows('view', $other))->toBeFalse();
});

it('prevents admin from deleting their own account via policy', function (): void {
    expect(Gate::forUser($this->admin)->allows('delete', $this->admin))->toBeFalse();
});

it('allows admin to delete another user', function (): void {
    $other = User::factory()->create();

    expect(Gate::forUser($this->admin)->allows('delete', $other))->toBeTrue();
});

it('forbids non-admin from deleting any user', function (): void {
    $other = User::factory()->create();

    expect(Gate::forUser($this->member)->allows('delete', $other))->toBeFalse();
});

it('allows only admins to create an api token for a user', function (): void {
    $other = User::factory()->create();

    expect(Gate::forUser($this->admin)->allows('createApiToken', $other))->toBeTrue()
        ->and(Gate::forUser($this->member)->allows('createApiToken', $other))->toBeFalse();
});

it('allows only admins to revoke an api token for a user', function (): void {
    $other = User::factory()->create();

    expect(Gate::forUser($this->admin)->allows('revokeApiToken', $other))->toBeTrue()
        ->and(Gate::forUser($this->member)->allows('revokeApiToken', $other))->toBeFalse();
});

it('allows only admins to view api docs outside local and testing', function (): void {
    $originalEnv = app('env');

    try {
        app()->instance('env', 'production');

        expect(Gate::forUser($this->admin)->allows('viewApiDocs'))->toBeTrue()
            ->and(Gate::forUser($this->member)->allows('viewApiDocs'))->toBeFalse()
            ->and(Gate::forUser(null)->allows('viewApiDocs'))->toBeFalse();
    } finally {
        app()->instance('env', $originalEnv);
    }
});
