# AI_CONTEXT.md — Penginapan Kelapa Sawit

> Dokumen ini ditulis untuk AI assistant (Claude, ChatGPT, Gemini, Cursor, Codex, OpenCode, dll) yang akan membantu pengembangan project ini.
> Baca dokumen ini sebelum menyentuh source code apapun.
> **Terakhir diverifikasi:** Agustus 2026, berdasarkan source code aktual.

---

## 1. APA PROYEK INI

Website penginapan full-stack berbasis **Laravel 12** untuk "Penginapan Kelapa Sawit" di Kota Bangun, Kalimantan Timur, Indonesia. Bukan proyek demo — ini sistem bisnis nyata dengan:

- **Booking engine** + payment gateway (Midtrans Snap)
- **Member area** dengan loyalty points
- **Admin panel** lengkap (reservasi, laporan, konten)
- **Guest booking** — tamu tidak wajib daftar akun

**Bahasa aplikasi:** Bahasa Indonesia  
**Timezone:** `Asia/Makassar` (WITA, UTC+8)  
**Mata uang:** IDR, disimpan sebagai integer (BIGINT UNSIGNED, tanpa desimal)

---

## 2. STACK TEKNOLOGI

```
Backend:   Laravel 12.x, PHP 8.2.16, MySQL 8.0.30 (InnoDB)
Auth:      Laravel Fortify (member) + guard terpisah (admin)
OAuth:     Laravel Socialite (Google)
Payment:   midtrans/midtrans-php ^2.6 (Snap)
PDF:       barryvdh/laravel-dompdf ^3.1
Image:     intervention/image 3.0
Storage:   Local + S3 (league/flysystem-aws-s3-v3)
Frontend:  Blade + Tailwind CSS v4 + Alpine.js v3
Build:     Vite 6 + laravel-vite-plugin
Queue:     Database queue (QUEUE_CONNECTION=database)
Testing:   PHPUnit 11
```

**TIDAK ADA:** React, Vue, Inertia, Livewire, SPA, Laravel Policy, Spatie Permission.

---

## 3. TIGA PENGGUNA

| Peran | Guard | Tabel | Akses |
|---|---|---|---|
| **Guest** | — | — | Website publik, booking via token, cek booking |
| **Member** | `web` | `users` | + dashboard, histori, poin loyalitas, klaim booking |
| **Admin** | `admin` | `admins` | + seluruh manajemen properti & operasional |

Guard admin **sepenuhnya terpisah** dari member — session berbeda, route berbeda, tabel berbeda.

---

## 4. STRUKTUR FOLDER PENTING

```
app/
├── Enums/              ← Semua status (BookingStatus, PaymentStatus, dll)
├── Events/             ← BookingCreated, BookingCancelled, PaymentConfirmed
├── Exceptions/         ← InvalidStatusTransitionException, RoomNotAvailableException
├── Http/
│   ├── Controllers/
│   │   ├── Admin/      ← Semua admin pages + Auth/LoginController
│   │   ├── Auth/       ← GoogleController
│   │   ├── Member/     ← Dashboard, bookings, profile, claim, points, reviews
│   │   ├── Public/     ← Homepage, rooms, booking, payment, pages
│   │   └── Webhook/    ← MidtransWebhookController
│   ├── Requests/       ← Form Request per domain
│   └── Responses/      ← LoginResponse (Fortify custom)
├── Listeners/          ← SendBookingConfirmationListener, dll
├── Mail/               ← 9 Mailable classes (semua ShouldQueue)
├── Models/             ← 31 Eloquent models
├── Services/           ← 12 domain services (semua business logic di sini)
└── Support/
    ├── Phone/PhoneNormalizer.php   ← Normalisasi nomor ke 628xxx
    ├── WhatsApp.php                ← Generator wa.me URL
    └── ArrivalTimeSlots.php        ← Slot waktu 14:00–23:30
config/
├── booking.php         ← hold_minutes=30, eligible_sources, check-in/out time
├── loyalty.php         ← earn_divisor=1000, point_value=50, min_redeem=100
└── midtrans.php        ← server_key, client_key, is_production=false
routes/web.php          ← SATU file untuk semua route
```

---

