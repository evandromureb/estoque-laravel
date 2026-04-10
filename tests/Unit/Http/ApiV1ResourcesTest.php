<?php

declare(strict_types = 1);

use App\Http\Resources\Api\V1\{CategoryResource, ProductImageResource, ProductLocationResource, ProductResource, UserResource, WarehouseResource};
use App\Models\{Category, Product, ProductImage, ProductLocation, User, Warehouse};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->request = Request::create('/');
});

it('resolves category resource to array', function (): void {
    $category = Category::factory()->create();
    $data     = (new CategoryResource($category))->toArray($this->request);

    expect($data)->toBeArray()->toHaveKey('id');
});

it('resolves product resource to array', function (): void {
    $product = Product::factory()->create();
    $data    = (new ProductResource($product))->toArray($this->request);

    expect($data)->toBeArray()->toHaveKey('id');
});

it('resolves product image resource to array', function (): void {
    $product = Product::factory()->create();
    $image   = ProductImage::factory()->for($product)->create();
    $data    = (new ProductImageResource($image))->toArray($this->request);

    expect($data)->toBeArray()->toHaveKey('id')->toHaveKey('url');
});

it('resolves product location resource to array', function (): void {
    $location = ProductLocation::factory()->create();
    $data     = (new ProductLocationResource($location))->toArray($this->request);

    expect($data)->toBeArray()->toHaveKey('id');
});

it('resolves user resource to array', function (): void {
    $user = User::factory()->create();
    $data = (new UserResource($user))->toArray($this->request);

    expect($data)->toBeArray()->toHaveKey('id');
});

it('resolves warehouse resource to array', function (): void {
    $warehouse = Warehouse::factory()->create();
    $data      = (new WarehouseResource($warehouse))->toArray($this->request);

    expect($data)->toBeArray()->toHaveKey('id');
});
