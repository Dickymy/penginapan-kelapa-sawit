<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Payment;
use App\Models\PaymentWebhookEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;

class MidtransPaymentService
{
    public function __construct()
    {
        MidtransConfig::$serverKey = config('midtrans.server_key');
        MidtransConfig::$clientKey = config('midtrans.client_key');
        MidtransConfig::$isProduction = config('midtrans.is_production');
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create or resume payment for a booking.
     * Returns ['snap_token' => ..., 'client_key' => ..., 'payment' => ...]
     */
    public function createOrResumePayment(Booking $booking): array
    {
        // Verify booking is still payable
        if ($booking->status !== BookingStatus::PendingPayment) {
            throw new \RuntimeException('Booking tidak dalam status menunggu pembayaran.');
        }

        if ($booking->payment_expires_at && $booking->payment_expires_at->isPast()) {
            throw new \RuntimeException('Batas waktu pembayaran telah berlewat.');
        }

        // Find existing usable payment attempt
        $existingPayment = $booking->payments()
            ->whereNotIn('status', [PaymentStatus::Expired->value, PaymentStatus::Failed->value])
            ->whereNotNull('snap_token')
            ->latest()
            ->first();

        if ($existingPayment) {
            return [
                'snap_token' => $existingPayment->snap_token,
                'client_key' => config('midtrans.client_key'),
                'payment' => $existingPayment,
            ];
        }

        // Create new payment attempt
        $attemptNo = ($booking->payments()->max('attempt_no') ?? 0) + 1;
        $providerOrderId = "{$booking->booking_code}-{$attemptNo}";

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'provider' => 'midtrans',
            'provider_order_id' => $providerOrderId,
            'attempt_no' => $attemptNo,
            'gross_amount' => $booking->total_amount,
            'status' => PaymentStatus::Unpaid->value,
        ]);

        // Call Midtrans Snap API
        $itemDetails = [
            [
                'id' => $booking->room_id,
                'price' => $booking->price_per_night_snapshot,
                'quantity' => $booking->nights,
                'name' => substr($booking->room_type_name_snapshot . ' - ' . $booking->room_name_snapshot, 0, 50),
            ],
        ];

