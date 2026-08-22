<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_type_factory_works(): void
    {
        $roomType = RoomType::factory()->create();
        
        $this->assertDatabaseHas('room_types', [
            'id' => $roomType->id,
        ]);
    }

    public function test_room_factory_works(): void
    {
        $room = Room::factory()->create();
        
        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'room_type_id' => $room->room_type_id,
        ]);
    }

    public function test_booking_factory_works_with_all_states(): void
    {
        $states = [
            'pendingPayment',
            'confirmed',
            'checkedIn',
            'checkedOut',
            'completed',
            'cancelled',
            'expired',
        ];

        foreach ($states as $state) {
            $booking = Booking::factory()->{$state}()->create();
            
            $this->assertDatabaseHas('bookings', [
                'id' => $booking->id,
                'status' => $booking->status->value,
            ]);
        }
    }

    public function test_payment_factory_works(): void
    {
        $payment = Payment::factory()->create();
        
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'booking_id' => $payment->booking_id,
        ]);
    }
}
