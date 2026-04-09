<?php

declare(strict_types = 1);

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\{Category, Product, ProductImage, ProductLocation, User, Warehouse};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    $this->admin  = User::factory()->admin()->create();
    $this->member = User::factory()->create();

    $this->mock(ProductQrCodeGenerator::class, function ($mock): void {
        $mock->shouldReceive('generateAndStore')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function (Product $product): void {
                $product->forceFill(['qr_code_path' => 'qrcodes/fake.png'])->save();
            });
    });
});

it('returns 401 for unauthenticated api requests', function (): void {
    $this->getJson('/api/v1/products')->assertUnauthorized();
});

it('authenticates api requests with a sanctum bearer token', function (): void {
    Product::factory()->create();

    $token = $this->admin->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/v1/products')
        ->assertSuccessful();
});

it('forbids non-admin from category endpoints', function (): void {
    $this->actingAs($this->member, 'sanctum')->getJson('/api/v1/categories')->assertForbidden();
});

it('lists categories for admin', function (): void {
    Category::factory()->count(2)->create();

    $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/v1/categories');

    $response->assertSuccessful()
        ->assertJsonStructure(['data' => [['id', 'name']]]);
});

it('validates category store payload', function (): void {
    $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/categories', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('creates and deletes a category', function (): void {
    $create = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/categories', [
        'name'        => 'Eletrônicos',
        'description' => 'Teste',
    ]);

    $create->assertCreated()
        ->assertJsonPath('data.name', 'Eletrônicos');

    $id = $create->json('data.id');

    $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/categories/{$id}")
        ->assertNoContent();
});

it('lists warehouses for admin', function (): void {
    Warehouse::factory()->create(['name' => 'CD Norte']);

    $this->actingAs($this->admin, 'sanctum')
        ->getJson('/api/v1/warehouses')
        ->assertSuccessful()
        ->assertJsonFragment(['name' => 'CD Norte']);
});

it('validates warehouse store payload', function (): void {
    $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/warehouses', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('lists products for any authenticated user', function (): void {
    Product::factory()->create();

    $this->actingAs($this->member, 'sanctum')
        ->getJson('/api/v1/products')
        ->assertSuccessful()
        ->assertJsonStructure(['data' => [['id', 'sku']]]);
});

it('forbids non-admin from creating products', function (): void {
    $category = Category::factory()->create();

    $this->actingAs($this->member, 'sanctum')
        ->postJson('/api/v1/products', [
            'category_id'   => $category->id,
            'name'          => 'Item',
            'sku'           => 'SKU-1',
            'price'         => 10.5,
            'minimum_stock' => 0,
        ])
        ->assertForbidden();
});

it('validates product store payload for admin', function (): void {
    $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/products', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['category_id', 'name', 'sku', 'price', 'minimum_stock']);
});

it('creates a product without images via api', function (): void {
    $category = Category::factory()->create();

    $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/products', [
        'category_id'   => $category->id,
        'name'          => 'Teclado',
        'sku'           => 'KB-001',
        'price'         => 99.9,
        'minimum_stock' => 2,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.sku', 'KB-001')
        ->assertJsonPath('data.total_stock_quantity', 0);
});

it('updates a product for admin', function (): void {
    $product = Product::factory()->create(['name' => 'Antigo']);

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/products/{$product->id}", [
            'category_id'   => $product->category_id,
            'name'          => 'Novo nome',
            'sku'           => $product->sku,
            'price'         => $product->price,
            'minimum_stock' => $product->minimum_stock,
        ])
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Novo nome');
});

it('deletes a product for admin', function (): void {
    $product = Product::factory()->create();

    $this->actingAs($this->admin, 'sanctum')
        ->deleteJson("/api/v1/products/{$product->id}")
        ->assertNoContent();

    expect(Product::query()->find($product->id))->toBeNull();
});

