<?php

namespace Tests\Feature\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    use RefreshDatabase;

    private RoomType $roomType;
    private Room $room1;
    private Room $room2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roomType = RoomType::create([
            'name' => 'Twin',
            'slug' => 'twin',
            'capacity' => 2,
            'bed_count' => 2,
            'base_price' => 250000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->room1 = Room::create([
            'room_type_id' => $this->roomType->id,
            'code' => 'TWIN-01',
            'name' => 'Twin 01',
            'status' => 'active',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->room2 = Room::create([
            'room_type_id' => $this->roomType->id,
            'code' => 'TWIN-02',
            'name' => 'Twin 02',
            'status' => 'active',
            'is_active' => true,
            'sort_order' => 2,
        ]);
    }

    public function test_available_rooms_when_no_bookings(): void
    {
        $service = app(AvailabilityService::class);

        $result = $service->isRoomAvailable(
            $this->room1->id,
            Carbon::parse('2026-08-01'),
            Carbon::parse('2026-08-03')
        );

        $this->assertTrue($result);
    }

    public function test_room_blocked_by_confirmed_booking(): void
    {
        Booking::create([
            'booking_code' => 'BKG-202607-0001',
            'room_id' => $this->room1->id,
            'status' => BookingStatus::Confirmed->value,
            'payment_status' => 'paid',
            'source' => 'website',
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-03',
            'nights' => 2,
            'guest_name' => 'Tamu Test',
            'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin',
            'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000,
            'total_amount' => 500000,
        ]);

        $service = app(AvailabilityService::class);

        // Same dates → blocked
        $this->assertFalse($service->isRoomAvailable(
            $this->room1->id,
            Carbon::parse('2026-08-01'),
            Carbon::parse('2026-08-03')
        ));

        // Overlapping dates → blocked
        $this->assertFalse($service->isRoomAvailable(
            $this->room1->id,
            Carbon::parse('2026-08-02'),
            Carbon::parse('2026-08-04')
        ));

        // Adjacent dates (checkout day = new checkin) → available (half-open)
        $this->assertTrue($service->isRoomAvailable(
            $this->room1->id,
            Carbon::parse('2026-08-03'),
            Carbon::parse('2026-08-05')
        ));
    }

    public function test_pending_booking_blocks_while_not_expired(): void
    {
        Booking::create([
            'booking_code' => 'BKG-202607-0002',
            'room_id' => $this->room1->id,
            'status' => BookingStatus::PendingPayment->value,
            'payment_status' => 'unpaid',
            'source' => 'website',
            'check_in' => '2026-08-10',
            'check_out' => '2026-08-12',
            'nights' => 2,
            'guest_name' => 'Tamu Hold',
            'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin',
            'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000,
            'total_amount' => 500000,
            'payment_expires_at' => now()->addMinutes(30), // still valid
        ]);

        $service = app(AvailabilityService::class);

        // Blocked while hold active
        $this->assertFalse($service->isRoomAvailable(
            $this->room1->id,
            Carbon::parse('2026-08-10'),
            Carbon::parse('2026-08-12')
        ));
    }

    public function test_expired_pending_booking_does_not_block(): void
    {
        Booking::create([
            'booking_code' => 'BKG-202607-0003',
            'room_id' => $this->room1->id,
            'status' => BookingStatus::PendingPayment->value,
            'payment_status' => 'unpaid',
            'source' => 'website',
            'check_in' => '2026-08-10',
            'check_out' => '2026-08-12',
            'nights' => 2,
            'guest_name' => 'Tamu Expired',
            'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin',
            'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000,
            'total_amount' => 500000,
            'payment_expires_at' => now()->subMinutes(5), // already expired
        ]);

        $service = app(AvailabilityService::class);

        // Not blocked — hold expired
        $this->assertTrue($service->isRoomAvailable(
            $this->room1->id,
            Carbon::parse('2026-08-10'),
            Carbon::parse('2026-08-12')
        ));
    }

    public function test_search_availability_page(): void
    {
        $response = $this->get(route('availability.search', [
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Twin');
    }

    public function test_search_availability_hides_inactive_room_types(): void
    {
        $this->roomType->update(['is_active' => false]);

        $response = $this->get(route('availability.search', [
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('Twin');
    }
}
