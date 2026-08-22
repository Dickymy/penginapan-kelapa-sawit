<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'provider' => 'midtrans',
            'provider_order_id' => 'MID-' . fake()->unique()->numerify('##########'),
            'transaction_id' => Str::uuid(),
            'attempt_no' => 1,
            'snap_token' => Str::random(32),
            'payment_type' => fake()->randomElement(['credit_card', 'bank_transfer', 'echannel', 'gopay', 'qris']),
            'gross_amount' => function (array $attributes) {
                return Booking::find($attributes['booking_id'])?->total_amount ?? 500000;
            },
            'status' => PaymentStatus::Unpaid,
            'provider_transaction_status' => null,
            'fraud_status' => null,
            'provider_transaction_time' => null,
            'paid_at' => null,
            'expired_at' => null,
            'refunded_at' => null,
            'raw_response' => null,
            'last_status_checked_at' => null,
        ];
    }
}
