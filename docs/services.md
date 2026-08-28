# Services — Penginapan Kelapa Sawit

> **Sumber:** `app/Services/` — diverifikasi dari source code aktual.

Semua service berada di `app/Services/`. Controller hanya memanggil service, tidak mengandung business logic.

---

## AvailabilityService

**File:** `app/Services/AvailabilityService.php`

Mengelola semua logika ketersediaan kamar. Diinjeksi ke `BookingService`.

| Method | Deskripsi |
|---|---|
| `searchAvailableRoomTypes($checkIn, $checkOut, $guestCount)` | Cari tipe kamar yang tersedia untuk rentang tanggal |
| `findAvailableRooms($roomTypeId, $checkIn, $checkOut)` | Daftar kamar fisik yang tersedia |
| `isRoomAvailable($roomId, $checkIn, $checkOut)` | Cek ketersediaan satu kamar (tanpa lock) |
| `assertRoomAvailableForBooking($roomId, $checkIn, $checkOut)` | Cek availability setelah lock — throws `RoomNotAvailableException` jika tidak tersedia |

**Aturan overlap:** `existing.check_in < new.check_out AND existing.check_out > new.check_in`

**Status blocking:** `PendingPayment`, `Confirmed`, `CheckedIn` — lihat `BookingStatus::isBlocking()`

---

## BookingService

**File:** `app/Services/BookingService.php`

Orkestrasi utama pembuatan dan pengelolaan booking. Menggunakan `DB::transaction()` + `lockForUpdate()`.

| Method | Deskripsi |
|---|---|
| `createGuestBooking(array $data)` | Buat booking tanpa user (returns `['booking' => Booking, 'raw_token' => string]`) |
| `createMemberBooking(array $data, User $user)` | Buat booking dengan user |
| `createManualBooking(array $data, Admin $admin)` | Buat booking manual oleh admin (walk-in, WA, OTA, dll) |
| `expirePendingBooking(Booking $booking)` | Expire booking — dispatch `BookingCancelled` event |

**Internal private methods:**
- `createBooking(array $data, ?User $user)` — core logic dengan transaction + lock
- `findAndLockRoom($roomTypeId, $checkIn, $checkOut)` — kandidat kamar → lock → availability check
- `generateIntentKey(array $data)` — SHA-256 fingerprint untuk idempotency
- `determineManualBookingStatus($source, $isPaid)` — status awal booking manual per sumber
- `determineManualBookingExpiry($source, $isPaid, $data)` — expiry per sumber (OTA/walk-in = null, WhatsApp/Phone = 24 jam)

**Event yang di-dispatch:**
- `BookingCreated::dispatch($booking)` — setelah `createBooking()` (di luar transaction, cek `wasRecentlyCreated`)
- `BookingCancelled::dispatch($booking)` — dari `expirePendingBooking()`

**Dependensi injected:**
- `AvailabilityService`
- `PricingService`
- `DocumentSequenceService`
- `LoyaltyPointService`

---

## PricingService

**File:** `app/Services/PricingService.php`

Kalkulasi harga server-side. Frontend tidak pernah menjadi sumber kebenaran harga.

| Method | Deskripsi |
|---|---|
| `calculateNights($checkIn, $checkOut)` | Hitung jumlah malam (int) |
| `calculateQuote($roomType, $checkIn, $checkOut)` | Quote dasar dengan breakdown per malam + rate override |
| `calculateQuoteWithPromo($roomType, $checkIn, $checkOut, $promoCode, $user)` | Quote + validasi & diskon promo |
| `calculateQuoteWithPoints($roomType, $checkIn, $checkOut, $requestedPoints, $user)` | Quote + kalkulasi redeem poin |
| `calculateQuoteWithAddons($baseQuote, $selectedAddons)` | Tambahkan addon ke quote yang sudah ada |

**Struktur return `calculateQuote()`:**
```php
[
    'nights' => int,
    'price_per_night' => int,        // base price (backward compat)
    'night_prices' => array,         // breakdown per malam ['date', 'price', 'label']
    'subtotal' => int,
    'promotion_discount' => int,     // 0 jika tidak ada promo
    'points_discount' => int,        // 0 jika tidak pakai poin
    'points_redeemed' => int,        // 0 jika tidak pakai poin
    'total_amount' => int,
    'eligible_loyalty_amount' => int,
    'promotion' => null|Promotion,
]
```

**Promo tidak dapat digabung dengan poin** pada satu booking (V1).

**Dependensi injected:** `PromotionService`, `LoyaltyPointService`

---

## LoyaltyPointService

**File:** `app/Services/LoyaltyPointService.php`

Semua operasi saldo dan transaksi poin loyalitas. Menggunakan `lockForUpdate` + idempotency key.

| Method | Deskripsi |
|---|---|
| `getBalance(User $user)` | Hitung saldo dari ledger (`sum('points')`) — bukan dari cache |
| `awardForCompletedBooking(Booking $booking)` | Beri poin setelah booking `Completed` |
| `redeemForBooking(User $user, Booking $booking, int $requestedPoints)` | Redeem poin FIFO, buat alokasi |
| `reverseRedemptionForBooking(Booking $booking)` | Batalkan redemption — buat transaksi Reversal, kembalikan poin ke lot |
| `expirePointsForUser(User $user)` | Expire semua lot yang melewati `expires_at` |
| `adjustPoints(User $user, int $points, string $reason, Admin $admin)` | Penyesuaian manual oleh admin |