it('manages product locations for admin', function (): void {
    $product   = Product::factory()->create();
    $warehouse = Warehouse::factory()->create();

    $store = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/product-locations', [
        'product_id'   => $product->id,
        'warehouse_id' => $warehouse->id,
        'aisle'        => 'A',
        'shelf'        => '1',
        'quantity'     => 5,
    ]);

    $store->assertCreated()->assertJsonPath('data.quantity', 5);

    $id = $store->json('data.id');

    $this->actingAs($this->admin, 'sanctum')
        ->putJson("/api/v1/product-locations/{$id}", [
            'warehouse_id' => $warehouse->id,
            'aisle'        => 'A',
            'shelf'        => '1',
            'quantity'     => 8,
        ])
        ->assertSuccessful()
        ->assertJsonPath('data.quantity', 8);

    $this->actingAs($this->admin, 'sanctum')
        ->deleteJson("/api/v1/product-locations/{$id}")
        ->assertNoContent();
});

it('rejects duplicate bin on product location update via api', function (): void {
    $admin     = $this->admin;
    $product   = Product::factory()->create();
    $warehouse = Warehouse::factory()->create();

    ProductLocation::factory()->create([
        'product_id'   => $product->id,
        'warehouse_id' => $warehouse->id,
        'aisle'        => 'A',
        'shelf'        => '1',
        'quantity'     => 5,
    ]);

    $second = ProductLocation::factory()->create([
        'product_id'   => $product->id,
        'warehouse_id' => $warehouse->id,
        'aisle'        => 'B',
        'shelf'        => '2',
        'quantity'     => 3,
    ]);

    $this->actingAs($admin, 'sanctum')->putJson("/api/v1/product-locations/{$second->id}", [
        'warehouse_id' => $warehouse->id,
        'aisle'        => 'A',
        'shelf'        => '1',
        'quantity'     => 2,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['warehouse_id']);
});

it('manages users for admin', function (): void {
    $create = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/users', [
        'name'     => 'Novo',
        'email'    => 'novo@example.com',
        'password' => 'senha-segura',
        'is_admin' => false,
    ]);

    $create->assertCreated()->assertJsonPath('data.email', 'novo@example.com');

    $id = $create->json('data.id');

    $this->actingAs($this->admin, 'sanctum')
        ->getJson("/api/v1/users/{$id}")
        ->assertSuccessful();
});

it('validates user store payload', function (): void {
    $this->actingAs($this->admin, 'sanctum')
        ->postJson('/api/v1/users', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('forbids non-admin from user index', function (): void {
    $this->actingAs($this->member, 'sanctum')->getJson('/api/v1/users')->assertForbidden();
});

it('stores product images via multipart upload', function (): void {
    Storage::fake('public');

    $product = Product::factory()->create();
    $file    = UploadedFile::fake()->image('foto.jpg', 100, 100);

    $response = $this->actingAs($this->admin, 'sanctum')->post("/api/v1/products/{$product->id}/images", [
        'images' => [$file],
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['data' => [['id', 'url']]]);

    expect(ProductImage::query()->where('product_id', $product->id)->count())->toBeGreaterThan(0);
});

it('deletes a product image', function (): void {
    Storage::fake('public');

    $product = Product::factory()->create();
    $image   = ProductImage::factory()->for($product)->create([
        'path' => 'product_images/test.jpg',
    ]);

    Storage::disk('public')->put($image->path, 'fake');

    $this->actingAs($this->admin, 'sanctum')
        ->deleteJson("/api/v1/products/{$product->id}/images/{$image->id}")
        ->assertNoContent();

    expect(ProductImage::query()->find($image->id))->toBeNull();
});

it('returns 404 when deleting image from another product', function (): void {
    $p1    = Product::factory()->create();
    $p2    = Product::factory()->create();
    $image = ProductImage::factory()->for($p2)->create();

    $this->actingAs($this->admin, 'sanctum')
        ->deleteJson("/api/v1/products/{$p1->id}/images/{$image->id}")
        ->assertNotFound();
});
