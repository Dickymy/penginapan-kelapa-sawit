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

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    private Booking $booking;
    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $roomType = RoomType::create([
            'name' => 'Twin', 'slug' => 'twin', 'capacity' => 2,
            'bed_count' => 2, 'base_price' => 250000, 'is_active' => true, 'sort_order' => 1,
        ]);

        $room = Room::create([
            'room_type_id' => $roomType->id, 'code' => 'TWIN-01', 'name' => 'Twin 01',
            'status' => 'active', 'is_active' => true, 'sort_order' => 1,
        ]);

        $this->booking = Booking::create([
            'booking_code' => 'BKG-202607-0001',
            'room_id' => $room->id,
            'status' => BookingStatus::PendingPayment->value,
            'payment_status' => PaymentStatus::Unpaid->value,
            'source' => 'website',
            'check_in' => '2026-08-01', 'check_out' => '2026-08-03', 'nights' => 2,
            'guest_name' => 'Test Guest', 'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin', 'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000, 'total_amount' => 500000,
            'payment_expires_at' => now()->addMinutes(30),
        ]);

        $this->payment = Payment::create([
            'booking_id' => $this->booking->id,
            'provider' => 'midtrans',
            'provider_order_id' => 'BKG-202607-0001-1',
            'attempt_no' => 1,
            'gross_amount' => 500000,
            'status' => PaymentStatus::Unpaid->value,
            'snap_token' => 'test-snap-token',
        ]);
    }

    private function makeWebhookPayload(string $status = 'settlement', ?string $orderId = null, ?string $grossAmount = null): array
    {
        $orderId = $orderId ?? $this->payment->provider_order_id;
        $grossAmount = $grossAmount ?? '500000.00';
        $serverKey = config('midtrans.server_key') ?: 'test-server-key';
        $statusCode = '200';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return [
            'order_id' => $orderId,
            'transaction_status' => $status,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_id' => 'midtrans-txn-001',
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];
    }

    public function test_valid_webhook_confirms_booking(): void
    {
        // Set server key for signature verification
        config(['midtrans.server_key' => 'test-server-key']);

        $payload = $this->makeWebhookPayload('settlement');

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->booking->refresh();

        $this->assertEquals(PaymentStatus::Paid, $this->payment->status);
        $this->assertEquals(BookingStatus::Confirmed, $this->booking->status);
    }

    public function test_invalid_signature_does_not_process(): void
    {
        config(['midtrans.server_key' => 'test-server-key']);

        $payload = $this->makeWebhookPayload('settlement');
        $payload['signature_key'] = 'invalid-signature';

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200); // Still returns 200

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::Unpaid, $this->payment->status);
    }

    public function test_amount_mismatch_does_not_process(): void
    {
        config(['midtrans.server_key' => 'test-server-key']);

        // Different gross amount
        $payload = $this->makeWebhookPayload('settlement', null, '999999.00');

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::Unpaid, $this->payment->status);
    }

    public function test_duplicate_webhook_is_idempotent(): void
    {
        config(['midtrans.server_key' => 'test-server-key']);

        $payload = $this->makeWebhookPayload('settlement');

        // First call
        $this->postJson(route('webhook.midtrans'), $payload);

        // Second call (duplicate)
        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        // Still only one booking transition
        $this->assertEquals(1, $this->booking->statusHistories()->where('to_status', 'confirmed')->count());
    }

    public function test_late_payment_sets_needs_attention(): void
    {
        config(['midtrans.server_key' => 'test-server-key']);

        // Expire the booking first
        $this->booking->update(['status' => BookingStatus::Expired->value]);

        $payload = $this->makeWebhookPayload('settlement');

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->booking->refresh();

        $this->assertEquals(PaymentStatus::Paid, $this->payment->status);
        $this->assertEquals(BookingStatus::Expired, $this->booking->status); // NOT changed to confirmed
        $this->assertTrue($this->booking->needs_attention);
        $this->assertEquals('late_payment_after_booking_expired', $this->booking->attention_reason);
    }

    public function test_pending_status_webhook(): void
    {
        config(['midtrans.server_key' => 'test-server-key']);

        $payload = $this->makeWebhookPayload('pending');
        // Adjust status_code for pending
        $payload['status_code'] = '201';
        $orderId = $payload['order_id'];
        $grossAmount = $payload['gross_amount'];
        $serverKey = 'test-server-key';
        $payload['signature_key'] = hash('sha512', $orderId . '201' . $grossAmount . $serverKey);

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::Pending, $this->payment->status);
    }
}
