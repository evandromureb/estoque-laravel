<?php

namespace Database\Seeders;

use App\Domain\Inventory\Contracts\ProductQrCodeGenerator;
use App\Models\{Category, Product, ProductImage, ProductLocation, User, Warehouse};
use Database\Seeders\Support\StandardProductSeedImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Quando true, associa imagens de demonstração em public/assets/images/products aos produtos.
     * Quando false, usa a factory (paths de exemplo; uploads reais ficam em storage).
     */
    public bool $useStandardProductImages = true;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // Random users
        User::factory(10)->create();

        // Armazéns nomeados (sempre presentes no seed)
        $warehouseNames = ['CD São Paulo', 'CD Rio', 'Loja Centro', 'Hub Nordeste', 'Filial Sul'];
        $warehouses     = collect();

        foreach ($warehouseNames as $name) {
            $warehouses->push(Warehouse::factory()->create([
                'name'            => $name,
                'location_string' => fake()->city(),
            ]));
        }

        // Categories
        $categories = Category::factory(8)->create();

        // Products com estoque mínimo explícito
        $products = Product::factory(20)
            ->recycle($categories)
            ->create();

        if ($this->useStandardProductImages) {
            StandardProductSeedImages::assertBundledFilesPresent();
        }

        $qrCodeGenerator = app(ProductQrCodeGenerator::class);

        foreach ($products as $index => $product) {
            $min = fake()->numberBetween(25, 90);
            $product->forceFill(['minimum_stock' => $min])->save();

            $qrCodeGenerator->generateAndStore($product);

            $imageCount = rand(1, 3);

            if ($this->useStandardProductImages) {
                foreach (StandardProductSeedImages::pickPathsForProduct($index, $imageCount) as $path) {
                    ProductImage::query()->create([
                        'product_id' => $product->id,
                        'path'       => $path,
                        'is_primary' => false,
                    ]);
                }
            } else {
                ProductImage::factory($imageCount)->create([
                    'product_id' => $product->id,
                ]);
            }

            $belowMinimum = fake()->boolean(45);
            $numLocations = fake()->numberBetween(1, 3);
            $targetTotal  = $belowMinimum
                ? fake()->numberBetween(0, max(0, $min - 1))
                : fake()->numberBetween($min, $min + 200);

            if ($targetTotal <= 0) {
                ProductLocation::query()->create([
                    'product_id'   => $product->id,
                    'warehouse_id' => $warehouses->random()->id,
                    'aisle'        => fake()->randomElement(['A', 'B', 'C', 'D']) . '-' . fake()->numberBetween(1, 9),
                    'shelf'        => (string) fake()->numberBetween(1, 40),
                    'quantity'     => 0,
                ]);

                continue;
            }

            foreach ($this->splitTotalAcrossBins($targetTotal, $numLocations) as $quantity) {
                ProductLocation::query()->create([
                    'product_id'   => $product->id,
                    'warehouse_id' => $warehouses->random()->id,
                    'aisle'        => fake()->randomElement(['A', 'B', 'C', 'D']) . '-' . fake()->numberBetween(1, 9),
                    'shelf'        => (string) fake()->numberBetween(1, 40),
                    'quantity'     => $quantity,
                ]);
            }
        }
    }

    /**
     * Parte o total em {@see $bins} quantidades inteiras positivas (soma = {@see $total}).
     *
     * @return list<int>
     */
    private function splitTotalAcrossBins(int $total, int $bins): array
    {
        if ($total <= 0) {
            return [];
        }

        $bins = max(1, min($bins, $total));

        if ($bins === 1) {
            return [$total];
        }

        $cutPoints = collect(range(1, $total - 1))
            ->random($bins - 1)
            ->sort()
            ->values()
            ->all();

        $points = array_merge([0], $cutPoints, [$total]);
        $qtys   = [];

        for ($i = 0; $i < $bins; $i++) {
            $qtys[] = $points[$i + 1] - $points[$i];
        }

        return $qtys;
    }
}
