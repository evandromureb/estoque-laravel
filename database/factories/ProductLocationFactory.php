<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\{Product, ProductLocation, Warehouse};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductLocation>
 */
class ProductLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id'   => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'aisle'        => $this->faker->bothify('Corredor-??'),
            'shelf'        => $this->faker->bothify('Prateleira-##'),
            'quantity'     => $this->faker->numberBetween(1, 500),
        ];
    }
}
