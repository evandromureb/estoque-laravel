<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
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
