# Feature: Booking Flow — Penginapan Kelapa Sawit

> **Sumber:** `app/Services/BookingService.php`, `app/Services/AvailabilityService.php`, `routes/web.php`

---

## Overview

Guest dan member dapat memesan kamar tanpa perbedaan alur utama. Perbedaannya hanya:
- Member: `user_id` diisi, data profil di-autofill, bisa pakai poin
- Guest: `user_id` null, akses via `guest_access_token_hash`

---

## Alur Guest/Member (Website)

```
1. Cari kamar → GET /ketersediaan
2. Pilih kamar → GET /kamar/{slug}
3. Isi form checkout → GET /checkout
4. Submit booking → POST /booking
5. Halaman konfirmasi → GET /booking/{code}/konfirmasi
6. Bayar → GET /booking/{code}/bayar (Midtrans Snap)
7. Selesai → GET /booking/{code}/selesai
```

---

## Proteksi Double Booking (5 Lapis)

Referensi: `app/Services/BookingService.php::createBooking()`, `app/Services/AvailabilityService.php`

| Layer | Implementasi |
|---|---|
| 1. Search | `AvailabilityService::searchAvailableRoomTypes()` — filter kamar tersedia |
| 2. Checkout | `AvailabilityService::isRoomAvailable()` — recheck sebelum tampil ringkasan |
| 3. Create Booking | `DB::transaction()` + `Room::lockForUpdate()` + `AvailabilityService::assertRoomAvailableForBooking()` |
| 4. Idempotency | `idempotency_key` SHA-256 — cek dalam transaction dengan `lockForUpdate` |
| 5. Unique Constraint | `bookings.booking_code` unique, `payments.provider_order_id` unique |

**Aturan overlap:** `existing.check_in < new.check_out AND existing.check_out > new.check_in`

**Status blocking:** `PendingPayment`, `Confirmed`, `CheckedIn` (via `BookingStatus::isBlocking()`)

Room block juga memblokir kamar dengan rumus overlap yang sama.

---

## Kalkulasi Harga

Referensi: `app/Services/PricingService.php`

```
subtotal = Σ harga_per_malam (base_price atau rate_override)
total_amount = max(0, subtotal - promotion_discount - points_discount)
eligible_loyalty_amount = total_amount setelah diskon
```

- **Rate Override:** `RateOverride` per tanggal per room_type — cek setiap malam dalam rentang
- **Promo:** tidak dapat digabung dengan poin (V1)
- Snapshot `booking_night_prices` disimpan saat booking dibuat

---

## Hold & Expiry

Referensi: `config/booking.php`, `app/Console/Commands/ExpirePendingBookingsCommand.php`

- `payment_expires_at = now() + 30 menit` (configurable via `BOOKING_HOLD_MINUTES`)
- Scheduler expire: command `booking:expire-pending`
- Booking website dengan source berbeda memiliki expiry berbeda:
  - Website: 30 menit
  - WhatsApp/Phone (manual admin): 24 jam
  - OTA/Walk-in (manual admin): null (tidak auto-expire)

---

## Status Transition

Referensi: `app/Enums/BookingStatus.php`

```
PendingPayment → Confirmed → CheckedIn → CheckedOut → Completed
             ↘ Expired
             ↘ Cancelled
    Confirmed → NoShow
```

Setiap transisi wajib menulis `BookingStatusHistory`. Tidak ada endpoint generic `update_status` — setiap aksi menggunakan method spesifik di controller/service.

---

## Akses Booking Guest

Referensi: `app/Services/BookingAccessService.php`

- Token 64 karakter random dibuat saat booking (`Str::random(64)`)
- Yang disimpan: `SHA-256(raw_token)` di `bookings.guest_access_token_hash`
- Raw token dikirim ke tamu via URL konfirmasi / session
- Verifikasi: `hash('sha256', $rawToken) === $booking->guest_access_token_hash`

---

## Klaim Booking (Member)

Referensi: `app/Services/BookingClaimService.php`, `app/Http/Controllers/Member/ClaimController.php`

Member yang register setelah booking dapat mengklaim booking lama:
1. `getClaimableBookings($user)` — booking dengan `guest_email = $user->email`, `user_id = null`, belum diklaim
2. `claimByEmail($user, $booking)` — set `user_id`, set `claimed_at`
3. **Validasi wajib:** email terverifikasi harus cocok dengan `guest_email` booking

---

## Booking Manual oleh Admin

Referensi: `app/Services/BookingService.php::createManualBooking()`

Admin dapat membuat booking untuk sumber walk-in, WhatsApp, Phone, OTA:
- Harga dapat di-override manual
- OTA/Walk-in langsung `Confirmed` (tidak `PendingPayment`)
- OTA tidak auto-expire
- Dispatch `BookingCreated` event setelah sukses

---

## Invoice

Referensi: `app/Services/InvoiceService.php`, `resources/views/invoices/booking.blade.php`

- Format nomor: `INV-YYYYMM-0001`
- Data dari snapshot booking — tidak mengambil harga terbaru room_type
- Authorization: member hanya bisa invoice miliknya; guest perlu token; admin bebas
- Route: `GET /booking/{bookingCode}/invoice`
