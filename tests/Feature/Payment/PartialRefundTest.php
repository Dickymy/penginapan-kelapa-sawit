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
use Tests\TestCase;

class PartialRefundTest extends TestCase
{
    use RefreshDatabase;

    private Booking $booking;
    private Payment $payment;

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

        $room = Room::create([
            'room_type_id' => $roomType->id,
            'code' => 'TWIN-01',
            'name' => 'Twin 01',
            'status' => 'active',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->booking = Booking::create([
            'booking_code' => 'BKG-REFUND01',
            'room_id' => $room->id,
            'source' => 'website',
            'status' => BookingStatus::Confirmed->value,
            'payment_status' => PaymentStatus::Paid->value,
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'nights' => 2,
            'guest_count' => 1,
            'guest_name' => 'Refund Test',
            'guest_email' => 'refund@example.com',
            'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin',
            'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000,
            'total_amount' => 500000,
        ]);

        $this->payment = Payment::create([
            'booking_id' => $this->booking->id,
            'provider' => 'midtrans',
            'provider_order_id' => 'BKG-REFUND01-1',
            'attempt_no' => 1,
            'gross_amount' => 500000,
            'status' => PaymentStatus::Paid->value,
            'paid_at' => now(),
        ]);
    }

    public function test_full_refund_maps_to_refunded(): void
    {
        $service = app(MidtransPaymentService::class);

        $status = $service->mapProviderStatus([
            'transaction_status' => 'refund',
        ]);

        $this->assertEquals(PaymentStatus::Refunded, $status);
    }

    public function test_partial_refund_maps_to_partial_refund(): void
    {
        $service = app(MidtransPaymentService::class);

        $status = $service->mapProviderStatus([
            'transaction_status' => 'partial_refund',
        ]);

        $this->assertEquals(PaymentStatus::PartialRefund, $status);
    }

    public function test_partial_and_full_refund_are_different_statuses(): void
    {
        $service = app(MidtransPaymentService::class);

        $full = $service->mapProviderStatus(['transaction_status' => 'refund']);
        $partial = $service->mapProviderStatus(['transaction_status' => 'partial_refund']);

        $this->assertNotEquals($full, $partial);
        $this->assertEquals(PaymentStatus::Refunded, $full);
        $this->assertEquals(PaymentStatus::PartialRefund, $partial);
    }
}
