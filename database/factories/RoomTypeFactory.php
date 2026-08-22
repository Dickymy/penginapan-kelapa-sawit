<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true) . ' Room';
        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'capacity' => fake()->numberBetween(1, 4),
            'bed_count' => fake()->numberBetween(1, 2),
            'bed_type' => fake()->randomElement(['King', 'Queen', 'Twin', 'Single']),
            'base_price' => fake()->numberBetween(3, 15) * 100000,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
