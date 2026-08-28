# Models — Penginapan Kelapa Sawit

> **Sumber:** `app/Models/` — diverifikasi dari source code aktual.

---

## User

**File:** `app/Models/User.php`  
**Tabel:** `users`  
**Guard:** `web`  
**Implements:** `MustVerifyEmail`

| Fillable | Cast |
|---|---|
| name, email, whatsapp, password, avatar_path, avatar_url, is_active, last_login_at | email_verified_at (datetime), last_login_at (datetime), is_active (boolean), loyalty_balance_cache (integer), password (hashed) |

**Relasi:**
- `socialAccounts()` → HasMany `SocialAccount`
- `bookings()` → HasMany `Booking`
- `loyaltyTransactions()` → HasMany `LoyaltyTransaction`

**Mutator:** `setEmailAttribute()` — lowercase + trim otomatis.

---

## Admin

**File:** `app/Models/Admin.php`  
**Tabel:** `admins`  
**Guard:** `admin`

| Fillable | Cast |
|---|---|
| name, email, password, role, is_active, last_login_at, password_changed_at | is_active (boolean), last_login_at (datetime), password_changed_at (datetime), password (hashed) |

---

## Booking

**File:** `app/Models/Booking.php`  
**Tabel:** `bookings`  
**Guarded:** `[id]`

**Casts penting:**
- `status` → `BookingStatus` enum
- `payment_status` → `PaymentStatus` enum
- `source` → `BookingSource` enum
- `check_in`, `check_out` → `date`
- Semua amount (price_per_night_snapshot, subtotal, promotion_discount, points_redeemed, points_discount, total_amount, eligible_loyalty_amount) → `integer`
- Semua email tracking timestamps (confirmation_email_sent_at, payment_email_sent_at, reminder_email_sent_at, checkout_email_sent_at, cancellation_email_sent_at) → `datetime`
- needs_attention → `boolean`

**Relasi:**
- `user()` → BelongsTo `User`
- `room()` → BelongsTo `Room`
- `createdByAdmin()` → BelongsTo `Admin` (FK: created_by_admin_id)
- `statusHistories()` → HasMany `BookingStatusHistory` (orderBy created_at)
- `payments()` → HasMany `Payment`
- `changeRequests()` → HasMany `BookingChangeRequest`
- `review()` → HasOne `Review`
- `nightPrices()` → HasMany `BookingNightPrice` (orderBy date)
- `addons()` → HasMany `BookingAddon`

**Accessors:**
- `getFormattedSubtotalAttribute()` → `'Rp' . number_format($this->subtotal, 0, ',', '.')`
- `getFormattedTotalAttribute()` → `'Rp' . number_format($this->total_amount, 0, ',', '.')`
- `getIsExpiredAttribute()` → boolean
- `getIsHoldActiveAttribute()` → boolean (status PendingPayment + expires_at di masa depan)

---

## Room

**File:** `app/Models/Room.php`  
**Tabel:** `rooms`

**Fillable:** room_type_id, code, name, floor, notes, status, is_active, sort_order

**Casts:** status → `RoomStatus`, is_active (boolean), sort_order (integer)

**Relasi:**
- `roomType()` → BelongsTo `RoomType`
- `bookings()` → HasMany `Booking`

**Scopes:** `active()`, `sellable()`

---

## RoomType

**File:** `app/Models/RoomType.php`  
**Tabel:** `room_types`

**Fillable:** name, slug, short_description, description, capacity, bed_count, bed_type, base_price, is_active, sort_order

**Casts:** capacity, bed_count, base_price, sort_order (integer), is_active (boolean)

**Relasi:**
- `rooms()` → HasMany `Room`
- `facilities()` → BelongsToMany `Facility` via `room_type_facility`
- `images()` → HasMany `RoomImage`

**Scopes:** `active()`, `ordered()`

---

## Payment

**File:** `app/Models/Payment.php`  
**Tabel:** `payments`

