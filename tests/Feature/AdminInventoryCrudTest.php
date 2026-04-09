<?php

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\{Category, Product, ProductImage, ProductLocation, User, Warehouse};
use Database\Seeders\Support\StandardProductSeedImages;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    $this->admin  = User::factory()->admin()->create();
    $this->member = User::factory()->create();
});

describe('user management', function (): void {
    it('redirects guests from users index', function (): void {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    });

    it('forbids non-admin from users index', function (): void {
        $this->actingAs($this->member)->get(route('users.index'))->assertForbidden();
    });

    it('allows admin to view users index', function (): void {
        User::factory()->create(['name' => 'Usuário comum']);

        $this->actingAs($this->admin)->get(route('users.index'))
            ->assertOk()
            ->assertSee('w-full min-w-full divide-y divide-gray-200', false)
            ->assertSee('Perfil')
            ->assertSee('Conta')
            ->assertSee('Administrador')
            ->assertSee('Membro');
    });

    it('allows admin to create a user', function (): void {
        $this->actingAs($this->admin)->post(route('users.store'), [
            'name'            => 'Novo Usuário',
            'email'           => 'novo@example.com',
            'password'        => 'senha-segura',
            'additional_info' => null,
        ])->assertRedirect(route('users.index'));

        expect(User::query()->where('email', 'novo@example.com')->exists())->toBeTrue();
    });

    it('prevents admin from deleting their own account via user management', function (): void {
        $this->actingAs($this->admin)->delete(route('users.destroy', $this->admin))->assertForbidden();
    });

    it('allows admin to delete another user', function (): void {
        $other = User::factory()->create();

        $this->actingAs($this->admin)->delete(route('users.destroy', $other))->assertRedirect(route('users.index'));

        expect(User::query()->find($other->id))->toBeNull();
    });

    it('redirects to _return_url when provided on user store', function (): void {
        $return = route('users.index', ['page' => 2]);

        $this->actingAs($this->admin)->post(route('users.store'), [
            'name'            => 'Com Retorno',
            'email'           => 'retorno@example.com',
            'password'        => 'senha-segura',
            'additional_info' => null,
            '_return_url'     => $return,
        ])->assertRedirect($return);
    });

    it('allows admin to create an api token for a user', function (): void {
        $target = User::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('users.api-token.store', $target), [
            'token_name' => 'Integração teste',
        ]);

        $response->assertRedirect(route('users.index'))
            ->assertSessionHas('api_token_plain')
            ->assertSessionHas('success');

        $plain = (string) $response->getSession()->get('api_token_plain');
        expect($plain)->not->toBe('');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id'   => $target->id,
            'name'           => 'Integração teste',
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $plain)
            ->getJson('/api/v1/categories')
            ->assertOk();
    });

    it('prevents a second api token with the default name for the same user', function (): void {
        $target = User::factory()->create();

        $this->actingAs($this->admin)->post(route('users.api-token.store', $target), [
            'token_name' => User::DEFAULT_API_TOKEN_NAME,
        ])->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('api_token_plain');

        $this->actingAs($this->admin)->post(route('users.api-token.store', $target), [
            'token_name' => User::DEFAULT_API_TOKEN_NAME,
        ])->assertRedirect()
            ->assertSessionHasErrors('token_name');
    });

    it('allows default-named tokens for different users', function (): void {
        $first  = User::factory()->create();
        $second = User::factory()->create();
        $name   = User::DEFAULT_API_TOKEN_NAME;

        $this->actingAs($this->admin)->post(route('users.api-token.store', $first), [
            'token_name' => $name,
        ])->assertSessionHas('api_token_plain');

        $this->actingAs($this->admin)->post(route('users.api-token.store', $second), [
            'token_name' => $name,
        ])->assertSessionHas('api_token_plain');
    });

    it('allows admin to revoke the default api token for a user', function (): void {
        $target = User::factory()->create();
        $target->createToken(User::DEFAULT_API_TOKEN_NAME);

        $this->actingAs($this->admin)
            ->from(route('users.index'))
            ->delete(route('users.api-token.destroy', $target))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        expect($target->tokens()->where('name', User::DEFAULT_API_TOKEN_NAME)->exists())->toBeFalse();
    });

    it('reports success when revoking default token that does not exist', function (): void {
        $target = User::factory()->create();

        $this->actingAs($this->admin)
            ->from(route('users.index'))
            ->delete(route('users.api-token.destroy', $target))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');
    });

    it('forbids non-admin from revoking an api token', function (): void {
        $target = User::factory()->create();
        $target->createToken(User::DEFAULT_API_TOKEN_NAME);

        $this->actingAs($this->member)
            ->delete(route('users.api-token.destroy', $target))
            ->assertForbidden();
    });

    it('forbids non-admin from creating an api token', function (): void {
        $target = User::factory()->create();

        $this->actingAs($this->member)->post(route('users.api-token.store', $target), [
            'token_name' => 'Tentativa',
        ])->assertForbidden();
    });

    it('redirects guests from api token creation', function (): void {
        $target = User::factory()->create();

        $this->post(route('users.api-token.store', $target), [
            'token_name' => 'X',
        ])->assertRedirect(route('login'));
    });
});

