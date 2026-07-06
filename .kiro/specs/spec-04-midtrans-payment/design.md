# SPEC 04 — Midtrans Payment: Design

> **Referensi:** requirements.md SPEC 04, Master Requirements Fase 6

---

## 1. Database — `payments` table

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    provider VARCHAR(30) NOT NULL DEFAULT 'midtrans',
    provider_order_id VARCHAR(100) NOT NULL,
    transaction_id VARCHAR(191) NULL,
    attempt_no SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    snap_token TEXT NULL,
    payment_type VARCHAR(100) NULL,
    gross_amount BIGINT UNSIGNED NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
    provider_transaction_status VARCHAR(50) NULL,
    fraud_status VARCHAR(50) NULL,
    provider_transaction_time TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    expired_at TIMESTAMP NULL,
    refunded_at TIMESTAMP NULL,
    raw_response JSON NULL,
    last_status_checked_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,

    UNIQUE KEY (provider, provider_order_id),
    UNIQUE KEY (booking_id, attempt_no),
    INDEX (booking_id, status),
    INDEX (transaction_id),
    INDEX (status, created_at),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
```

## 2. Database — `payment_webhook_events` table

```sql
CREATE TABLE payment_webhook_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider VARCHAR(30) NOT NULL DEFAULT 'midtrans',
    deduplication_key CHAR(64) NOT NULL,
    provider_order_id VARCHAR(100) NULL,
    transaction_id VARCHAR(191) NULL,
    event_status VARCHAR(50) NULL,
    signature_valid BOOLEAN NOT NULL DEFAULT FALSE,
    amount_valid BOOLEAN NOT NULL DEFAULT FALSE,
    processing_status VARCHAR(30) NOT NULL DEFAULT 'received',
    payload JSON NOT NULL,
    error_message TEXT NULL,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,

    UNIQUE KEY (provider, deduplication_key),
    INDEX (provider_order_id, created_at),
    INDEX (processing_status, created_at)
) ENGINE=InnoDB;
```

---

## 3. MidtransPaymentService

```php
class MidtransPaymentService
{
    // Create or resume payment for booking
    public function createOrResumePayment(Booking $booking): array;
    
    // Handle webhook notification
    public function handleWebhook(array $payload): void;
    
    // Verify signature
    public function verifySignature(array $payload): bool;
    
    // Map provider status to app PaymentStatus
    public function mapProviderStatus(array $payload): PaymentStatus;
    
    // Fetch status from Midtrans (reconciliation)
    public function fetchStatus(string $providerOrderId): array;
    
    // Reconcile a specific payment
    public function reconcilePayment(Payment $payment): void;
}
```

### Payment Creation Flow

1. Verify booking is `pending_payment` and hold not expired
2. Find existing usable payment attempt (has snap_token, not expired/failed)
3. If found → return existing snap_token + client_key
4. If not → create new attempt:
   - `provider_order_id = {booking_code}-{attempt_no}`
   - `gross_amount = booking.total_amount`
   - Call Midtrans Snap API → get token
   - Save snap_token
   - Return token + client_key

### Webhook Flow

1. Receive JSON payload
2. Generate dedup key: `sha256(order_id + status + gross_amount)`
3. Check dedup → if already processed, return 200
4. Verify signature
5. Find payment by `provider_order_id`
6. Verify amount matches
7. Save webhook event
8. Lock payment + booking
9. Map status → update payment
10. If paid and booking pending → confirm booking
11. If paid and booking expired → set needs_attention
12. Commit, return 200

### Signature Verification (Midtrans)

```php
$serverKey = config('midtrans.server_key');
$orderId = $payload['order_id'];
$statusCode = $payload['status_code'];
$grossAmount = $payload['gross_amount'];
$expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
return hash_equals($expectedSignature, $payload['signature_key']);
```

---

## 4. Models

### Payment Model
- Casts: status → PaymentStatus, gross_amount → integer
- Relations: belongsTo(Booking)
- Methods: isPaid(), isExpired()

### PaymentWebhookEvent Model
- Fillable: all columns
- Casts: payload → array, signature_valid/amount_valid → boolean

---

## 5. Controllers

### Public\PaymentController
- `pay(bookingCode)` — create/resume payment, return view with Snap.js
- `finish(bookingCode)` — redirect after Snap (informational only, not authoritative)

### Webhook\MidtransWebhookController
- `handle(Request)` — receive webhook, process via service

---

## 6. Routes

```php
// Payment (public, needs booking access)
Route::get('/booking/{bookingCode}/bayar', [PaymentController::class, 'pay'])->name('booking.pay');
Route::get('/booking/{bookingCode}/selesai', [PaymentController::class, 'finish'])->name('booking.finish');

// Webhook (no CSRF, no session)
Route::post('/webhook/midtrans', [MidtransWebhookController::class, 'handle'])
    ->name('webhook.midtrans')
    ->withoutMiddleware(['web']);
```

---

## 7. Artisan Command

### `payment:reconcile`
- Query payments: status pending, created > 10 minutes ago, no recent check
- For each: fetch status from Midtrans, process through same mapping
- Idempotent
- Schedule: every 5 minutes

---

## 8. Frontend (Snap.js)

```html
<!-- Payment page -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
<script>
    snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            window.location.href = '{{ route("booking.finish", $bookingCode) }}';
        },
        onPending: function(result) {
            window.location.href = '{{ route("booking.finish", $bookingCode) }}';
        },
        onError: function(result) {
            window.location.href = '{{ route("booking.finish", $bookingCode) }}';
        },
        onClose: function() {
            // User closed without completing
        }
    });
</script>
```

**IMPORTANT:** These callbacks only redirect. They do NOT change payment status.

---

## 9. Test Strategy

| Test | Type | Key Assertions |
|---|---|---|
| Create payment attempt | Feature | Payment created, snap_token saved |
| Resume existing payment | Feature | Same token returned |
| Webhook valid signature | Feature | Payment → paid, booking → confirmed |
| Webhook invalid signature | Feature | Rejected, 200 returned |
| Webhook amount mismatch | Feature | Logged as invalid, not processed |
| Webhook duplicate | Feature | Idempotent, 200 |
| Late payment (booking expired) | Feature | Payment paid, booking needs_attention |
| Reconcile command | Feature | Pending payment checked and updated |
| JS callback does NOT change status | Design | No endpoint for frontend status change |
