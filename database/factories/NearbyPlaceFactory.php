<?php

namespace Database\Factories;

use App\Models\NearbyPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NearbyPlace>
 */
class NearbyPlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'category' => $this->faker->randomElement(['Wisata', 'Kuliner', 'Transportasi', 'Kesehatan', 'Belanja']),
            'distance' => $this->faker->numberBetween(1, 15) . ' km',
            'description' => $this->faker->paragraph(),
            'image' => null,
            'map_link' => 'https://maps.google.com/?q=' . $this->faker->latitude . ',' . $this->faker->longitude,
            'sort_order' => $this->faker->numberBetween(0, 10),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
