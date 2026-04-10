<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id'     => Category::factory(),
            'name'            => $this->faker->words(3, true),
            'sku'             => $this->faker->unique()->bothify('PROD-####-????'),
            'description'     => $this->faker->paragraph(),
            'price'           => $this->faker->randomFloat(2, 10, 1000),
            'minimum_stock'   => $this->faker->numberBetween(15, 120),
            'additional_info' => $this->faker->paragraph(),
        ];
    }
}