## 5. PRINSIP ARSITEKTUR — WAJIB DIPAHAMI

### Controller Tipis

Controller hanya: terima request → validasi (Form Request) → authorization (guard/check) → panggil Service → return response.

**Business logic TIDAK boleh ada di controller.** Semua di Service.

### Service Boundary

| Service | Tanggung Jawab |
|---|---|
| `AvailabilityService` | Cek ketersediaan kamar, overlap check |
| `BookingService` | Buat booking (guest/member/manual), expire |
| `PricingService` | Kalkulasi harga per malam + rate override + diskon |
| `MidtransPaymentService` | Snap token, webhook, rekonsiliasi |
| `LoyaltyPointService` | Earn, redeem, reverse, expire poin (FIFO) |
| `PromotionService` | Validasi promo, reserve/consume/release quota |
| `BookingAccessService` | Akses token guest (tanpa login) |
| `BookingClaimService` | Member klaim guest booking via email |
| `BookingChangeService` | Preview & proses perubahan booking |
| `InvoiceService` | Generate nomor & PDF invoice |
| `DocumentSequenceService` | Sequence unik kode booking & nomor invoice |
| `ImageUploadService` | Upload + resize gambar (thumb/medium/large) |

### Authorization

**Tidak ada Laravel Policy.** Authorization via:
1. Middleware: `auth`, `auth:admin`, `verified`, `guest:admin`
2. Inline check di controller/service (contoh: ownership check di `BookingAccessService`)

---

## 6. DATABASE — KONVENSI KRITIS

```
Harga/amount   → BIGINT UNSIGNED (integer IDR, tidak ada float)
Tanggal        → DATE kolom 'check_in', 'check_out' (bukan datetime)
Interval       → Half-open [check_in, check_out) — check_out TIDAK termasuk
Overlap query  → existing.check_in < new.check_out AND existing.check_out > new.check_in
Phone          → String '628xxxxxxxxx' setelah PhoneNormalizer::normalize()
Booking code   → 'BKG-YYYYMM-XXXX' dari DocumentSequenceService
Invoice number → 'INV-YYYYMM-XXXX' dari DocumentSequenceService
```

**37 migration files.** Tabel utama:

```
users, admins, social_accounts
room_types, rooms, room_type_facility (pivot), room_images, facilities
bookings, booking_status_histories, booking_night_prices, booking_addons
booking_change_requests, document_sequences
payments, payment_webhook_events
loyalty_transactions, loyalty_point_allocations
promotions, promotion_usages
refunds, expenses, room_blocks, rate_overrides, addons
reviews, faqs, contact_messages, nearby_places, galleries
settings, policy_versions, audit_logs
cache, jobs (Laravel defaults)
```

---

## 7. ENUM STATUS — REFERENSI CEPAT

### BookingStatus (`app/Enums/BookingStatus.php`)

```
pending_payment → confirmed → checked_in → checked_out → completed
               ↘ expired                                 (terminal)
               ↘ cancelled                               (terminal)
    confirmed  → no_show                                 (terminal)
```

- **Blocking** (mencegah booking baru): `pending_payment`, `confirmed`, `checked_in`
- **Terminal** (tidak bisa transisi lagi): `completed`, `cancelled`, `expired`, `no_show`
- Transisi tidak valid → throw `InvalidStatusTransitionException`
- Gunakan: `$booking->status->canTransitionTo(BookingStatus::CheckedIn)`

### PaymentStatus

`unpaid → pending → paid` (atau `failed`/`expired`/`refunded`/`partial_refund`)

Payment status dan booking status adalah **dua hal yang berbeda**.

### LoyaltyTransactionType

`earn` (positif), `redeem` (negatif), `expire` (negatif), `adjustment` (±), `reversal` (positif)

### PromotionUsageStatus

`reserved` (saat booking dibuat) → `consumed` (saat paid) atau `released` (saat cancel/expired)

---

## 8. ALUR BOOKING — RINGKASAN

