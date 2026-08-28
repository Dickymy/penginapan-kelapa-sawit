# Architecture — Penginapan Kelapa Sawit

> **Sumber:** Analisis source code aktual, Agustus 2026.

---

## Stack

| Layer | Teknologi | Versi |
|---|---|---|
| Backend | Laravel | 12.x |
| Runtime | PHP (NTS) | 8.2.16 |
| Database | MySQL (InnoDB) | 8.0.30 |
| Auth member | Laravel Fortify + custom Blade UI | ^1.37 |
| OAuth | Laravel Socialite | ^5.28 |
| Payment | midtrans/midtrans-php | ^2.6 |
| PDF | barryvdh/laravel-dompdf | ^3.1 |
| Image processing | intervention/image | 3.0 |
| S3 storage | league/flysystem-aws-s3-v3 | 3.0 |
| Frontend | Blade + Tailwind CSS | v4.3.2 |
| Interaksi UI | Alpine.js | v3.15.12 |
| Build | Vite | 6.x |
| Queue | Database queue | — |

**Tidak menggunakan:** React, Vue, Inertia, SPA framework.

---

## Struktur Folder

```
app/
├── Actions/Fortify/        # CreateNewUser, UpdateUserProfileInformation, dll
├── Console/Commands/       # Artisan commands (expire, reminders, reconcile)
├── Enums/                  # Backed string enums (BookingStatus, dll)
├── Events/                 # BookingCreated, BookingCancelled, PaymentConfirmed
├── Exceptions/             # InvalidStatusTransitionException, RoomNotAvailableException
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin CRUD controllers + Auth/LoginController
│   │   ├── Auth/           # GoogleController (OAuth)
│   │   ├── Member/         # Dashboard, bookings, profile, claim, points, reviews
│   │   ├── Public/         # Homepage, rooms, availability, booking, payment, pages
│   │   └── Webhook/        # MidtransWebhookController
│   ├── Requests/           # Form Request per domain
│   └── Responses/          # LoginResponse (Fortify custom)
├── Listeners/              # SendBookingConfirmationListener, dll
├── Mail/                   # Mailable classes (semua ShouldQueue)
├── Models/                 # Eloquent models
├── Providers/              # AppServiceProvider, FortifyServiceProvider
├── Services/               # Domain services
└── Support/
    ├── Phone/              # PhoneNormalizer
    ├── ArrivalTimeSlots.php
    ├── WhatsApp.php        # WhatsApp URL generator
    └── WhatsAppHelper.php

config/
├── booking.php             # Hold duration, eligible sources, check-in/out time
├── loyalty.php             # Earn rate, point value, expiry, min/max redeem
└── midtrans.php            # Server/Client key, sandbox flag

resources/views/
├── layouts/                # public.blade.php, member.blade.php, admin.blade.php
│   └── partials/           # admin-nav.blade.php
├── public/                 # Homepage, rooms, availability, booking, pages
├── auth/                   # login, register, verify-email, forgot/reset-password
├── member/                 # dashboard, bookings, profile, claim, points, reviews
├── admin/                  # All admin pages
├── components/             # Reusable Blade components
├── mail/                   # Email layouts + templates
├── emails/                 # Email templates tambahan
├── invoices/               # PDF template (booking.blade.php)
└── errors/                 # 403, 404, 419, 429, 500, 503

routes/
└── web.php                 # Satu file: public + auth + member + admin + webhook

database/
├── migrations/             # 37 migration files
├── factories/              # Database factories (UserFactory + tambahan)
└── seeders/                # Database seeders

tests/
├── Unit/                   # Pricing, overlap, transition, loyalty math
└── Feature/                # HTTP endpoint tests
```

---

## Authentication

### Member (Guard: `web`)
- Tabel: `users`
- Backend: Laravel Fortify
- UI: Custom Blade views
- Custom login response: `app/Http/Responses/LoginResponse.php` — redirect ke `/` dengan toast

### Admin (Guard: `admin`)
- Tabel: `admins`
- Backend: Custom `Admin\Auth\LoginController`
- UI: Custom Blade views di `resources/views/admin/auth/`
- Sepenuhnya terpisah dari member — session berbeda, route berbeda

### Google OAuth
- Library: Laravel Socialite
- Provider: Google
- Linked via tabel `social_accounts`
- Routes: `/auth/google`, `/auth/google/callback`

---

## Authorization

**Tidak menggunakan Laravel Policy.** `app/Policies/` tidak ada.

