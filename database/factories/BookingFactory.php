<?php

namespace Database\Factories;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = Carbon::tomorrow();
        $nights = 1;
        $checkOut = $checkIn->copy()->addDays($nights);
        $pricePerNight = 500000;
        $subtotal = $pricePerNight * $nights;
        $totalAmount = $subtotal;

        return [
            'booking_code' => 'BKG-' . fake()->unique()->numerify('######'),
            'invoice_number' => 'INV-' . fake()->unique()->numerify('######'),
            'idempotency_key' => Str::uuid(),
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'created_by_admin_id' => null,
            'source' => BookingSource::Website,
            'status' => BookingStatus::PendingPayment,
            'payment_status' => PaymentStatus::Unpaid,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'nights' => $nights,
            'guest_count' => 2,
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'guest_whatsapp' => '62' . fake()->numerify('8##########'),
            'arrival_estimate' => '14:00',
            'special_request' => null,
            'room_type_name_snapshot' => 'Deluxe Room',
            'room_name_snapshot' => '101',
            'price_per_night_snapshot' => $pricePerNight,
            'subtotal' => $subtotal,
            'promotion_id' => null,
            'promotion_code_snapshot' => null,
            'promotion_discount' => 0,
            'points_redeemed' => 0,
            'points_discount' => 0,
            'total_amount' => $totalAmount,
            'currency' => 'IDR',
            'eligible_loyalty_amount' => $totalAmount,
            'payment_expires_at' => now()->addHours(2),
            'policy_version_id' => null,
            'policy_accepted_at' => null,
            'guest_access_token_hash' => hash('sha256', Str::random(40)),
            'needs_attention' => false,
        ];
    }

    public function pendingPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::PendingPayment,
            'payment_status' => PaymentStatus::Unpaid,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Confirmed,
            'payment_status' => PaymentStatus::Paid,
            'payment_expires_at' => null,
        ]);
    }

    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::CheckedIn,
            'payment_status' => PaymentStatus::Paid,
            'checked_in_at' => Carbon::now(),
            'payment_expires_at' => null,
        ]);
    }

    public function checkedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::CheckedOut,
            'payment_status' => PaymentStatus::Paid,
            'checked_in_at' => Carbon::now()->subDays(1),
            'checked_out_at' => Carbon::now(),
            'payment_expires_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Completed,
            'payment_status' => PaymentStatus::Paid,
            'checked_in_at' => Carbon::now()->subDays(1),
            'checked_out_at' => Carbon::now(),
            'completed_at' => Carbon::now(),
            'payment_expires_at' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Cancelled,
            'cancelled_at' => Carbon::now(),
            'cancellation_reason' => 'User request',
            'payment_expires_at' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookingStatus::Expired,
            'payment_expires_at' => Carbon::now()->subHour(),
        ]);
    }
}
