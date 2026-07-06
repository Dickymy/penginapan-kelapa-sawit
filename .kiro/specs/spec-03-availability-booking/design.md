# SPEC 03 — Availability & Guest Booking Engine: Design

> **Referensi:** requirements.md SPEC 03, Master Requirements Fase 4–5

---

## 1. New Database Tables

### 1.1 `bookings`

```sql
CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(40) NOT NULL UNIQUE,
    invoice_number VARCHAR(40) NULL UNIQUE,
    idempotency_key VARCHAR(100) NULL UNIQUE,
    user_id BIGINT UNSIGNED NULL,
    room_id BIGINT UNSIGNED NOT NULL,
    created_by_admin_id BIGINT UNSIGNED NULL,
    source VARCHAR(30) NOT NULL DEFAULT 'website',
    status VARCHAR(30) NOT NULL DEFAULT 'pending_payment',
    payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    nights SMALLINT UNSIGNED NOT NULL,
    guest_count SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    guest_name VARCHAR(150) NOT NULL,
    guest_email VARCHAR(191) NULL,
    guest_whatsapp VARCHAR(32) NOT NULL,
    arrival_estimate VARCHAR(100) NULL,
    special_request TEXT NULL,
    room_type_name_snapshot VARCHAR(120) NOT NULL,
    room_name_snapshot VARCHAR(120) NOT NULL,
    price_per_night_snapshot BIGINT UNSIGNED NOT NULL,
    subtotal BIGINT UNSIGNED NOT NULL,
    promotion_id BIGINT UNSIGNED NULL,
    promotion_code_snapshot VARCHAR(100) NULL,
    promotion_discount BIGINT UNSIGNED NOT NULL DEFAULT 0,
    points_redeemed BIGINT UNSIGNED NOT NULL DEFAULT 0,
    points_discount BIGINT UNSIGNED NOT NULL DEFAULT 0,
    total_amount BIGINT UNSIGNED NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'IDR',
    eligible_loyalty_amount BIGINT UNSIGNED NOT NULL DEFAULT 0,
    payment_expires_at TIMESTAMP NULL,
    policy_version_id BIGINT UNSIGNED NULL,
    policy_accepted_at TIMESTAMP NULL,
    guest_access_token_hash CHAR(64) NULL,
    claimed_at TIMESTAMP NULL,
    claim_method VARCHAR(50) NULL,
    checked_in_at TIMESTAMP NULL,
    checked_out_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    cancelled_at TIMESTAMP NULL,
    cancellation_reason VARCHAR(255) NULL,
    cancellation_notes TEXT NULL,
    cancelled_by_admin_id BIGINT UNSIGNED NULL,
    needs_attention BOOLEAN NOT NULL DEFAULT FALSE,
    attention_reason VARCHAR(191) NULL,
    internal_notes TEXT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,

    INDEX idx_room_dates (room_id, check_in, check_out, status),
    INDEX idx_expiry (status, payment_expires_at),
    INDEX idx_user (user_id, status, check_in),
    INDEX idx_source (source, created_at),
    INDEX idx_payment (payment_status, created_at),
    INDEX idx_attention (needs_attention, created_at),
    INDEX idx_guest_email (guest_email),
    INDEX idx_guest_whatsapp (guest_whatsapp),

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by_admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (policy_version_id) REFERENCES policy_versions(id) ON DELETE SET NULL,
    FOREIGN KEY (cancelled_by_admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

### 1.2 `booking_status_histories`

```sql
CREATE TABLE booking_status_histories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    from_status VARCHAR(30) NULL,
    to_status VARCHAR(30) NOT NULL,
    reason VARCHAR(255) NULL,
    actor_type VARCHAR(50) NOT NULL DEFAULT 'system',
    actor_id BIGINT UNSIGNED NULL,
    metadata JSON NULL,
    created_at TIMESTAMP NOT NULL,

    INDEX (booking_id, created_at),
    INDEX (to_status, created_at),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
```

### 1.3 `document_sequences`

```sql
CREATE TABLE document_sequences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    document_type VARCHAR(30) NOT NULL,
    period CHAR(6) NOT NULL,
    last_number BIGINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    UNIQUE KEY (document_type, period)
) ENGINE=InnoDB;
```

---

## 2. Services

### 2.1 AvailabilityService

```php
class AvailabilityService
{
    // Informational search (not authoritative)
    public function searchAvailableRoomTypes(Carbon $checkIn, Carbon $checkOut, int $guestCount): Collection;
    
    // Get available rooms for a specific type
    public function findAvailableRooms(int $roomTypeId, Carbon $checkIn, Carbon $checkOut): Collection;
    
    // Single room check (used in recheck)
    public function isRoomAvailable(int $roomId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null): bool;
    
