<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\PolicyVersion;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdempotencyTest extends TestCase
{
    use RefreshDatabase;

    private RoomType $roomType;
    private Room $room;

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

        $this->room = Room::create([
            'room_type_id' => $this->roomType->id,
            'code' => 'TWIN-01',
            'name' => 'Twin 01',
            'status' => 'active',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        PolicyVersion::create([
            'policy_key' => 'guest_policy',
            'version' => '2026-07-v1',
            'title' => 'Kebijakan Tamu',
            'content' => 'Isi kebijakan.',
            'is_current' => true,
            'published_at' => now(),
        ]);
    }

    public function test_double_click_submit_returns_same_booking(): void
    {
        $service = app(BookingService::class);

        $data = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 1,
            'guest_name' => 'Double Click User',
            'guest_email' => 'double@example.com',
            'guest_whatsapp' => '08123456789',
            'idempotency_key' => 'double-click-key-1',
        ];

        $result1 = $service->createGuestBooking($data);
        $result2 = $service->createGuestBooking($data);

        $this->assertEquals($result1['booking']->id, $result2['booking']->id);
        $this->assertEquals(1, Booking::count());
    }

    public function test_different_dates_different_bookings(): void
    {
        // Create second room so both can succeed
        Room::create([
            'room_type_id' => $this->roomType->id,
            'code' => 'TWIN-02',
            'name' => 'Twin 02',
            'status' => 'active',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $service = app(BookingService::class);

        $dataA = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 1,
            'guest_name' => 'Tab A User',
            'guest_email' => 'taba@example.com',
            'guest_whatsapp' => '08123456789',
            'idempotency_key' => 'same-session-key',
        ];

        $dataB = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guest_count' => 1,
            'guest_name' => 'Tab B User',
            'guest_email' => 'tabb@example.com',
            'guest_whatsapp' => '08123456789',
            'idempotency_key' => 'same-session-key',
        ];

        $resultA = $service->createGuestBooking($dataA);
        $resultB = $service->createGuestBooking($dataB);

        // Different dates + same raw key = different fingerprinted keys = different bookings
        $this->assertNotEquals($resultA['booking']->id, $resultB['booking']->id);
        $this->assertEquals(2, Booking::count());
    }

    public function test_different_room_types_different_bookings(): void
    {
        $otherType = RoomType::create([
            'name' => 'Deluxe',
            'slug' => 'deluxe',
            'capacity' => 3,
            'bed_count' => 1,
            'base_price' => 400000,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Room::create([
            'room_type_id' => $otherType->id,
            'code' => 'DLX-01',
            'name' => 'Deluxe 01',
            'status' => 'active',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $service = app(BookingService::class);

        $dataA = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 1,
            'guest_name' => 'Tab A User',
            'guest_email' => 'user@example.com',
            'guest_whatsapp' => '08123456789',
            'idempotency_key' => 'same-key',
        ];

        $dataB = [
            'room_type_id' => $otherType->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 1,
            'guest_name' => 'Tab B User',
            'guest_email' => 'user@example.com',
            'guest_whatsapp' => '08123456789',
            'idempotency_key' => 'same-key',
        ];

        $resultA = $service->createGuestBooking($dataA);
        $resultB = $service->createGuestBooking($dataB);

        // Different room types + same raw key = different bookings
        $this->assertNotEquals($resultA['booking']->id, $resultB['booking']->id);
    }

    public function test_refresh_submit_returns_same_booking(): void
    {
        $service = app(BookingService::class);

        $data = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 2,
            'guest_name' => 'Refresh User',
            'guest_email' => 'refresh@example.com',
            'guest_whatsapp' => '08123456789',
            'idempotency_key' => 'refresh-key',
        ];

        // Simulate multiple submits of the exact same data
        $result1 = $service->createGuestBooking($data);
        $result2 = $service->createGuestBooking($data);
        $result3 = $service->createGuestBooking($data);

        $this->assertEquals($result1['booking']->id, $result2['booking']->id);
        $this->assertEquals($result1['booking']->id, $result3['booking']->id);
        $this->assertEquals(1, Booking::count());
    }
}