**Aturan earn:** `floor(eligible_loyalty_amount / earn_divisor)` — diconfig di `config/loyalty.php`

**Aturan redeem:**
- Minimum: 100 poin (configurable)
- 1 poin = Rp50 (configurable)
- Maksimum 20% dari subtotal (configurable)
- FIFO: lot dengan `expires_at` terdekat duluan

**Idempotency keys:**
- Earn: `"earn:booking:{$booking->id}"`
- Redeem: `"redeem:booking:{$booking->id}"`
- Reversal: `"reversal:redeem:booking:{$booking->id}"`
- Expire: `"expire:loyalty_transaction:{$lot->id}"`
- Adjust: hash SHA-256 dari user+points+reason+admin+date

---

## PromotionService

**File:** `app/Services/PromotionService.php`

Validasi dan lifecycle quota promo. Row lock pada tabel `promotions` saat reserve.

| Method | Deskripsi |
|---|---|
| `validateForQuote($promoCode, $subtotal, $user)` | Validasi kode promo, hitung diskon — return `['discount', 'promotion']` |
| `reserve($promotion, $booking, $user)` | Set status usage → `reserved` (di dalam transaction) |
| `consume($promotionUsage)` | Set usage → `consumed` (saat payment confirmed) |
| `release($promotionUsage)` | Set usage → `released` (saat expired/cancel) |

---

## MidtransPaymentService

**File:** `app/Services/MidtransPaymentService.php`

Integrasi Midtrans Snap. Server Key hanya digunakan backend — tidak pernah dikirim ke browser.

| Method | Deskripsi |
|---|---|
| `createOrResumePayment(Booking $booking)` | Cek payment existing atau buat baru, return Snap token |
| `generateSnapTokenForPayment(Payment $payment)` | Generate token ke Midtrans API |
| `getSnapTokenFromProvider(Payment $payment)` | Ambil token dari provider |
| `handleWebhook(Request $request)` | Verifikasi signature, proses notifikasi, update status |
| `reconcilePayment(Booking $booking)` | Server-to-server cek status pembayaran |

**Keamanan webhook:**
1. Signature verification (SHA-512 sesuai docs Midtrans)
2. Idempotency: duplikat webhook return 2xx tanpa side effect ganda
3. Amount check: `gross_amount` payload harus cocok dengan `total_amount` booking
4. Tidak log Server Key atau data sensitif

---

## BookingAccessService

**File:** `app/Services/BookingAccessService.php`

Kontrol akses booking untuk tamu tanpa login (menggunakan `guest_access_token_hash`).

| Method | Deskripsi |
|---|---|
| `hasAccess($booking, $rawToken)` | Cek apakah token valid (SHA-256 hash comparison) |
| `grantAccess($booking, $session)` | Simpan akses ke session |
| `grantCreationAccess($booking, $rawToken, $session)` | Simpan akses saat booking baru dibuat |
| `verifyByToken($booking, $rawToken)` | Verifikasi token sekali pakai |

---

## BookingClaimService

**File:** `app/Services/BookingClaimService.php`

Member mengklaim booking yang dibuat sebelum register.

| Method | Deskripsi |
|---|---|
| `getClaimableBookings(User $user)` | Cari booking dengan `guest_email` cocok email member, belum diklaim |
| `claimByEmail(User $user, Booking $booking)` | Set `user_id` pada booking (validasi email wajib cocok) |

> **Klaim hanya berdasarkan email terverifikasi.** Tidak boleh berdasarkan nama atau nomor WhatsApp saja.

---

## BookingChangeService

**File:** `app/Services/BookingChangeService.php`

Perubahan booking oleh member (date change, dll).

| Method | Deskripsi |
|---|---|
| `previewChange(Booking $booking, array $data)` | Preview dampak perubahan (harga, availability) |
| `submitRequest(Booking $booking, User $user, array $data)` | Submit change request |
| `approveRequest(BookingChangeRequest $request, Admin $admin)` | Admin approve dan eksekusi perubahan |

---

## InvoiceService

**File:** `app/Services/InvoiceService.php`

| Method | Deskripsi |
|---|---|
| `isEligible(Booking $booking)` | Cek apakah booking berhak mendapat invoice |
| `generateInvoiceNumber(Booking $booking)` | Generate nomor format `INV-YYYYMM-0001` |
| `generatePdf(Booking $booking)` | Render template Blade → PDF via DomPDF |

Invoice menggunakan **snapshot data booking**, bukan harga terbaru room type.

---

## DocumentSequenceService

**File:** `app/Services/DocumentSequenceService.php`

Generator sequence unik dengan table lock untuk concurrency safety.

| Method | Deskripsi |
|---|---|
| `generateBookingCode()` | Generate kode booking unik (format `BKG-YYYYMM-XXXX`) |
| `generateInvoiceNumber()` | Generate nomor invoice unik (format `INV-YYYYMM-XXXX`) |

---

## ImageUploadService

**File:** `app/Services/ImageUploadService.php`

| Method | Deskripsi |
|---|---|
| `upload($file, $path)` | Upload file gambar |
| `uploadWithVariants($file, $path)` | Upload + buat varian thumb/medium/large via Intervention Image |
| `delete($path)` | Hapus file dari storage |
