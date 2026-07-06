# SPEC 06 — Loyalty & Promotion: Design

> **Referensi:** requirements.md SPEC 06

---

## 1. Database Tables (already in master plan)

### `loyalty_transactions` (new migration)
### `loyalty_point_allocations` (new migration)
### `promotions` (new migration)
### `promotion_usages` (new migration)

These follow the exact schema from master requirements (sections 7.3.13–7.3.16).

---

## 2. LoyaltyPointService

```php
class LoyaltyPointService
{
    public function getBalance(User $user): int;
    public function awardForCompletedBooking(Booking $booking): void;
    public function redeemForBooking(User $user, Booking $booking, int $points): int; // returns discount
    public function reverseRedemptionForBooking(Booking $booking): void;
    public function expirePointsForUser(User $user): void;
    public function adjustPoints(User $user, int $points, string $reason, Admin $admin): void;
}
```

### Earn Flow
1. Lock booking, verify completed + source eligible + user exists + not already earned
2. Compute points = floor(eligible_amount / earn_divisor)
3. Create earn transaction (idempotency key)
4. Set remaining_points = points, expires_at = +18 months
5. Update user cache

### Redeem Flow
1. Lock user
2. Get lots: remaining > 0, not expired, order by expires_at ASC, created_at ASC
3. Verify sufficient balance
4. Compute max discount: min(points × value, 20% subtotal)
5. Create debit transaction
6. Create allocations from lots (FIFO)
7. Decrement remaining_points
8. Update cache

### Reversal Flow
1. Find redeem transaction for booking
2. Create reversal transaction
3. Restore allocations to lots (remaining_points +=)
4. Update cache

---

## 3. PromotionService

```php
class PromotionService
{
    public function validateForQuote(string $code, int $subtotal, ?User $user = null): array;
    public function calculateDiscount(Promotion $promo, int $subtotal): int;
    public function reserveForBooking(Promotion $promo, Booking $booking, ?User $user = null): void;
    public function consumeForBooking(Booking $booking): void;
    public function releaseForBooking(Booking $booking): void;
}
```

### Reserve Flow (during booking creation)
1. Begin transaction
2. Lock promotion row
3. Recheck active, date range, minimum
4. Count usages (reserved + consumed)
5. Check quota
6. Check per-user limit
7. Insert usage as `reserved`
8. Commit

---

## 4. PricingService Extension

Update `calculateQuote` to accept optional promo code and points:
```php
public function calculateQuote(
    RoomType $roomType,
    Carbon $checkIn,
    Carbon $checkOut,
    ?string $promoCode = null,
    ?int $redeemPoints = null,
    ?User $user = null
): array;
```

---

## 5. Routes

```php
// Admin Loyalty
Route::get('loyalty', [AdminLoyaltyController::class, 'index'])->name('loyalty.index');
Route::get('loyalty/{user}', [AdminLoyaltyController::class, 'show'])->name('loyalty.show');
Route::post('loyalty/{user}/adjust', [AdminLoyaltyController::class, 'adjust'])->name('loyalty.adjust');

// Admin Promotions
Route::resource('promotions', AdminPromotionController::class)->except(['show']);

// Member Poin
Route::get('/points', [MemberPointController::class, 'index'])->name('points.index');
```

---

## 6. Commands

### `loyalty:expire-points`
- Daily scheduler
- Process per user: find expired lots, create expire transactions

### `loyalty:award-completed`
- Runs after booking complete (called from BookingService.complete or scheduled)
- Find completed bookings not yet awarded → award

---

## 7. Test Strategy

| Test | Type |
|---|---|
| Earn points on complete | Feature |
| Earn idempotent (double award prevented) | Feature |
| OTA source not eligible | Unit |
| Redeem with FIFO | Feature |
| Redeem max 20% | Unit |
| Reversal on cancel | Feature |
| Expiry command | Feature |
| Admin adjustment | Feature |
| Promo validation (active, date, minimum, quota) | Feature |
| Promo reserve/consume/release | Feature |
| Promo + poin rejected | Feature |
