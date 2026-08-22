<?php

namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $floor = fake()->numberBetween(1, 3);
        $number = fake()->unique()->numberBetween(1, 99);
        $code = sprintf("%d%02d", $floor, $number);
        
        return [
            'room_type_id' => RoomType::factory(),
            'code' => $code,
            'name' => 'Room ' . $code,
            'floor' => $floor,
            'notes' => null,
            'status' => RoomStatus::Active,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
