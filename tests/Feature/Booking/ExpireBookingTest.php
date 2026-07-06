<?php

namespace Tests\Feature\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $roomType = RoomType::create([
            'name' => 'Twin',
            'slug' => 'twin',
            'capacity' => 2,
            'bed_count' => 2,
            'base_price' => 250000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Room::create([
            'room_type_id' => $roomType->id,
            'code' => 'TWIN-01',
            'name' => 'Twin 01',
            'status' => 'active',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_expire_command_expires_pending_bookings(): void
    {
        $room = Room::first();

        Booking::create([
            'booking_code' => 'BKG-202607-0010',
            'room_id' => $room->id,
            'status' => BookingStatus::PendingPayment->value,
            'payment_status' => 'unpaid',
            'source' => 'website',
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-03',
            'nights' => 2,
            'guest_name' => 'Tamu Expired',
            'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin',
            'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000,
            'total_amount' => 500000,
            'payment_expires_at' => now()->subMinutes(5),
        ]);

        $this->artisan('booking:expire-pending')
            ->assertExitCode(0);

        $booking = Booking::where('booking_code', 'BKG-202607-0010')->first();
        $this->assertEquals(BookingStatus::Expired, $booking->status);
    }

    public function test_expire_command_skips_confirmed_bookings(): void
    {
        $room = Room::first();

        Booking::create([
            'booking_code' => 'BKG-202607-0011',
            'room_id' => $room->id,
            'status' => BookingStatus::Confirmed->value,
            'payment_status' => 'paid',
            'source' => 'website',
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-03',
            'nights' => 2,
            'guest_name' => 'Tamu Confirmed',
            'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin',
            'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000,
            'total_amount' => 500000,
            'payment_expires_at' => now()->subMinutes(5),
        ]);

        $this->artisan('booking:expire-pending')
            ->assertExitCode(0);

        $booking = Booking::where('booking_code', 'BKG-202607-0011')->first();
        $this->assertEquals(BookingStatus::Confirmed, $booking->status);
    }

    public function test_expire_command_skips_still_valid_pending(): void
    {
        $room = Room::first();

        Booking::create([
            'booking_code' => 'BKG-202607-0012',
            'room_id' => $room->id,
            'status' => BookingStatus::PendingPayment->value,
            'payment_status' => 'unpaid',
            'source' => 'website',
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-03',
            'nights' => 2,
            'guest_name' => 'Tamu Valid',
            'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin',
            'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000,
            'total_amount' => 500000,
            'payment_expires_at' => now()->addMinutes(25), // still valid
        ]);

        $this->artisan('booking:expire-pending')
            ->assertExitCode(0);

        $booking = Booking::where('booking_code', 'BKG-202607-0012')->first();
        $this->assertEquals(BookingStatus::PendingPayment, $booking->status);
    }
}
