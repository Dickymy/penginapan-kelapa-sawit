<?php

namespace Tests\Feature\Booking;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualBookingExpiryTest extends TestCase
{
    use RefreshDatabase;

    private RoomType $roomType;
    private Room $room;
    private Admin $admin;

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

        $this->admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    private function baseData(array $overrides = []): array
    {
        return array_merge([
            'room_id' => $this->room->id,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guest_count' => 1,
            'guest_name' => 'Manual Guest',
            'guest_whatsapp' => '08123456789',
            'price_per_night' => 250000,
        ], $overrides);
    }

    public function test_ota_booking_confirmed_without_expiry(): void
    {
        $service = app(BookingService::class);

        $booking = $service->createManualBooking(
            $this->baseData([
                'source' => BookingSource::BookingCom->value,
                'payment_status' => 'unpaid',
            ]),
            $this->admin
        );

        // OTA booking should be confirmed (not pending_payment)
        $this->assertEquals(BookingStatus::Confirmed->value, $booking->status->value);
        // No auto-expiry
        $this->assertNull($booking->payment_expires_at);
    }

    public function test_agoda_booking_confirmed_without_expiry(): void
    {
        $service = app(BookingService::class);

        $booking = $service->createManualBooking(
            $this->baseData([
                'source' => BookingSource::Agoda->value,
                'payment_status' => 'unpaid',
            ]),
            $this->admin
        );

        $this->assertEquals(BookingStatus::Confirmed->value, $booking->status->value);
        $this->assertNull($booking->payment_expires_at);
    }

    public function test_traveloka_booking_confirmed_without_expiry(): void
    {
        $service = app(BookingService::class);

        $booking = $service->createManualBooking(
            $this->baseData([
                'source' => BookingSource::Traveloka->value,
                'payment_status' => 'unpaid',
            ]),
            $this->admin
        );

        $this->assertEquals(BookingStatus::Confirmed->value, $booking->status->value);
        $this->assertNull($booking->payment_expires_at);
    }

    public function test_walkin_booking_confirmed_without_expiry(): void
    {
        $service = app(BookingService::class);

        $booking = $service->createManualBooking(
            $this->baseData([
                'source' => BookingSource::WalkIn->value,
                'payment_status' => 'unpaid',
            ]),
            $this->admin
        );

        $this->assertEquals(BookingStatus::Confirmed->value, $booking->status->value);
        $this->assertNull($booking->payment_expires_at);
    }

    public function test_whatsapp_booking_gets_24h_hold(): void
    {
        $service = app(BookingService::class);

        $booking = $service->createManualBooking(
            $this->baseData([
                'source' => BookingSource::Whatsapp->value,
                'payment_status' => 'unpaid',
            ]),
            $this->admin
        );

        $this->assertEquals(BookingStatus::PendingPayment->value, $booking->status->value);
        $this->assertNotNull($booking->payment_expires_at);
        // Should expire in approximately 24 hours (not 30 minutes)
        $hoursUntilExpiry = now()->diffInHours($booking->payment_expires_at, false);
        $this->assertGreaterThanOrEqual(23, $hoursUntilExpiry,
            "WhatsApp booking should have ~24h hold, got {$hoursUntilExpiry}h"
        );
    }

    public function test_whatsapp_booking_custom_hold_minutes(): void
    {
        $service = app(BookingService::class);

        $booking = $service->createManualBooking(
            $this->baseData([
                'source' => BookingSource::Whatsapp->value,
                'payment_status' => 'unpaid',
                'hold_minutes' => 120,
            ]),
            $this->admin
        );

        $this->assertEquals(BookingStatus::PendingPayment->value, $booking->status->value);
        // Custom hold should be approximately 120 minutes
        $minutesUntilExpiry = now()->diffInMinutes($booking->payment_expires_at, false);
        $this->assertGreaterThanOrEqual(119, $minutesUntilExpiry,
            "Custom hold minutes should be respected, got {$minutesUntilExpiry}min"
        );
    }

    public function test_paid_manual_booking_confirmed_immediately(): void
    {
        $service = app(BookingService::class);

        $booking = $service->createManualBooking(
            $this->baseData([
                'source' => BookingSource::Whatsapp->value,
                'payment_status' => 'paid',
            ]),
            $this->admin
        );

        $this->assertEquals(BookingStatus::Confirmed->value, $booking->status->value);
        $this->assertNull($booking->payment_expires_at);
    }

    public function test_ota_booking_blocks_room(): void
    {
        $service = app(BookingService::class);

        // Create OTA booking (confirmed)
        $service->createManualBooking(
            $this->baseData([
                'source' => BookingSource::BookingCom->value,
                'payment_status' => 'unpaid',
            ]),
            $this->admin
        );

        // Second booking for same dates should fail
        $this->expectException(\App\Exceptions\RoomNotAvailableException::class);

        $service->createManualBooking(
            $this->baseData([
                'source' => BookingSource::Website->value,
                'payment_status' => 'unpaid',
            ]),
            $this->admin
        );
    }
}
