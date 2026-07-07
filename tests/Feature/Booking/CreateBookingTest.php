<?php

namespace Tests\Feature\Booking;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\PolicyVersion;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateBookingTest extends TestCase
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

    public function test_create_guest_booking_via_service(): void
    {
        $service = app(BookingService::class);

        $result = $service->createGuestBooking([
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 2,
            'guest_name' => 'Budi Santoso',
            'guest_email' => 'budi@example.com',
            'guest_whatsapp' => '08123456789',
            'idempotency_key' => 'test-key-001',
        ]);

        $booking = $result['booking'];
        $rawToken = $result['raw_token'];

        $this->assertNotNull($booking->id);
        $this->assertEquals('Budi Santoso', $booking->guest_name);
        $this->assertNull($booking->user_id);
        $this->assertEquals(BookingStatus::PendingPayment, $booking->status);
        $this->assertEquals(2, $booking->nights);
        $this->assertEquals(500000, $booking->total_amount);
        $this->assertNotEmpty($rawToken);
        $this->assertNotNull($booking->guest_access_token_hash);
        $this->assertNotNull($booking->payment_expires_at);
        $this->assertStringStartsWith('BKG-', $booking->booking_code);
    }

    public function test_create_member_booking(): void
    {
        $user = User::factory()->create();
        $service = app(BookingService::class);

        $result = $service->createMemberBooking([
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(6)->toDateString(),
            'guest_count' => 1,
            'guest_name' => $user->name,
            'guest_email' => $user->email,
            'guest_whatsapp' => '08123456789',
            'idempotency_key' => 'member-key-001',
        ], $user);

        $this->assertEquals($user->id, $result['booking']->user_id);
    }

    public function test_idempotency_returns_same_booking(): void
    {
        $service = app(BookingService::class);

        $data = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guest_count' => 1,
            'guest_name' => 'Idem Test',
            'guest_whatsapp' => '08111111111',
            'idempotency_key' => 'idem-key-unique',
        ];

        $result1 = $service->createGuestBooking($data);
        $result2 = $service->createGuestBooking($data);

        $this->assertEquals($result1['booking']->id, $result2['booking']->id);
        // Only one booking should exist for same intent
        $this->assertEquals(1, Booking::where('id', $result1['booking']->id)->count());
    }

    public function test_double_booking_same_room_rejected(): void
    {
        $service = app(BookingService::class);

        $data = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(15)->toDateString(),
            'check_out' => now()->addDays(17)->toDateString(),
            'guest_count' => 1,
            'guest_name' => 'Tamu 1',
            'guest_whatsapp' => '08111111111',
            'idempotency_key' => 'first-booking',
        ];

        // First booking succeeds
        $service->createGuestBooking($data);

        // Second booking for same dates should fail (only 1 room available)
        $this->expectException(\App\Exceptions\RoomNotAvailableException::class);

        $service->createGuestBooking([
            ...$data,
            'guest_name' => 'Tamu 2',
            'idempotency_key' => 'second-booking',
        ]);
    }

    public function test_booking_creates_status_history(): void
    {
        $service = app(BookingService::class);

        $result = $service->createGuestBooking([
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(20)->toDateString(),
            'check_out' => now()->addDays(21)->toDateString(),
            'guest_count' => 1,
            'guest_name' => 'History Test',
            'guest_whatsapp' => '08222222222',
            'idempotency_key' => 'history-key',
        ]);

        $histories = $result['booking']->statusHistories;
        $this->assertCount(1, $histories);
        $this->assertEquals(BookingStatus::PendingPayment->value, $histories->first()->to_status);
    }

    public function test_checkout_page_renders(): void
    {
        $response = $this->get(route('booking.checkout', [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Checkout');
        $response->assertSee('Twin');
    }
}