        // Add discount as negative item to ensure item_details sum = gross_amount
        $totalDiscount = $booking->promotion_discount + $booking->points_discount;
        if ($totalDiscount > 0) {
            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => -$totalDiscount,
                'quantity' => 1,
                'name' => 'Diskon',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $providerOrderId,
                'gross_amount' => $booking->total_amount,
            ],
            'customer_details' => [
                'first_name' => $booking->guest_name,
                'email' => $booking->guest_email ?: null,
                'phone' => $booking->guest_whatsapp,
            ],
            'item_details' => $itemDetails,
        ];

        try {
            $snapToken = $this->getSnapTokenFromProvider($params);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap API error', [
                'booking_code' => $booking->booking_code,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Gagal membuat transaksi pembayaran. Silakan coba lagi.');
        }

        $payment->update(['snap_token' => $snapToken]);

        return [
            'snap_token' => $snapToken,
            'client_key' => config('midtrans.client_key'),
            'payment' => $payment,
        ];
    }

    /**
     * Call Midtrans Snap API. Extracted for testability.
     */
    public function getSnapTokenFromProvider(array $params): string
    {
        return Snap::getSnapToken($params);
    }

    /**
     * Handle incoming webhook notification from Midtrans.
     */
    public function handleWebhook(array $payload): void
    {
        $orderId = $payload['order_id'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '0';
        $transactionId = $payload['transaction_id'] ?? null;

        // Generate deduplication key
        $dedupKey = hash('sha256', $orderId . $transactionStatus . $grossAmount);

        // Check if already processed
        $existing = PaymentWebhookEvent::where('provider', 'midtrans')
            ->where('deduplication_key', $dedupKey)
            ->first();

        if ($existing && $existing->processing_status === 'processed') {
            return; // Already handled
        }

        // Verify signature
        $signatureValid = $this->verifySignature($payload);

        // Find payment
        $payment = Payment::where('provider_order_id', $orderId)->first();

        // Verify amount (Midtrans sends "500000.00" as string)
        $amountValid = false;
        if ($payment) {
            $amountValid = (int) round((float) $grossAmount) === $payment->gross_amount;
        }

        // Save webhook event
        $event = PaymentWebhookEvent::updateOrCreate(
            ['provider' => 'midtrans', 'deduplication_key' => $dedupKey],
            [
                'provider_order_id' => $orderId,
                'transaction_id' => $transactionId,
                'event_status' => $transactionStatus,
                'signature_valid' => $signatureValid,
                'amount_valid' => $amountValid,
                'payload' => $payload,
                'processing_status' => 'received',
            ]
        );

        // Reject if signature invalid
        if (! $signatureValid) {
            $event->update([
                'processing_status' => 'failed',
                'error_message' => 'Invalid signature',
            ]);
            return;
        }

        // Reject if payment not found
        if (! $payment) {
            $event->update([
                'processing_status' => 'ignored',
                'error_message' => 'Payment not found for order_id: ' . $orderId,
            ]);
            return;
        }

        // Reject if amount mismatch
        if (! $amountValid) {
            $event->update([
                'processing_status' => 'failed',
                'error_message' => 'Amount mismatch: expected ' . $payment->gross_amount . ', got ' . $grossAmount,
            ]);
            return;
        }

        // Process the status change
        $this->processPaymentStatus($payment, $payload, $event);
    }

    /**
     * Verify Midtrans notification signature.
     */
    public function verifySignature(array $payload): bool
    {
        $serverKey = config('midtrans.server_key');

        if (empty($serverKey)) {
            return false;
        }

        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    /**
     * Map Midtrans transaction_status to PaymentStatus.
     */
    public function mapProviderStatus(array $payload): PaymentStatus
    {
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        return match ($transactionStatus) {
            'capture' => $fraudStatus === 'accept' ? PaymentStatus::Paid : PaymentStatus::Failed,
            'settlement' => PaymentStatus::Paid,
            'pending' => PaymentStatus::Pending,
            'deny', 'cancel' => PaymentStatus::Failed,
            'expire' => PaymentStatus::Expired,
            'refund' => PaymentStatus::Refunded,
            'partial_refund' => PaymentStatus::PartialRefund,
            default => PaymentStatus::Pending,
        };
    }

    /**
     * Fetch payment status from Midtrans (server-to-server).
     */
    public function fetchStatus(string $providerOrderId): ?array
    {
        try {
            $response = MidtransTransaction::status($providerOrderId);
            return (array) $response;
        } catch (\Exception $e) {
            Log::warning('Midtrans status check failed', [
                'order_id' => $providerOrderId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Reconcile a payment by fetching status from Midtrans.
     */
    public function reconcilePayment(Payment $payment): void
    {
        $statusData = $this->fetchStatus($payment->provider_order_id);

        if (! $statusData) {
            $payment->update(['last_status_checked_at' => now()]);
            return;
        }

        // Process as if it were a webhook
        $this->processPaymentStatus($payment, $statusData, null);
        $payment->update(['last_status_checked_at' => now()]);
    }

    /**
     * Process payment status change.
     */
    private function processPaymentStatus(Payment $payment, array $payload, ?PaymentWebhookEvent $event): void
    {
        $newStatus = $this->mapProviderStatus($payload);

        DB::transaction(function () use ($payment, $payload, $newStatus, $event) {
            // Lock payment and booking
            $payment = Payment::where('id', $payment->id)->lockForUpdate()->first();
            $booking = Booking::where('id', $payment->booking_id)->lockForUpdate()->first();

            // Don't downgrade from paid to lesser status
            if ($payment->status === PaymentStatus::Paid && $newStatus !== PaymentStatus::Refunded) {
                $event?->update(['processing_status' => 'ignored', 'processed_at' => now()]);
                return;
            }

            // Update payment
            $payment->update([
                'status' => $newStatus->value,
                'transaction_id' => $payload['transaction_id'] ?? $payment->transaction_id,
                'payment_type' => $payload['payment_type'] ?? $payment->payment_type,
                'provider_transaction_status' => $payload['transaction_status'] ?? null,
                'fraud_status' => $payload['fraud_status'] ?? null,
                'provider_transaction_time' => isset($payload['transaction_time']) ? $payload['transaction_time'] : null,
                'paid_at' => $newStatus === PaymentStatus::Paid ? now() : $payment->paid_at,
                'expired_at' => $newStatus === PaymentStatus::Expired ? now() : $payment->expired_at,
            ]);

            // Update booking based on payment status
            if ($newStatus === PaymentStatus::Paid) {
                $this->handlePaymentPaid($booking, $payment);
            }

            // Update booking payment_status
            $booking->update(['payment_status' => $newStatus->value]);

            $event?->update(['processing_status' => 'processed', 'processed_at' => now()]);
        });
    }

    /**
     * Handle booking transition when payment is confirmed paid.
     */
    private function handlePaymentPaid(Booking $booking, Payment $payment): void
    {
        if ($booking->status === BookingStatus::PendingPayment) {
            // Normal flow: confirm booking
            $booking->update(['status' => BookingStatus::Confirmed->value]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'from_status' => BookingStatus::PendingPayment->value,
                'to_status' => BookingStatus::Confirmed->value,
                'reason' => 'Pembayaran berhasil diverifikasi',
                'actor_type' => 'system',
                'created_at' => now(),
            ]);
        } elseif ($booking->status === BookingStatus::Expired) {
            // Late payment: don't auto-confirm, flag for admin
            $booking->update([
                'needs_attention' => true,
                'attention_reason' => 'late_payment_after_booking_expired',
            ]);
        }
    }
}
