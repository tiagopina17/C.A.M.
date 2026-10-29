<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->paragraph(),
            'place_id' => 'place_' . fake()->uuid(),
            'lat' => fake()->latitude(38.0, 42.0),
            'lon' => fake()->longitude(-9.5, -6.5),
        ];
    }
}
