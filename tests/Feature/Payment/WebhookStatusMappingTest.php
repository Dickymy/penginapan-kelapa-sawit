<?php

namespace Tests\Feature\Payment;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookStatusMappingTest extends TestCase
{
    use RefreshDatabase;

    private Booking $booking;
    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        config(['midtrans.server_key' => 'test-server-key']);

        $roomType = RoomType::create([
            'name' => 'Twin', 'slug' => 'twin', 'capacity' => 2,
            'bed_count' => 2, 'base_price' => 250000, 'is_active' => true, 'sort_order' => 1,
        ]);

        $room = Room::create([
            'room_type_id' => $roomType->id, 'code' => 'TWIN-01', 'name' => 'Twin 01',
            'status' => 'active', 'is_active' => true, 'sort_order' => 1,
        ]);

        $this->booking = Booking::create([
            'booking_code' => 'BKG-STATUS-0001',
            'room_id' => $room->id,
            'status' => BookingStatus::PendingPayment->value,
            'payment_status' => PaymentStatus::Unpaid->value,
            'source' => 'website',
            'check_in' => '2026-08-10', 'check_out' => '2026-08-12', 'nights' => 2,
            'guest_name' => 'Status Test', 'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin', 'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000, 'total_amount' => 500000,
            'payment_expires_at' => now()->addMinutes(30),
        ]);

        $this->payment = Payment::create([
            'booking_id' => $this->booking->id,
            'provider' => 'midtrans',
            'provider_order_id' => 'BKG-STATUS-0001-1',
            'attempt_no' => 1,
            'gross_amount' => 500000,
            'status' => PaymentStatus::Unpaid->value,
            'snap_token' => 'test-snap-token',
        ]);
    }

    private function makePayload(string $transactionStatus, string $statusCode = '200'): array
    {
        $orderId = $this->payment->provider_order_id;
        $grossAmount = '500000.00';
        $serverKey = config('midtrans.server_key');
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return [
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_id' => 'midtrans-txn-status-test',
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];
    }

    public function test_deny_webhook_sets_payment_failed(): void
    {
        $payload = $this->makePayload('deny', '202');

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->booking->refresh();

        $this->assertEquals(PaymentStatus::Failed, $this->payment->status);
        // Booking should still be pending (not confirmed)
        $this->assertEquals(BookingStatus::PendingPayment, $this->booking->status);
    }

    public function test_cancel_webhook_sets_payment_failed(): void
    {
        $payload = $this->makePayload('cancel', '202');

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::Failed, $this->payment->status);
    }

    public function test_expire_webhook_sets_payment_expired(): void
    {
        $payload = $this->makePayload('expire', '202');

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::Expired, $this->payment->status);
    }

    public function test_capture_with_accept_fraud_sets_paid(): void
    {
        $payload = $this->makePayload('capture');
        $payload['fraud_status'] = 'accept';

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->booking->refresh();

        $this->assertEquals(PaymentStatus::Paid, $this->payment->status);
        $this->assertEquals(BookingStatus::Confirmed, $this->booking->status);
    }

    public function test_capture_with_challenge_fraud_sets_failed(): void
    {
        $payload = $this->makePayload('capture');
        $payload['fraud_status'] = 'challenge';

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::Failed, $this->payment->status);
    }

    public function test_settlement_confirms_booking(): void
    {
        $payload = $this->makePayload('settlement');

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->booking->refresh();

        $this->assertEquals(PaymentStatus::Paid, $this->payment->status);
        $this->assertEquals(BookingStatus::Confirmed, $this->booking->status);
    }

    public function test_unknown_order_id_is_handled_gracefully(): void
    {
        $orderId = 'NONEXISTENT-ORDER';
        $grossAmount = '500000.00';
        $serverKey = config('midtrans.server_key');
        $signature = hash('sha512', $orderId . '200' . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_id' => 'midtrans-unknown',
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200); // Still returns 200 (don't cause Midtrans retries)

        // Original payment unchanged
        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::Unpaid, $this->payment->status);
    }

    public function test_paid_payment_cannot_be_downgraded_to_pending(): void
    {
        // Set payment to paid first
        $this->payment->update(['status' => PaymentStatus::Paid->value]);
        $this->booking->update([
            'status' => BookingStatus::Confirmed->value,
            'payment_status' => PaymentStatus::Paid->value,
        ]);

        // Send pending webhook (should be ignored)
        $payload = $this->makePayload('pending', '201');

        $response = $this->postJson(route('webhook.midtrans'), $payload);
        $response->assertStatus(200);

        $this->payment->refresh();
        $this->assertEquals(PaymentStatus::Paid, $this->payment->status); // Still paid
    }
}