    // Authoritative check that throws (used inside transaction)
    public function assertRoomAvailableForBooking(int $roomId, Carbon $checkIn, Carbon $checkOut): void;
}
```

**Overlap Query:**
```sql
SELECT id FROM bookings
WHERE room_id = ?
  AND check_in < ?   -- new check_out
  AND check_out > ?  -- new check_in
  AND (
    status IN ('confirmed', 'checked_in')
    OR (status = 'pending_payment' AND payment_expires_at > NOW())
  )
LIMIT 1
```

**Room Block Query:**
```sql
SELECT id FROM room_blocks
WHERE room_id = ?
  AND start_date < ?  -- new check_out
  AND end_date > ?    -- new check_in
LIMIT 1
```

### 2.2 PricingService

```php
class PricingService
{
    public function calculateQuote(RoomType $roomType, Carbon $checkIn, Carbon $checkOut): array;
    public function calculateNights(Carbon $checkIn, Carbon $checkOut): int;
}
```

Returns:
```php
[
    'nights' => 2,
    'price_per_night' => 350000,
    'subtotal' => 700000,
    'promotion_discount' => 0,
    'points_discount' => 0,
    'total_amount' => 700000,
    'eligible_loyalty_amount' => 700000,
]
```

### 2.3 BookingService

```php
class BookingService
{
    public function createGuestBooking(array $data): Booking;
    public function createMemberBooking(array $data, User $user): Booking;
    public function expirePendingBooking(Booking $booking): void;
}
```

**Create Booking Flow:**
1. Validate idempotency key → return existing if found
2. Begin DB transaction
3. Lock room row with `lockForUpdate`
4. Assert room available (overlap + block)
5. Calculate quote server-side
6. Generate booking code (sequence lock)
7. Generate guest access token (random → store hash)
8. Create booking with all snapshots
9. Write status history
10. Commit
11. Return booking with raw token (one-time display)

### 2.4 DocumentSequenceService

```php
class DocumentSequenceService
{
    public function generateBookingCode(): string;  // BKG-YYYYMM-0001
    public function generateInvoiceNumber(): string; // INV-YYYYMM-0001
}
```

---

## 3. Models

### Booking Model
- Casts: status → BookingStatus, payment_status → PaymentStatus, source → BookingSource
- Relations: belongsTo(User nullable), belongsTo(Room), hasMany(BookingStatusHistory)
- Accessors: formatted_total, is_expired, remaining_time

### BookingStatusHistory Model
- Fillable: booking_id, from_status, to_status, reason, actor_type, actor_id, metadata

### DocumentSequence Model
- Used internally by DocumentSequenceService

---

## 4. Controllers

### Public\AvailabilityController
- `search(Request)` — validate dates+guests, call AvailabilityService, show results

### Public\BookingController
- `showCheckout(Request)` — display checkout form with recheck + quote
- `store(StoreBookingRequest)` — create booking via BookingService
- `confirmation($bookingCode, $token)` — show confirmation page
- `status($bookingCode)` — verify access, show current status

---

## 5. Routes

```php
// Public booking
Route::get('/ketersediaan', [AvailabilityController::class, 'search'])->name('availability.search');
Route::get('/checkout', [BookingController::class, 'showCheckout'])->name('booking.checkout');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{bookingCode}/konfirmasi', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/booking/{bookingCode}/status', [BookingController::class, 'status'])->name('booking.status');
Route::post('/booking/{bookingCode}/verify', [BookingController::class, 'verifyAccess'])->name('booking.verify');
```

---

## 6. Artisan Command

### `booking:expire-pending`
- Runs every minute via scheduler
- Queries pending bookings past expiry
- Locks each, rechecks, transitions to expired
- Writes status history
- Idempotent

---

## 7. Exception Classes

- `RoomNotAvailableException` — room has conflict
- `BookingExpiredException` — booking past expiry
- `InvalidBookingDataException` — validation at service level

---

## 8. Test Strategy

| Test | Type | Key Assertions |
|---|---|---|
| Night calculation | Unit | check_out - check_in = nights |
| Overlap boundary | Unit | Half-open interval correctness |
| Pricing calculation | Unit | nights × rate = subtotal |
| Availability search | Feature | Only available types shown |
| Room block exclusion | Feature | Blocked rooms hidden |
| Pending hold exclusion | Feature | Held rooms hidden, expired not |
| Create guest booking | Feature | Booking created, code generated, token hashed |
| Create member booking | Feature | user_id filled |
| Idempotency | Feature | Same key → same booking returned |
| Double booking (sequential) | Feature | Second attempt fails |
| Booking expiry command | Feature | Expired bookings transition correctly |
| Confirmation page | Feature | Accessible with valid token |
| Status page auth | Feature | Requires valid credentials |

---

## 9. Security

- Checkout form has CSRF
- Idempotency key is server-generated, session-bound
- Access token never logged
- Rate limit on status/verify endpoints
- Room ID not accepted from guest frontend (only room_type_id)
- Price recalculated server-side regardless of frontend values