describe('category management', function (): void {
    it('forbids non-admin from categories index', function (): void {
        $this->actingAs($this->member)->get(route('categories.index'))->assertForbidden();
    });

    it('allows admin to create and delete a category', function (): void {
        $this->actingAs($this->admin)->post(route('categories.store'), [
            'name'            => 'Eletrônicos',
            'description'     => 'Teste',
            'additional_info' => null,
        ])->assertRedirect(route('categories.index'));

        $category = Category::query()->where('name', 'Eletrônicos')->first();
        expect($category)->not->toBeNull();

        $this->actingAs($this->admin)->delete(route('categories.destroy', $category))->assertRedirect(route('categories.index'));

        expect(Category::query()->find($category->id))->toBeNull();
    });

    it('prevents deleting a category that has products', function (): void {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin)->delete(route('categories.destroy', $category))->assertRedirect(route('categories.index'));

        expect(Category::query()->find($category->id))->not->toBeNull();
    });
});

describe('warehouse management', function (): void {
    it('forbids non-admin from warehouses index', function (): void {
        $this->actingAs($this->member)->get(route('warehouses.index'))->assertForbidden();
    });

    it('allows admin to create and delete a warehouse', function (): void {
        $this->actingAs($this->admin)->post(route('warehouses.store'), [
            'name'            => 'CD-SP',
            'location_string' => 'São Paulo',
            'description'     => null,
            'additional_info' => null,
        ])->assertRedirect(route('warehouses.index'));

        $warehouse = Warehouse::query()->where('name', 'CD-SP')->first();
        expect($warehouse)->not->toBeNull();

        $this->actingAs($this->admin)->delete(route('warehouses.destroy', $warehouse))->assertRedirect(route('warehouses.index'));

        expect(Warehouse::query()->find($warehouse->id))->toBeNull();
    });

    it('prevents deleting a warehouse that has stock rows', function (): void {
        $warehouse = Warehouse::factory()->create();
        $product   = Product::factory()->create();
        ProductLocation::factory()->create([
            'product_id'   => $product->id,
            'warehouse_id' => $warehouse->id,
        ]);

        $this->actingAs($this->admin)->delete(route('warehouses.destroy', $warehouse))->assertRedirect(route('warehouses.index'));

        expect(Warehouse::query()->find($warehouse->id))->not->toBeNull();
    });
});