**Fillable:** booking_id, provider, provider_order_id, transaction_id, attempt_no, snap_token, payment_type, gross_amount, status, provider_transaction_status, fraud_status, timestamps, raw_response

**Casts:** status → `PaymentStatus`, gross_amount (integer), semua timestamp (datetime), raw_response (array)

**Relasi:** `booking()` → BelongsTo `Booking`

---

## LoyaltyTransaction

**File:** `app/Models/LoyaltyTransaction.php`  
**Tabel:** `loyalty_transactions`

**Fillable:** user_id, booking_id, type, points, balance_after, remaining_points, description, expires_at, source_transaction_id, idempotency_key, created_by_admin_id, metadata

**Casts:** type → `LoyaltyTransactionType`, points/balance_after/remaining_points (integer), expires_at (datetime), metadata (array)

**Relasi:**
- `user()` → BelongsTo `User`
- `booking()` → BelongsTo `Booking`
- `sourceTransaction()` → BelongsTo self (FK: source_transaction_id)
- `createdByAdmin()` → BelongsTo `Admin`
- `allocationsAsDebit()` / `allocationsAsCredit()` → HasMany `LoyaltyPointAllocation`

---

## LoyaltyPointAllocation

**File:** `app/Models/LoyaltyPointAllocation.php`  
**Tabel:** `loyalty_point_allocations`

Tabel penghubung FIFO antara transaksi debit (redeem/expire) dan transaksi kredit (earn/adjustment).

**Fillable:** debit_transaction_id, credit_transaction_id, points

**Relasi:**
- `debitTransaction()` → BelongsTo `LoyaltyTransaction`
- `creditTransaction()` → BelongsTo `LoyaltyTransaction`

---

## Promotion

**File:** `app/Models/Promotion.php`  
**Tabel:** `promotions`

**Fillable:** code, name, description, type, value, starts_at, ends_at, minimum_booking_amount, maximum_discount, usage_quota, max_usage_per_user, is_active

**Casts:** type → `PromotionType`, semua amount (integer), semua timestamp (datetime), is_active (boolean)

**Relasi:** `usages()` → HasMany `PromotionUsage`

---

## PromotionUsage

**File:** `app/Models/PromotionUsage.php`  
**Tabel:** `promotion_usages`

**Fillable:** promotion_id, booking_id, user_id, status, discount_amount, reserved_at, consumed_at, released_at

**Casts:** status → `PromotionUsageStatus`, discount_amount (integer), timestamps (datetime)

**Relasi:** `promotion()`, `booking()`, `user()` → BelongsTo

---

## Refund

**File:** `app/Models/Refund.php`  
**Tabel:** `refunds`

**Fillable:** booking_id, payment_id, requested_by_admin_id, processed_by_admin_id, amount, reason, notes, status, requested_at, processed_at, provider_refund_id, provider_response

**Casts:** status → `RefundStatus`, amount (integer), provider_response (array)

**Relasi:** `booking()`, `payment()`, `requestedByAdmin()`, `processedByAdmin()` → BelongsTo

---

## Review

**File:** `app/Models/Review.php`  
**Tabel:** `reviews`  
**Traits:** `SoftDeletes`

**Fillable:** user_id, booking_id, rating, title, comment, is_published, admin_reply, replied_at

**Relasi:** `user()`, `booking()` → BelongsTo

**Scopes:** `published()` — `where('is_published', true)`

---

## Expense

**File:** `app/Models/Expense.php`  
**Tabel:** `expenses`

**Fillable:** expense_date, category, amount, description, receipt_path, created_by_admin_id

**Constants:** `CATEGORIES` = [listrik, air, internet, laundry, perlengkapan_kamar, perbaikan, gaji, other]

**Relasi:** `createdBy()` → BelongsTo `Admin`

---

## RateOverride

**File:** `app/Models/RateOverride.php`  
**Tabel:** `rate_overrides`

**Fillable:** room_type_id, date, price, label

**Relasi:** `roomType()` → BelongsTo `RoomType`

Unique constraint pada `(room_type_id, date)`.

---

## Addon

