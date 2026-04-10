<?php

declare(strict_types = 1);

use App\Models\{Category, Product, User, Warehouse};
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
});

it('filters categories by search', function (): void {
    Category::factory()->create(['name' => 'FiltroAlpha']);
    Category::factory()->create(['name' => 'Outro']);

    $this->actingAs($this->admin)
        ->get(route('categories.index', ['search' => 'FiltroAlpha']))
        ->assertOk()
        ->assertSee('FiltroAlpha', false)
        ->assertDontSee('Outro', false);
});

it('updates a category', function (): void {
    $category = Category::factory()->create(['name' => 'Antigo']);

    $this->actingAs($this->admin)->put(route('categories.update', $category), [
        'name'            => 'Novo Nome Cat',
        'description'     => 'd',
        'additional_info' => null,
    ])->assertRedirect(route('categories.index'));

    expect($category->fresh()->name)->toBe('Novo Nome Cat');
});

it('redirects when category list page is beyond the last page', function (): void {
    Category::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->get(route('categories.index', ['page' => 2]))
        ->assertRedirect(route('categories.index', ['page' => 1]));
});

it('does not redirect when category list page is valid and non-empty', function (): void {
    Category::factory()->count(6)->create();

    $this->actingAs($this->admin)
        ->get(route('categories.index', ['page' => 2]))
        ->assertOk();
});

it('stores a category using a safe relative return url', function (): void {
    $this->actingAs($this->admin)->post(route('categories.store'), [
        'name'            => 'Com Retorno Relativo',
        'description'     => null,
        'additional_info' => null,
        '_return_url'     => '/categories',
    ])->assertRedirect('/categories');

    expect(Category::query()->where('name', 'Com Retorno Relativo')->exists())->toBeTrue();
});

it('filters warehouses by search on name or location', function (): void {
    Warehouse::factory()->create(['name' => 'CD Norte', 'location_string' => 'X']);
    Warehouse::factory()->create(['name' => 'Outro', 'location_string' => 'São Paulo Leste']);

    $this->actingAs($this->admin)
        ->get(route('warehouses.index', ['search' => 'Norte']))
        ->assertOk()
        ->assertSee('CD Norte', false);

    $this->actingAs($this->admin)
        ->get(route('warehouses.index', ['search' => 'Leste']))
        ->assertOk()
        ->assertSee('São Paulo Leste', false);
});

it('updates a warehouse', function (): void {
    $warehouse = Warehouse::factory()->create(['name' => 'W1']);

    $this->actingAs($this->admin)->put(route('warehouses.update', $warehouse), [
        'name'            => 'W2',
        'location_string' => 'Loc',
        'description'     => null,
        'additional_info' => null,
    ])->assertRedirect(route('warehouses.index'));

    expect($warehouse->fresh()->name)->toBe('W2');
});

it('redirects when warehouse list page is beyond the last page', function (): void {
    Warehouse::factory()->count(11)->create();

    $this->actingAs($this->admin)
        ->get(route('warehouses.index', ['page' => 99]))
        ->assertRedirect();
});

it('filters users by search', function (): void {
    User::factory()->create(['name' => 'Alice Especial', 'email' => 'alice@example.com']);
    User::factory()->create(['name' => 'Bob', 'email' => 'bob@example.com']);

    $this->actingAs($this->admin)
        ->get(route('users.index', ['search' => 'Especial']))
        ->assertOk()
        ->assertSee('Alice Especial', false);
});

it('updates a user without changing password', function (): void {
    $target = User::factory()->create(['name' => 'Antes', 'email' => 'antes@example.com']);

    $this->actingAs($this->admin)->put(route('users.update', $target), [
        'name'            => 'Depois',
        'email'           => 'depois@example.com',
        'additional_info' => null,
    ])->assertRedirect(route('users.index'));

    $target->refresh();
    expect($target->name)->toBe('Depois')
        ->and($target->email)->toBe('depois@example.com');
});

it('updates a user password when provided', function (): void {
    $target = User::factory()->create();

    $this->actingAs($this->admin)->put(route('users.update', $target), [
        'name'            => $target->name,
        'email'           => $target->email,
        'password'        => 'nova-senha-forte',
        'additional_info' => null,
    ])->assertRedirect(route('users.index'));

    expect(\Illuminate\Support\Facades\Hash::check('nova-senha-forte', $target->fresh()->password))->toBeTrue();
});

it('redirects when user list page is beyond the last page', function (): void {
    User::factory()->count(12)->create();

    $this->actingAs($this->admin)
        ->get(route('users.index', ['page' => 50]))
        ->assertRedirect();
});

it('filters products index by search', function (): void {
    Product::factory()->create(['name' => 'Produto Busca XYZ', 'sku' => 'SKU-XYZ']);
    Product::factory()->create(['name' => 'Outro', 'sku' => 'SKU-999']);

    $this->actingAs($this->admin)
        ->get(route('products.index', ['search' => 'XYZ']))
        ->assertOk()
        ->assertSee('Produto Busca XYZ', false);
});

it('wraps admin listing tables in a horizontal scroll container on narrow viewports', function (): void {
    $product = Product::factory()->create();

    $urls = [
        route('products.index'),
        route('products.show', $product),
        route('categories.index'),
        route('warehouses.index'),
        route('users.index'),
    ];

    foreach ($urls as $url) {
        $this->actingAs($this->admin)
            ->get($url)
            ->assertOk()
            ->assertSee('overflow-x-auto', false);
    }
});

it('redirects when product list page is beyond the last page', function (): void {
    Product::factory()->count(11)->create();

    $this->actingAs($this->admin)
        ->get(route('products.index', ['page' => 100]))
        ->assertRedirect();
});

it('updates a product including sku change', function (): void {
    $this->mock(\App\Domain\Inventory\Contracts\ProductQrCodeGenerator::class, function ($mock): void {
        $mock->shouldReceive('generateAndStore')->once();
    });

    Storage::fake('public');

    $product = Product::factory()->create([
        'sku'           => 'SKU-OLD',
        'qr_code_path'  => 'qrcodes/old.png',
        'minimum_stock' => 0,
    ]);
    Storage::disk('public')->put('qrcodes/old.png', 'x');

    $this->actingAs($this->admin)->put(route('products.update', $product), [
        'category_id'     => $product->category_id,
        'name'            => $product->name,
        'sku'             => 'SKU-NEW-UNIQUE',
        'description'     => null,
        'price'           => $product->price,
        'minimum_stock'   => 0,
        'additional_info' => null,
    ])->assertRedirect(route('products.index'));

    expect($product->fresh()->sku)->toBe('SKU-NEW-UNIQUE');
});
