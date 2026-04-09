<?php

declare(strict_types = 1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'            => $this->faker->company() . ' Logistics',
            'location_string' => $this->faker->address(),
            'description'     => $this->faker->sentence(),
            'additional_info' => $this->faker->paragraph(),
        ];
    }
}