```
1. GET /ketersediaan         → AvailabilityService::searchAvailableRoomTypes()
2. GET /checkout             → PricingService::calculateQuote() [server-side]
3. POST /booking             → BookingService::createGuestBooking() atau createMemberBooking()
   └─ DB::transaction()
      ├─ Idempotency check (lockForUpdate)
      ├─ findAndLockRoom() → Room::lockForUpdate()
      ├─ assertRoomAvailableForBooking() [authoritative check]
      ├─ PricingService::calculateQuote() [harga dihitung ulang di server]
      ├─ Booking::create() + BookingNightPrice::insert() + BookingAddon::insert()
      └─ BookingStatusHistory::create()
   └─ Event BookingCreated::dispatch() [di luar transaction]
4. GET /booking/{code}/bayar → MidtransPaymentService::createOrResumePayment()
5. POST /webhook/midtrans    → MidtransPaymentService::handleWebhook()
   └─ signature verify + amount check + idempotency
   └─ booking: pending_payment → confirmed
   └─ PromotionService::consumeForBooking()
   └─ Event PaymentConfirmed::dispatch() [DB::afterCommit]
```

---

## 9. ATURAN KEAMANAN — JANGAN DILANGGAR

Ini bukan saran — ini aturan bisnis yang menentukan integritas data:

1. **Harga selalu dihitung di backend.** `PricingService::calculateQuote()` dipanggil ulang di dalam `createBooking()` — bukan dari input form.

2. **Webhook adalah satu-satunya sumber kebenaran pembayaran.** Callback JavaScript Midtrans **bukan** bukti bayar.

3. **Double booking dicegah dengan transaction + pessimistic lock:**
   ```php
   DB::transaction(function() {
       $room = Room::where('id', $roomId)->lockForUpdate()->first();
       $this->availability->assertRoomAvailableForBooking($room->id, ...);
       // ... baru buat booking
   });
   ```

4. **Webhook Midtrans wajib signature verification** (SHA-512: order_id + status_code + gross_amount + server_key).

5. **Webhook idempotent** — duplikat webhook return sukses tanpa side effect ganda. Cek `PaymentWebhookEvent.deduplication_key`.

6. **Loyalty menggunakan idempotency key** — tidak bisa award poin 2x untuk booking yang sama. Key: `"earn:booking:{$id}"`.

7. **Tidak ada hard-delete** untuk booking, payment, loyalty transaction — data finansial permanen.

8. **Secret hanya dari `.env`** — tidak hardcode, tidak di tabel `settings`, tidak di source code.

9. **Event di-dispatch di luar `DB::transaction()`** — gunakan `DB::afterCommit()` atau dispatch setelah block transaction agar email tidak terkirim jika transaction rollback.

10. **Klaim booking hanya via email terverifikasi** — tidak boleh berdasarkan nama atau nomor WhatsApp saja.

---

## 10. SISTEM LOYALTY — RINGKASAN

**Earn:**
```php
poin = floor(eligible_loyalty_amount / 1000)  // Rp1.000 = 1 poin
// Hanya setelah status: Completed
// Hanya source: website, whatsapp, walk_in (OTA tidak eligible)
// Idempotency: "earn:booking:{$booking->id}"
```

**Redeem:**
```php
max_discount = floor(subtotal * 20 / 100)       // max 20% subtotal
actual_discount = min(points * 50, max_discount) // 1 poin = Rp50
// Minimum: 100 poin
// FIFO: lot dengan expires_at terdekat duluan
// Promo TIDAK bisa digabung dengan poin (V1)
```

**Ledger:** Tabel `loyalty_transactions` adalah sumber kebenaran saldo. `users.loyalty_balance_cache` hanya cache display.

**Expiry:** 18 bulan dari tanggal earn (configurable via `LOYALTY_EXPIRY_MONTHS`).

---

## 11. KALKULASI HARGA

```php
// PricingService::calculateQuote() return:
[
    'nights' => int,
    'price_per_night' => int,        // base_price (backward compat)
    'night_prices' => [              // breakdown per malam
        ['date' => 'YYYY-MM-DD', 'price' => int, 'label' => string|null],
    ],
    'subtotal' => int,
    'promotion_discount' => int,
    'points_discount' => int,
    'points_redeemed' => int,
    'total_amount' => int,
    'eligible_loyalty_amount' => int,
    'promotion' => Promotion|null,
]

total = max(0, subtotal - promotion_discount - points_discount)
```