describe('product management', function (): void {
    it('allows any authenticated user to view products index', function (): void {
        Product::factory()->create();

        $this->actingAs($this->member)->get(route('products.index'))
            ->assertOk()
            ->assertSee('openViewModal', false);
    });

    it('allows any authenticated user to open the product show page', function (): void {
        $product = Product::factory()->create();

        $this->actingAs($this->member)->get(route('products.show', $product))->assertOk();
    });

    it('shows stock and image counts on products index', function (): void {
        $product   = Product::factory()->create(['sku' => 'SKU-LIST-QTY']);
        $warehouse = Warehouse::factory()->create();
        ProductLocation::factory()->create([
            'product_id'   => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity'     => 15,
        ]);

        $this->actingAs($this->member)->get(route('products.index'))
            ->assertOk()
            ->assertSee('Estoque')
            ->assertSee('un. totais')
            ->assertSee('15')
            ->assertSee('SKU-LIST-QTY');
    });

    it('forbids non-admin from storing a product', function (): void {
        $category = Category::factory()->create();

        $this->actingAs($this->member)->post(route('products.store'), [
            'category_id' => $category->id,
            'name'        => 'X',
            'sku'         => 'SKU-X',
            'price'       => 1,
            'images'      => [UploadedFile::fake()->image('a.jpg', 100, 100)],
        ])->assertForbidden();
    });

    it('allows admin to store a product with images and qr code', function (): void {
        Storage::fake('public');
        $this->mock(ProductQrCodeGenerator::class, function ($mock): void {
            $mock->shouldReceive('generateAndStore')
                ->once()
                ->andReturnUsing(function (Product $product): void {
                    Storage::disk('public')->put('qrcodes/fake.png', 'fake-binary');
                    $product->forceFill(['qr_code_path' => 'qrcodes/fake.png'])->save();
                });
        });
        $category = Category::factory()->create();

        $this->actingAs($this->admin)->post(route('products.store'), [
            'category_id'     => $category->id,
            'name'            => 'Produto Teste',
            'sku'             => 'SKU-TEST-001',
            'description'     => 'Desc',
            'price'           => 99.9,
            'minimum_stock'   => 12,
            'additional_info' => null,
            'images'          => [UploadedFile::fake()->image('foto.jpg', 100, 100)],
        ])->assertRedirect(route('products.index'));

        $product = Product::query()->where('sku', 'SKU-TEST-001')->first();
        expect($product)->not->toBeNull();
        expect((int) $product->minimum_stock)->toBe(12);
        expect($product->qr_code_path)->not->toBeNull();
        Storage::disk('public')->assertExists($product->qr_code_path);
    });

    it('allows admin to delete a product and remove files from storage', function (): void {
        Storage::fake('public');
        $product = Product::factory()->create(['qr_code_path' => 'qrcodes/test.png']);
        Storage::disk('public')->put('qrcodes/test.png', 'fake');
        $path = $product->images()->create(['path' => 'product_images/x.jpg', 'is_primary' => false])->path;
        Storage::disk('public')->put($path, 'fake');

        $this->actingAs($this->admin)->delete(route('products.destroy', $product))->assertRedirect(route('products.index'));

        Storage::disk('public')->assertMissing('qrcodes/test.png');
        Storage::disk('public')->assertMissing($path);
        expect(Product::query()->find($product->id))->toBeNull();
    });

    it('allows admin to add images to an existing product', function (): void {
        Storage::fake('public');
        $product = Product::factory()->create();

        $this->actingAs($this->admin)->post(route('products.images.store', $product), [
            'images' => [
                UploadedFile::fake()->image('one.jpg', 100, 100),
                UploadedFile::fake()->image('two.png', 100, 100),
            ],
        ])->assertRedirect();

        expect($product->images()->count())->toBe(2);
    });

    it('forbids non-admin from adding product images', function (): void {
        $product = Product::factory()->create();

        $this->actingAs($this->member)->post(route('products.images.store', $product), [
            'images' => [UploadedFile::fake()->image('a.jpg', 100, 100)],
        ])->assertForbidden();
    });

    it('allows admin to delete a single product image', function (): void {
        Storage::fake('public');
        $product = Product::factory()->create();
        /** @var ProductImage $image */
        $image = $product->images()->create([
            'path'       => 'product_images/keep.jpg',
            'is_primary' => false,
        ]);
        Storage::disk('public')->put($image->path, 'fake');

        $this->actingAs($this->admin)->delete(route('products.images.destroy', [$product, $image]))->assertRedirect();

        expect(ProductImage::query()->find($image->id))->toBeNull();
        Storage::disk('public')->assertMissing($image->path);
    });

    it('allows admin to remove a bundled demo image record without touching storage', function (): void {
        Storage::fake('public');
        $product = Product::factory()->create();
        /** @var ProductImage $image */
        $image = $product->images()->create([
            'path'       => 'assets/images/products/demo-1.png',
            'is_primary' => false,
        ]);

        $this->actingAs($this->admin)->delete(route('products.images.destroy', [$product, $image]))->assertRedirect();

        expect(ProductImage::query()->find($image->id))->toBeNull();
        Storage::disk('public')->assertMissing($image->path);
    });

    it('returns 404 when product image does not belong to product', function (): void {
        $product = Product::factory()->create();
        $other   = Product::factory()->create();
        $image   = $other->images()->create([
            'path'       => 'product_images/x.jpg',
            'is_primary' => false,
        ]);

        $this->actingAs($this->admin)->delete(route('products.images.destroy', [$product, $image]))->assertNotFound();
    });

    it('declares bundled demo product images under public assets', function (): void {
        foreach (StandardProductSeedImages::relativePaths() as $path) {
            expect(file_exists(public_path($path)))->toBeTrue();
        }
    });
});

