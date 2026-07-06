# SPEC 05 — Admin Reservation & Member: Tasks

> **Status:** Ready for implementation  
> **Dependency:** SPEC 04 complete

---

## Task 1: RoomBlock Model + Admin CRUD

- [ ] Create `app/Models/RoomBlock.php` — fillable, relations
- [ ] Create `app/Http/Controllers/Admin/RoomBlockController.php` — index, create, store, destroy
- [ ] Create `app/Http/Requests/Admin/StoreRoomBlockRequest.php`
- [ ] Create views: admin/room-blocks/index, create
- [ ] Add routes
- [ ] Conflict detection before save (show conflicting bookings)
- [ ] Test: create block, delete block, conflict shown
- [ ] Commit

---

## Task 2: Admin Booking Controller (Manual + Operations)

- [ ] Extend BookingService: `createManualBooking(data, admin)`
- [ ] Create `app/Http/Controllers/Admin/BookingController.php` — index, create, store, show, cancel, checkIn, checkOut, complete, noShow
- [ ] Create `app/Http/Requests/Admin/StoreManualBookingRequest.php`
- [ ] Create views: admin/bookings/index, create, show
- [ ] Add routes for admin bookings + operations
- [ ] Each operation: validate transition, lock booking, update, write history
- [ ] Test: create manual, cancel, check-in, check-out, complete
- [ ] Commit

---

## Task 3: Google OAuth

- [ ] Install `laravel/socialite` if not installed
- [ ] Configure Google provider in `config/services.php`
- [ ] Create `app/Http/Controllers/Auth/GoogleController.php` — redirect, callback
- [ ] Implement find-or-create logic with SocialAccount linking
- [ ] Add Google login button to auth views
- [ ] Add routes
- [ ] Test: mock Socialite, new user created, existing user linked
- [ ] Commit

---

## Task 4: Member Bookings & Profile

- [ ] Create `app/Http/Controllers/Member/BookingController.php` — index (with tabs), show (ownership)
- [ ] Create `app/Http/Controllers/Member/ProfileController.php` — edit, update
- [ ] Create `app/Http/Requests/Member/UpdateProfileRequest.php`
- [ ] Create views: member/bookings/index, show, member/profile/edit
- [ ] Update member layout nav links
- [ ] Add routes
- [ ] Test: member sees own bookings only, profile update
- [ ] Commit

---

## Task 5: Guest Booking Claim

- [ ] Create `app/Services/BookingClaimService.php` — claimByEmail, getClaimableBookings
- [ ] Create `app/Http/Controllers/Member/ClaimController.php` — index, claim
- [ ] Create view: member/claim/index
- [ ] Add routes
- [ ] Test: claim succeeds with matching email, fails with mismatch, already claimed rejected
- [ ] Commit

---

## Task 6: Admin Sidebar Updates + Final Tests

- [ ] Enable admin sidebar: Reservasi → admin.bookings.index, Room Block → admin.room-blocks.index
- [ ] Run full test suite
- [ ] Run `npm run build`
- [ ] Commit

---

## Summary

| Task | Scope | Dependencies |
|---|---|---|
| 1 | Room Block CRUD | SPEC 04 |
| 2 | Admin Booking Operations | Task 1 |
| 3 | Google OAuth | SPEC 04 |
| 4 | Member Bookings/Profile | SPEC 04 |
| 5 | Guest Claim | Task 4 |
| 6 | Final | All |