**Rate Override:** Tabel `rate_overrides` menyimpan harga per tanggal per room_type. Override diterapkan per malam saat `calculateQuote()`. Snapshot disimpan ke `booking_night_prices` saat booking dibuat.

---

## 12. SISTEM EMAIL

**Events → Listeners (async, ShouldQueue):**

```
BookingCreated    → SendBookingConfirmationListener → BookingConfirmationMail
PaymentConfirmed  → SendPaymentSuccessListener      → PaymentSuccessMail
BookingCancelled  → SendBookingCancelledListener    → BookingCancelledMail
```

**Scheduled Commands:**
- `booking:send-checkin-reminders` — daily 09:00, target: confirmed + check_in besok + belum kirim
- `booking:send-post-checkout-emails` — daily 10:00, target: checked_out kemarin + belum kirim

**Idempotency email:** Kolom timestamp di tabel `bookings`:
- `confirmation_email_sent_at`, `payment_email_sent_at`, `reminder_email_sent_at`
- `checkout_email_sent_at`, `cancellation_email_sent_at`

Listener cek kolom ini sebelum kirim — aman diretry berkali-kali.

---

## 13. KONVENSI KODE

### Naming

```
Controller  → PascalCase + Controller  (RoomTypeController)
Model       → PascalCase singular      (RoomType, Booking)
Service     → PascalCase + Service     (AvailabilityService)
Form Request→ PascalCase + Request     (StoreBookingRequest)
Enum        → PascalCase singular      (BookingStatus)
View folder → kebab-case, dot notation (admin.rooms.index)
Route name  → dot notation             (admin.rooms.store)
DB table    → snake_case plural        (room_types, booking_status_histories)
DB column   → snake_case               (check_in, total_amount)
Config key  → snake_case               (earn_divisor)
```

### Format Rupiah & Tanggal

```php
// Rupiah (Blade & PHP)
'Rp' . number_format($amount, 0, ',', '.')   // → Rp150.000

// Tanggal (Carbon)
$date->format('d M Y')                        // → 25 Agu 2026
$datetime->format('d M Y, H:i') . ' WITA'    // → 25 Agu 2026, 14:00 WITA
```

### Harga di Blade

```blade
Rp{{ number_format($booking->total_amount, 0, ',', '.') }}
{{-- atau via accessor: --}}
{{ $booking->formatted_total }}
```

---

## 14. ROUTE MAP RINGKAS

**Public (tanpa auth):**
```
GET  /                          home
GET  /kamar                     rooms.index
GET  /kamar/{slug}              rooms.show
GET  /ketersediaan              availability.search
GET  /checkout                  booking.checkout
POST /booking                   booking.store           [throttle: 5/mnt]
GET  /booking/{code}/konfirmasi booking.confirmation
GET  /cek-booking               booking.verify.form
POST /cek-booking               booking.verify          [throttle: 10/mnt]
GET  /booking/{code}/detail     booking.guest.detail
GET  /booking/{code}/bayar      booking.pay             [throttle: 10/mnt]
GET  /booking/{code}/selesai    booking.finish
GET  /booking/{code}/invoice    booking.invoice
POST /webhook/midtrans          webhook.midtrans        [no CSRF, throttle: 60/mnt]
GET  /auth/google               auth.google
GET  /tentang /lokasi /kebijakan /hubungi /galeri /faq /sekitar
```

**Member (prefix /member, auth + verified):**
```
GET  /member/dashboard
GET  /member/bookings
GET  /member/bookings/{id}
PATCH /member/bookings/{id}/cancel
GET  /member/profile
GET  /member/claim
POST /member/claim/{booking}
GET  /member/points
GET  /member/reviews/create/{booking}
POST /member/reviews            [throttle: 5/60mnt]
GET  /member/bookings/{id}/change
POST /member/bookings/{id}/change
```

