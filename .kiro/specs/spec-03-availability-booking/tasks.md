# SPEC 03 — Availability & Guest Booking Engine: Tasks

> **Status:** Ready for implementation  
> **Dependency:** SPEC 02 complete

---

## Task 1: Migrations — Bookings, Status History, Document Sequences

- [ ] Create migration `create_bookings_table` with all columns, indexes, and FKs
- [ ] Create migration `create_booking_status_histories_table`
- [ ] Create migration `create_document_sequences_table`
- [ ] Run migrations
- [ ] Verify tables, indexes, FKs correct
- [ ] Commit

**Depends on:** SPEC 02  
**REQ traceability:** REQ-4.1, REQ-5.1, REQ-6.1  
**Verification:** Migrations run cleanly

---

## Task 2: Booking Model & Status History Model

- [ ] Create `app/Models/Booking.php` — fillable, casts (status, payment_status, source enums), relations, accessors
- [ ] Create `app/Models/BookingStatusHistory.php` — fillable, relations
- [ ] Create `app/Models/DocumentSequence.php`
- [ ] Add `hasMany(Booking)` to Room model
- [ ] Add `hasMany(Booking)` to User model (if not already)
- [ ] Commit

**Depends on:** Task 1  
**REQ traceability:** REQ-3.1, REQ-6.1  
**Verification:** Models instantiate, relations work

---

## Task 3: AvailabilityService

- [ ] Create `app/Services/AvailabilityService.php`
- [ ] Implement `searchAvailableRoomTypes(checkIn, checkOut, guestCount)`
- [ ] Implement `findAvailableRooms(roomTypeId, checkIn, checkOut)`
- [ ] Implement `isRoomAvailable(roomId, checkIn, checkOut, excludeBookingId?)`
- [ ] Implement `assertRoomAvailableForBooking(roomId, checkIn, checkOut)` — throws RoomNotAvailableException
- [ ] Create `app/Exceptions/RoomNotAvailableException.php`
- [ ] Unit test: overlap boundary cases (adjacent OK, overlapping NOT OK)
- [ ] Feature test: search with blocked/held/available rooms
- [ ] Commit

**Depends on:** Task 2  
**REQ traceability:** REQ-1.1, REQ-1.2  
**Verification:** Unit + feature tests pass

---

## Task 4: PricingService

- [ ] Create `app/Services/PricingService.php`
- [ ] Implement `calculateNights(checkIn, checkOut)` — pure, returns int
- [ ] Implement `calculateQuote(roomType, checkIn, checkOut)` — returns structured array
- [ ] Unit test: night calculation (1 night, 7 nights, boundary)
- [ ] Unit test: pricing calculation (nights × rate)
- [ ] Commit

**Depends on:** SPEC 02 (RoomType model)  
**REQ traceability:** REQ-2.1  
**Verification:** Unit tests pass

---

## Task 5: DocumentSequenceService & Booking Code Generation

- [ ] Create `app/Services/DocumentSequenceService.php`
- [ ] Implement `generateBookingCode()` — lock + increment + format BKG-YYYYMM-XXXX
- [ ] Implement `generateInvoiceNumber()` — same pattern INV-YYYYMM-XXXX
- [ ] Unit/feature test: generates sequential codes, handles month rollover
- [ ] Commit

**Depends on:** Task 1 (document_sequences table)  
**REQ traceability:** REQ-6.1  
**Verification:** Tests pass, codes sequential

---

## Task 6: BookingService — Create Booking

- [ ] Create `app/Services/BookingService.php`
- [ ] Implement `createGuestBooking(data)` — full flow with transaction, lock, recheck, quote, sequence, token
- [ ] Implement `createMemberBooking(data, user)` — same but with user_id
- [ ] Create `app/Exceptions/InvalidBookingDataException.php`
- [ ] Generate guest_access_token (random 64 chars), store SHA-256 hash
- [ ] Snapshot: room_type_name, room_name, price_per_night
- [ ] Set payment_expires_at = now + config('booking.hold_minutes') minutes
- [ ] Write BookingStatusHistory on creation
- [ ] Handle idempotency_key (return existing if same key)
- [ ] Feature test: create guest booking successfully
- [ ] Feature test: create member booking with user_id
- [ ] Feature test: idempotency (same key → same booking)
- [ ] Feature test: double booking rejected (room locked)
- [ ] Commit

**Depends on:** Task 3, 4, 5  
**REQ traceability:** REQ-3.1, REQ-3.2, REQ-4.1, REQ-5.1, REQ-7.1  
**Verification:** All feature tests pass, no double bookings possible

---

## Task 7: Expire Pending Command

- [ ] Create `app/Console/Commands/ExpirePendingBookingsCommand.php`
- [ ] Query: pending_payment + payment_expires_at <= now
- [ ] Per booking: lock, recheck status, transition to expired
- [ ] Write status history
- [ ] Register in scheduler (every minute)
- [ ] Feature test: pending booking expired after time, confirmed booking NOT expired
- [ ] Commit

**Depends on:** Task 6  
**REQ traceability:** REQ-5.2  
**Verification:** Command test passes, scheduler registered

---

## Task 8: Public Availability & Checkout Pages

- [ ] Create `app/Http/Controllers/Public/AvailabilityController.php` — search
- [ ] Create `app/Http/Controllers/Public/BookingController.php` — showCheckout, store, confirmation, status, verifyAccess
- [ ] Create `app/Http/Requests/Booking/SearchAvailabilityRequest.php`
- [ ] Create `app/Http/Requests/Booking/StoreBookingRequest.php`
- [ ] Create views: `public/availability/results.blade.php`
- [ ] Create views: `public/booking/checkout.blade.php`
- [ ] Create views: `public/booking/confirmation.blade.php`
- [ ] Create views: `public/booking/status.blade.php`
- [ ] Create views: `public/booking/verify.blade.php` (form: code + email/wa)
- [ ] Add routes for availability and booking
- [ ] Generate idempotency_key in checkout form (session-based)
- [ ] Commit

**Depends on:** Task 6  
**REQ traceability:** REQ-1.1, REQ-3.1, REQ-7.1, REQ-8.1  
**Verification:** Pages render, form submits create booking

---

## Task 9: Final Integration & Tests

- [ ] Run full test suite
- [ ] Run `npm run build`
- [ ] Run `php artisan route:list`
- [ ] Verify availability search end-to-end
- [ ] Verify guest checkout end-to-end (creates booking, shows confirmation)
- [ ] Verify expire command
- [ ] Verify access token verification
- [ ] Commit: "chore(spec03): final verification"

**Depends on:** All previous tasks  
**Verification:** All tests pass, flows work end-to-end

---

## Summary

| Task | Scope | Dependencies |
|---|---|---|
| 1 | Migrations | SPEC 02 |
| 2 | Models | Task 1 |
| 3 | AvailabilityService | Task 2 |
| 4 | PricingService | SPEC 02 |
| 5 | DocumentSequenceService | Task 1 |
| 6 | BookingService | Task 3, 4, 5 |
| 7 | Expire Command | Task 6 |
| 8 | Public Pages | Task 6 |
| 9 | Final Verification | All |

**Parallelizable:** Task 3, 4, 5 setelah Task 2.  
**Critical path:** Task 6 memerlukan Task 3+4+5, lalu Task 7+8 memerlukan Task 6.
