<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\{Product, ProductImage};
use Database\Seeders\Support\StandardProductSeedImages;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'path'       => fake()->randomElement(StandardProductSeedImages::relativePaths()),
            'is_primary' => false,
        ];
    }
}