Authorization dilakukan via:
1. **Middleware:** `auth` (member), `auth:admin` (admin), `verified` (email verification), `guest:admin`
2. **Inline checks:** Cek kepemilikan data di controller atau service (contoh: `BookingAccessService`)
3. **Service validation:** `BookingClaimService` validasi email cocok sebelum klaim

---

## Service Architecture

Controller memanggil service. Service adalah satu-satunya tempat business logic.

```
Controller
    ↓ (Form Request validation)
    ↓ (Guard check via middleware)
Service
    ↓ DB::transaction() untuk operasi kritis
    ↓ lockForUpdate() sebelum overlap check
    ↓ Event dispatch (di luar transaction)
Response/View
```

**Service dependencies:**

```
BookingService
  ├── AvailabilityService
  ├── PricingService
  │   ├── PromotionService
  │   └── LoyaltyPointService
  ├── DocumentSequenceService
  └── LoyaltyPointService

MidtransPaymentService (standalone)
InvoiceService (standalone)
ImageUploadService (standalone)
BookingAccessService (standalone)
BookingClaimService (standalone)
BookingChangeService (standalone)
```

---

## Event System

```
BookingCreated → SendBookingConfirmationListener → BookingConfirmationMail
BookingCancelled → SendBookingCancelledListener → BookingCancelledMail
PaymentConfirmed → SendPaymentSuccessListener → PaymentSuccessMail
```

Semua listener adalah `ShouldQueue` — diproses async via queue database.

Event di-dispatch **setelah** transaction commit (bukan di dalam `DB::transaction()`).

---

## Queue

- Driver: `database` (tabel `jobs`)
- Worker: `php artisan queue:listen --tries=1` (dijalankan via `composer dev`)
- Semua email di-queue via Mailable + ShouldQueue
- Re-throw exception di listener agar queue retry

---

## Scheduler

**File:** `routes/console.php`

| Command | Jadwal | Keterangan |
|---|---|---|
| `booking:expire-pending` | — | Expire booking yang melewati payment_expires_at |
| `booking:send-checkin-reminders` | Daily 09:00 | Email pengingat H-1 check-in |
| `booking:send-post-checkout-emails` | Daily 10:00 | Email post-checkout |
| `loyalty:expire-points` | Daily | Expire poin loyalty kadaluarsa |
| `payments:reconcile` | Berkala | Rekonsiliasi status payment Midtrans |

---

## Support Classes

**Folder:** `app/Support/`

| Class | File | Kegunaan |
|---|---|---|
| `PhoneNormalizer` | `Support/Phone/PhoneNormalizer.php` | Normalisasi nomor telepon ke format 628xxx |
| `WhatsApp` | `Support/WhatsApp.php` | Generate wa.me URL, validasi nomor Indonesia |
| `WhatsAppHelper` | `Support/WhatsAppHelper.php` | Helper tambahan WhatsApp |
| `ArrivalTimeSlots` | `Support/ArrivalTimeSlots.php` | Generate slot waktu kedatangan dari config (14:00–23:30 per 30 menit + "Belum pasti") |

---

## Konvensi Naming

| Jenis | Konvensi | Contoh |
|---|---|---|
| Controller | PascalCase + Controller | `RoomTypeController` |
| Model | PascalCase singular | `RoomType`, `Booking` |
| Service | PascalCase + Service | `AvailabilityService` |
| Form Request | PascalCase + Request | `StoreBookingRequest` |
| Enum | PascalCase singular | `BookingStatus` |
| View folder | kebab-case, dot notation | `admin.rooms.index` |
| Route name | dot notation | `admin.rooms.store` |
| DB table | snake_case plural | `room_types`, `booking_status_histories` |
| DB column | snake_case | `check_in`, `total_amount` |
| Config key | snake_case | `earn_divisor` |
| Migration | Laravel timestamp format | `2026_07_07_024221_create_room_types_table.php` |

---

## Format Data

| Data | Format | Keterangan |
|---|---|---|
| Harga/Amount | `BIGINT UNSIGNED` integer | Rupiah tanpa desimal |
| Tanggal check-in/out | `DATE` | `YYYY-MM-DD` |
| Interval menginap | Half-open `[check_in, check_out)` | check_out = hari pertama tidak menginap |
| Nomor WA | `628xxxxxxxxx` | Setelah `PhoneNormalizer::normalize()` |
| Format display Rupiah | `'Rp' . number_format($amount, 0, ',', '.')` | Mis. `Rp150.000` |
| Format display tanggal | `->format('d M Y')` | Mis. `25 Agu 2026` |
| Format display datetime | `->format('d M Y, H:i') . ' WITA'` | Via Carbon |
| Timezone | `Asia/Makassar` (WITA, UTC+8) | Di `config/app.php` |
