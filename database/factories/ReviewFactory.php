<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'booking_id' => Booking::factory()->checkedOut(),
            'rating' => fake()->numberBetween(3, 5),
            'title' => fake()->sentence(3),
            'comment' => fake()->paragraph(),
            'is_published' => fake()->boolean(80),
            'admin_reply' => null,
            'replied_at' => null,
        ];
    }
}
