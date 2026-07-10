<?php

namespace Tests\Feature\Payment;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\MidtransPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SnapTokenTest extends TestCase
{
    use RefreshDatabase;

    private RoomType $roomType;
    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roomType = RoomType::create([
            'name' => 'Twin', 'slug' => 'twin', 'capacity' => 2,
            'bed_count' => 2, 'base_price' => 250000, 'is_active' => true, 'sort_order' => 1,
        ]);

        $this->room = Room::create([
            'room_type_id' => $this->roomType->id, 'code' => 'TWIN-01', 'name' => 'Twin 01',
            'status' => 'active', 'is_active' => true, 'sort_order' => 1,
        ]);
    }

    private function createBooking(array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'booking_code' => 'BKG-SNAP-' . uniqid(),
            'room_id' => $this->room->id,
            'source' => 'website',
            'status' => BookingStatus::PendingPayment->value,
            'payment_status' => PaymentStatus::Unpaid->value,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'nights' => 2,
            'guest_count' => 1,
            'guest_name' => 'Snap Test',
            'guest_email' => 'snap@example.com',
            'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin',
            'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000,
            'total_amount' => 500000,
            'payment_expires_at' => now()->addMinutes(30),
        ], $overrides));
    }

    public function test_create_payment_rejects_non_pending_booking(): void
    {
        $booking = $this->createBooking([
            'status' => BookingStatus::Confirmed->value,
        ]);

        $service = app(MidtransPaymentService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Booking tidak dalam status menunggu pembayaran.');

        $service->createOrResumePayment($booking);
    }

    public function test_create_payment_rejects_expired_booking(): void
    {
        $booking = $this->createBooking([
            'payment_expires_at' => now()->subMinutes(5),
        ]);

        $service = app(MidtransPaymentService::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Batas waktu pembayaran telah berakhir.');

        $service->createOrResumePayment($booking);
    }

    public function test_create_payment_reuses_existing_snap_token(): void
    {
        $booking = $this->createBooking();

        // Create an existing usable payment
        Payment::create([
            'booking_id' => $booking->id,
            'provider' => 'midtrans',
            'provider_order_id' => $booking->booking_code . '-1',
            'attempt_no' => 1,
            'gross_amount' => 500000,
            'status' => PaymentStatus::Unpaid->value,
            'snap_token' => 'existing-snap-token-123',
        ]);

        $service = app(MidtransPaymentService::class);
        $result = $service->createOrResumePayment($booking);

        $this->assertEquals('existing-snap-token-123', $result['snap_token']);
        $this->assertNotEmpty($result['client_key']);
    }

    public function test_create_payment_skips_expired_payment_attempt(): void
    {
        $booking = $this->createBooking();

        // Create an expired payment (should be skipped)
        Payment::create([
            'booking_id' => $booking->id,
            'provider' => 'midtrans',
            'provider_order_id' => $booking->booking_code . '-1',
            'attempt_no' => 1,
            'gross_amount' => 500000,
            'status' => PaymentStatus::Expired->value,
            'snap_token' => 'expired-snap-token',
        ]);

        // Use a partial mock of the service to intercept only the Snap API call
        $service = Mockery::mock(MidtransPaymentService::class)->makePartial();
        $service->shouldAllowMockingProtectedMethods();

        // Override getSnapTokenFromProvider
        $service->shouldReceive('getSnapTokenFromProvider')
            ->once()
            ->andReturn('new-snap-token-456');

        $result = $service->createOrResumePayment($booking);

        $this->assertEquals('new-snap-token-456', $result['snap_token']);
        $this->assertEquals(2, $result['payment']->attempt_no);
    }

    public function test_create_payment_does_not_create_for_already_paid(): void
    {
        $booking = $this->createBooking([
            'status' => BookingStatus::Confirmed->value,
            'payment_status' => PaymentStatus::Paid->value,
        ]);

        $service = app(MidtransPaymentService::class);

        $this->expectException(\RuntimeException::class);

        $service->createOrResumePayment($booking);
    }

    public function test_gross_amount_matches_booking_total(): void
    {
        $booking = $this->createBooking(['total_amount' => 350000]);

        // Use a partial mock to intercept the Snap API call
        $service = Mockery::mock(MidtransPaymentService::class)->makePartial();
        $service->shouldAllowMockingProtectedMethods();

        $service->shouldReceive('getSnapTokenFromProvider')
            ->once()
            ->andReturn('token-for-350k');

        $result = $service->createOrResumePayment($booking);

        $this->assertEquals(350000, $result['payment']->gross_amount);
    }
}