**Admin (prefix /admin, auth:admin):**
```
GET/POST /admin/login
POST     /admin/logout
GET      /admin/dashboard
resource /admin/room-types      (no destroy)
resource /admin/rooms           (no destroy)
resource /admin/facilities      (no show)
resource /admin/promotions      (no show)
resource /admin/addons          (no show)
resource /admin/expenses        (no show)
resource /admin/faqs            (no show)
resource /admin/nearby-places   (full)
GET/PUT  /admin/settings/{group}
GET      /admin/calendar + /admin/calendar/data
GET/POST/PATCH /admin/bookings  (+ status actions)
GET      /admin/reports/revenue|occupancy|profit|sources  (+ exports)
GET      /admin/loyalty + /admin/loyalty/{user}
POST     /admin/loyalty/{user}/adjust
```

---

## 15. FORM REQUESTS

Setiap endpoint yang menerima input **wajib** menggunakan Form Request. Validasi tidak boleh inline di controller body.

File ada di `app/Http/Requests/`:
- Root: `StoreContactMessageRequest`, `StoreFaqRequest`, `StoreNearbyPlaceRequest`, `StoreRateOverrideRequest`, `StoreReviewRequest`, `UpdateFaqRequest`, `UpdateNearbyPlaceRequest`, `UpdateRateOverrideRequest`
- `Admin/`: `AdminLoginRequest`, `StoreExpenseRequest`, `StoreFacilityRequest`, `StorePromotionRequest`, `StoreRoomBlockRequest`, `StoreRoomRequest`, `StoreRoomTypeRequest`, `UpdateFacilityRequest`, `UpdateRoomRequest`, `UpdateRoomTypeRequest`
- `Member/`: `StoreBookingChangeRequest`

---

## 16. BLADE COMPONENTS

Tersedia di `resources/views/components/`:

```blade
<x-alert />             ← Flash message / alert
<x-badge />             ← Label badge
<x-button />            ← Tombol standar
<x-confirm-modal />     ← Modal konfirmasi dengan Alpine.js
<x-empty-state />       ← Tampilan data kosong
<x-form-error />        ← Error validasi form
<x-loading-button />    ← Tombol dengan loading state
<x-password-input />    ← Input password + toggle show/hide
<x-star-rating />       ← Bintang rating SVG (param: $rating float)
<x-status-badge />      ← Badge status booking (warna dari Enum)
<x-toast />             ← Toast notification
<x-whatsapp-link />     ← Link WhatsApp via wa.me
```

---

## 17. TIGA LAYOUT BLADE

```blade
@extends('layouts.public')   ← Navbar + footer publik
@extends('layouts.member')   ← Nav member
@extends('layouts.admin')    ← Sidebar + topbar admin
```

---

## 18. SUPPORT CLASSES PENTING

```php
// Normalisasi nomor telepon Indonesia
App\Support\Phone\PhoneNormalizer::normalize('+62 812-3456-7890')
// → '628123456789'

// Generate WhatsApp URL
App\Support\WhatsApp::url('628123456789', 'Halo saya ingin bertanya...')
// → 'https://wa.me/628123456789?text=Halo...'

// Slot waktu kedatangan
App\Support\ArrivalTimeSlots::generate()
// → ['14:00' => '14:00', '14:30' => '14:30', ..., 'unknown' => 'Belum pasti...']
```

---

## 19. KONFIGURASI BISNIS

```php
// config/booking.php
'hold_minutes'     => 30        // Payment deadline: 30 menit dari booking dibuat
'check_in_time'    => '14:00'
'check_out_time'   => '12:00'
'latest_arrival'   => '23:30'
'eligible_sources' => ['website', 'whatsapp', 'walk_in']

// config/loyalty.php
'earn_divisor'           => 1000  // Rp1.000 = 1 poin
'point_value'            => 50    // 1 poin = Rp50
'min_redeem'             => 100   // Minimum 100 poin
'max_redemption_percent' => 20    // Max 20% dari subtotal
'expiry_months'          => 18    // Poin expired setelah 18 bulan

// config/midtrans.php
'is_production' => false  // DEFAULT SANDBOX — production butuh config eksplisit
```

Semua nilai ini configurable via `.env`. Jangan hardcode.

---

## 20. APA YANG SUDAH ADA vs BELUM ADA

### Sudah Diimplementasikan ✅