**File:** `app/Models/Addon.php`  
**Tabel:** `addons`

**Fillable:** name, description, price, is_active, sort_order

**Scopes:** `active()`, `ordered()`

---

## BookingAddon

**File:** `app/Models/BookingAddon.php`  
**Tabel:** `booking_addons`

Snapshot addon saat booking dibuat — `unit_price` tidak berubah meskipun harga addon diubah.

**Fillable:** booking_id, addon_id, quantity, unit_price, subtotal

**Relasi:** `booking()`, `addon()` → BelongsTo

---

## BookingChangeRequest

**File:** `app/Models/BookingChangeRequest.php`  
**Tabel:** `booking_change_requests`

**Fillable:** booking_id, user_id, type, original_data, requested_data, price_difference, status, admin_notes, processed_by_admin_id, processed_at

**Casts:** original_data, requested_data (array), price_difference (integer)

**Relasi:** `booking()`, `user()`, `processedByAdmin()` → BelongsTo

---

## Facility

**File:** `app/Models/Facility.php`  
**Tabel:** `facilities`

**Fillable:** name, slug, icon, description, is_active, sort_order

**Relasi:** `roomTypes()` → BelongsToMany `RoomType` via `room_type_facility`

---

## Gallery

**File:** `app/Models/Gallery.php`  
**Tabel:** `galleries`

**Fillable:** title, path, thumb_path, medium_path, large_path, alt_text, is_active, sort_order, created_by_admin_id

**Relasi:** `createdBy()` → BelongsTo `Admin`

---

## Faq

**File:** `app/Models/Faq.php`  
**Tabel:** `faqs`

**Fillable:** question, answer, category, sort_order, is_active

---

## NearbyPlace

**File:** `app/Models/NearbyPlace.php`  
**Tabel:** `nearby_places`

**Fillable:** name, category, distance, description, image, map_link, sort_order, is_active

---

## ContactMessage

**File:** `app/Models/ContactMessage.php`  
**Tabel:** `contact_messages`

**Fillable:** name, email, phone, subject, message, is_read, admin_notes, replied_at

**Scopes:** `unread()`

---

## PolicyVersion

**File:** `app/Models/PolicyVersion.php`  
**Tabel:** `policy_versions`

**Fillable:** policy_key, version, title, content, is_current, published_at, created_by_admin_id

**Relasi:** `createdBy()` → BelongsTo `Admin`

**Scopes:** `current($policyKey)` — filter by policy_key + is_current = true

---

## Setting

**File:** `app/Models/Setting.php`  
**Tabel:** `settings`

**Fillable:** group, key, value, type, is_public, updated_by_admin_id

**Static methods:** `get($group, $key, $default)`, `set($group, $key, $value, ...)`

> ⚠️ Secret (API key, password) TIDAK BOLEH disimpan di tabel ini. Hanya config UI seperti nama properti, alamat, nomor WA yang boleh disimpan.

---

## SocialAccount

**File:** `app/Models/SocialAccount.php`  
**Tabel:** `social_accounts`

**Fillable:** user_id, provider, provider_user_id, provider_email, provider_email_verified

**Relasi:** `user()` → BelongsTo `User`

---

## Model Pendukung

| Model | File | Kegunaan |
|---|---|---|
| AuditLog | `app/Models/AuditLog.php` | Log aksi kritis, static `record()` |
| DocumentSequence | `app/Models/DocumentSequence.php` | Generator kode booking & nomor invoice |
| BookingNightPrice | `app/Models/BookingNightPrice.php` | Snapshot harga per malam, relasi ke Booking |
| BookingStatusHistory | `app/Models/BookingStatusHistory.php` | Histori transisi status booking |
| PaymentWebhookEvent | `app/Models/PaymentWebhookEvent.php` | Log raw webhook Midtrans |
| RoomImage | `app/Models/RoomImage.php` | Gambar kamar (path variants), relasi ke RoomType |
| RoomBlock | `app/Models/RoomBlock.php` | Blokir kamar manual, relasi ke Room |