describe('product location management', function (): void {
    it('forbids non-admin from storing a product location', function (): void {
        $product   = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $this->actingAs($this->member)->post(route('product-locations.store'), [
            'product_id'   => $product->id,
            'warehouse_id' => $warehouse->id,
            'aisle'        => 'A',
            'shelf'        => '1',
            'quantity'     => 5,
        ])->assertForbidden();
    });

    it('allows admin to allocate stock and merge duplicate bins', function (): void {
        $product   = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $this->actingAs($this->admin)->post(route('product-locations.store'), [
            'product_id'   => $product->id,
            'warehouse_id' => $warehouse->id,
            'aisle'        => 'B',
            'shelf'        => '2',
            'quantity'     => 3,
        ])->assertRedirect();

        $this->actingAs($this->admin)->post(route('product-locations.store'), [
            'product_id'   => $product->id,
            'warehouse_id' => $warehouse->id,
            'aisle'        => 'B',
            'shelf'        => '2',
            'quantity'     => 2,
        ])->assertRedirect();

        $loc = ProductLocation::query()
            ->where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('aisle', 'B')
            ->where('shelf', '2')
            ->first();

        expect($loc->quantity)->toBe(5);
    });

    it('allows admin to delete a product location', function (): void {
        $location = ProductLocation::factory()->create();

        $this->actingAs($this->admin)->delete(route('product-locations.destroy', $location))->assertRedirect();

        expect(ProductLocation::query()->find($location->id))->toBeNull();
    });

    it('allows admin to update a product location', function (): void {
        $location  = ProductLocation::factory()->create(['aisle' => 'A', 'shelf' => '1', 'quantity' => 4]);
        $warehouse = Warehouse::factory()->create();
        $showUrl   = route('products.show', $location->product);

        $this->actingAs($this->admin)->from($showUrl)->put(route('product-locations.update', $location), [
            'warehouse_id' => $warehouse->id,
            'aisle'        => 'B',
            'shelf'        => '2',
            'quantity'     => 12,
            '_return_url'  => $showUrl,
        ])->assertRedirect($showUrl);

        $location->refresh();
        expect($location->warehouse_id)->toBe($warehouse->id)
            ->and($location->aisle)->toBe('B')
            ->and($location->shelf)->toBe('2')
            ->and($location->quantity)->toBe(12);
    });
});

describe('dashboard', function (): void {
    it('shows dashboard for authenticated users', function (): void {
        $this->actingAs($this->member)->get(route('dashboard'))->assertOk();
    });

    it('lists products below configured minimum stock total', function (): void {
        $category = Category::factory()->create();
        $low      = Product::factory()->create([
            'category_id'   => $category->id,
            'name'          => 'Produto Estoque Baixo Dash',
            'sku'           => 'SKU-DASH-LOW',
            'minimum_stock' => 50,
        ]);
        $warehouse = Warehouse::factory()->create();
        ProductLocation::factory()->create([
            'product_id'   => $low->id,
            'warehouse_id' => $warehouse->id,
            'quantity'     => 5,
        ]);

        $this->actingAs($this->member)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Abaixo do estoque mínimo', false)
            ->assertSee('Produto Estoque Baixo Dash', false)
            ->assertSee('SKU-DASH-LOW', false);
    });
});