- Booking engine (guest + member + manual admin)
- Anti-double-booking (5 lapis)
- Midtrans Snap payment + webhook
- Loyalty points (earn/redeem/expire/reversal/FIFO)
- Promosi (percentage + fixed, quota, per-user limit)
- Rate override per tanggal
- Addon layanan tambahan
- Booking change request
- Invoice PDF
- Review tamu (moderated)
- Klaim guest booking oleh member
- Email notifications (konfirmasi, pembayaran, reminder, post-checkout, cancel)
- Admin full CRUD: kamar, fasilitas, promo, addon, pengeluaran, refund
- Admin laporan: revenue, occupancy, profit, sources (CSV export)
- Kalender visual kamar
- Room block (blokir manual)
- Galeri foto (multi-variant)
- FAQ, Kontak, Tempat Sekitar, Kebijakan
- Google OAuth
- Custom error pages (403, 404, 419, 429, 500, 503)

### Belum Ada / Perlu Diperhatikan ⚠️

- Tidak ada Laravel Policy — authorization via middleware + inline checks
- Tidak ada app/Policies/ directory
- Tidak ada Jobs (queue via Listeners + ShouldQueue Mailables)
- Tidak ada API endpoint terpisah (semua web routes)
- Factory hanya untuk sebagian model (diperlukan untuk testing)
- Content produksi (foto kamar, FAQ asli, kebijakan asli) perlu diisi via admin panel

---

## 21. PANDUAN JIKA INGIN MENAMBAH FITUR

### Langkah Standar

1. **Baca source code terkait dulu.** Jangan menebak nama method atau struktur data.
2. **Cari implementasi serupa.** Mis. mau buat CRUD baru → lihat `Admin\FaqController` sebagai contoh.
3. **Buat migration** dengan `down()` yang benar.
4. **Buat Model** + relasi + fillable + casts.
5. **Buat Service** jika ada business logic — jangan taruh di controller.
6. **Buat Form Request** untuk validasi input.
7. **Tambahkan route** di `routes/web.php` dengan nama dot notation.
8. **Buat View** yang `@extends` layout yang sesuai, gunakan components yang ada.

### Aturan Kritis saat Coding

```
✅ Harga SELALU dihitung di server (PricingService)
✅ Webhook SELALU diverifikasi signature-nya
✅ Booking creation SELALU dalam DB::transaction() + lockForUpdate()
✅ Email SELALU di-queue (ShouldQueue)
✅ Event SELALU di-dispatch di luar transaction
✅ Loyalty mutasi SELALU pakai idempotency key
✅ Status transition SELALU via BookingStatus::transitionTo()
✅ Tidak ada magic string — pakai enum value
✅ Amount dibandingkan sebagai integer (bukan float)

❌ JANGAN validasi harga dari input form
❌ JANGAN percaya callback JS Midtrans sebagai bukti bayar
❌ JANGAN buat booking tanpa transaction + lockForUpdate
❌ JANGAN hardcode secret di source code
❌ JANGAN jalankan migrate:fresh tanpa instruksi eksplisit
❌ JANGAN hard-delete booking/payment/loyalty transaction
❌ JANGAN dispatch event di dalam DB::transaction()
❌ JANGAN award poin tanpa cek idempotency key
```

---

## 22. FILE DOKUMENTASI LENGKAP

Jika butuh detail lebih dalam, dokumentasi ada di `docs/`:

| File | Isi |
|---|---|
| `docs/overview.md` | Gambaran umum, stack, config bisnis |
| `docs/architecture.md` | Struktur folder lengkap, konvensi naming |
| `docs/models.md` | Semua 31 model + relasi + casts |
| `docs/controllers.md` | Semua controller + methods |
| `docs/services.md` | Semua 12 service + methods |
| `docs/routes.md` | Semua route dengan method, URI, controller |
| `docs/database.md` | Skema 37 tabel dengan semua kolom |
| `docs/enums.md` | Semua enum + transition rules |
| `docs/events-listeners-mail.md` | Event system + email tracking |
| `docs/views.md` | Semua views + components |
| `docs/commands.md` | Artisan commands + scheduler |
| `docs/dependencies.md` | Package list backend + frontend |
| `docs/features/booking-flow.md` | Deep dive alur booking |
| `docs/features/payment.md` | Deep dive Midtrans integration |
| `docs/features/loyalty.md` | Deep dive loyalty system |
| `docs/features/promotion.md` | Deep dive promotion system |
