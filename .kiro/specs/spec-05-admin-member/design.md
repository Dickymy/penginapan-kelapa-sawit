# SPEC 05 — Admin Reservation & Member: Design

> **Referensi:** requirements.md SPEC 05

---

## 1. Admin Booking Manual

### BookingService extension
```php
public function createManualBooking(array $data, Admin $admin): Booking;
```

- Same lock + recheck flow as guest/member booking
- Accepts `source`, `room_id` (admin picks physical room), `payment_status` override
- If admin marks paid → booking starts as `confirmed`
- If unpaid → `pending_payment` with configurable hold or no hold (admin decides)
- `created_by_admin_id` = admin ID
- Price override tracked (snapshot differs from room type base → audit)

### Admin\BookingController
- `create()` — form with room picker, date picker, guest fields, source dropdown
- `store(StoreManualBookingRequest)` — validate, call BookingService
- `index()` — reservation list with filters
- `show($booking)` — detail view
- `cancel($booking)` — cancel with reason
- `checkIn($booking)` — transition to checked_in
- `checkOut($booking)` — transition to checked_out
- `complete($booking)` — transition to completed
- `noShow($booking)` — transition to no_show

---

## 2. Room Block Management

### Admin\RoomBlockController
- `index()` — list blocks
- `create()` — form
- `store(StoreRoomBlockRequest)` — check conflicts first, warn admin
- `destroy($roomBlock)` — delete

### RoomBlock Model
Already has migration from SPEC 03. Add model:
- fillable, relations (belongsTo Room, belongsTo Admin)

---

## 3. Google OAuth

### Flow
```text
User clicks "Masuk dengan Google"
→ GET /auth/google → redirect to Google
→ Google authenticates → callback to /auth/google/callback
→ Controller receives provider data
→ Find SocialAccount by (google, provider_user_id)
  → Found → login user
  → Not found → find User by email (normalized)
    → Found + Google email verified → create SocialAccount, login
    → Not found → create User (email_verified if Google says so) + SocialAccount, login
→ Regenerate session
```

### Auth\GoogleController
- `redirect()` — Socialite redirect
- `callback()` — handle callback, find-or-create user

---

## 4. Member Dashboard

### Member\DashboardController (update)
- Query booking aktif (pending/confirmed/checked_in)
- Query stats

### Member\BookingController
- `index()` — list user bookings with tabs
- `show($booking)` — detail (ownership check)

### Member\ProfileController
- `edit()` — show form
- `update(UpdateProfileRequest)` — update name/whatsapp/avatar

---

## 5. Guest Claim

### BookingClaimService
```php
class BookingClaimService
{
    public function claimByEmail(User $user, Booking $booking): void;
    public function getClaimableBookings(User $user): Collection;
}
```

### Member\ClaimController
- `index()` — show claimable bookings (matching email)
- `claim($booking)` — execute claim

---

## 6. Routes (additions)

```php
// Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Member
Route::middleware(['auth', 'verified'])->prefix('member')->name('member.')->group(function () {
    Route::get('/bookings', [MemberBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [MemberBookingController::class, 'show'])->name('bookings.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/claim', [ClaimController::class, 'index'])->name('claim.index');
    Route::post('/claim/{booking}', [ClaimController::class, 'claim'])->name('claim.claim');
});

// Admin
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('bookings', AdminBookingController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('bookings/{booking}/check-in', [AdminBookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::patch('bookings/{booking}/check-out', [AdminBookingController::class, 'checkOut'])->name('bookings.check-out');
    Route::patch('bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
    Route::patch('bookings/{booking}/no-show', [AdminBookingController::class, 'noShow'])->name('bookings.no-show');
    Route::resource('room-blocks', RoomBlockController::class)->except(['edit', 'update', 'show']);
});
```

---

## 7. Test Strategy

| Test | Type |
|---|---|
| Admin create manual booking | Feature |
| Admin cancel/check-in/check-out/complete | Feature |
| Room block creation + conflict detection | Feature |
| Google OAuth mock | Feature |
| Member booking list (ownership) | Feature |
| Guest claim by email | Feature |
| Claim rejected if email mismatch | Feature |
