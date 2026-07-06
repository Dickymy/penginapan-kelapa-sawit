# Project Structure & Conventions — Penginapan Kelapa Sawit

## Struktur Folder Utama

```text
app/
├── Enums/              # Backed string enums (BookingStatus, PaymentStatus, dll)
├── Http/
│   ├── Controllers/
│   │   ├── Public/     # Halaman publik (home, room, availability)
│   │   ├── Auth/       # Login, register, OAuth, password reset
│   │   ├── Member/     # Dashboard, booking, poin, profil member
│   │   ├── Admin/      # Seluruh admin operations
│   │   └── Webhook/    # Midtrans webhook
│   ├── Requests/       # Form Request per domain (Booking/, Member/, Admin/)
│   └── Middleware/
├── Models/             # Eloquent models
├── Policies/           # Authorization policies
├── Services/           # Domain services (Availability, Booking, Pricing, dll)
├── Jobs/               # Queue jobs (email, PDF heavy)
├── Console/Commands/   # Artisan commands (expire bookings, expire points)
└── Support/            # Helper classes (Money, Phone, Security)

config/
├── booking.php         # Hold duration, eligible sources
├── loyalty.php         # Earn rate, point value, expiry, min/max
├── midtrans.php        # Server/Client key from env, sandbox flag

resources/views/
├── layouts/            # public.blade.php, member.blade.php, admin.blade.php
├── public/             # Home, room, availability, about, policy
├── auth/               # Login, register, verify, reset
├── member/             # Dashboard, bookings, points, profile
├── admin/              # Admin pages
├── components/         # Reusable Blade components
└── invoices/           # PDF templates

routes/
├── web.php             # Public + member routes
└── admin.php           # Admin routes (jika dipisahkan)

tests/
├── Unit/               # Pricing, overlap, transition, loyalty math
└── Feature/            # HTTP endpoint tests
```

## Naming Conventions

- **Controller:** PascalCase, singular resource + `Controller` (e.g., `RoomTypeController`)
- **Model:** PascalCase, singular (e.g., `RoomType`, `Booking`)
- **Migration:** Laravel default timestamp format
- **Enum:** PascalCase, singular (e.g., `BookingStatus`)
- **Service:** PascalCase + `Service` (e.g., `AvailabilityService`)
- **Form Request:** PascalCase + `Request` (e.g., `StoreBookingRequest`)
- **View:** kebab-case folders, dot notation (e.g., `admin.rooms.index`)
- **Route names:** dot notation (e.g., `admin.rooms.store`)
- **Config keys:** snake_case
- **Database columns:** snake_case
- **Database tables:** snake_case, plural

## Prinsip Controller

Controller harus tipis. Tugas controller:
1. Menerima request
2. Validasi via Form Request
3. Authorization via Policy/Gate
4. Memanggil Service
5. Return response/view/redirect

Controller TIDAK boleh berisi: query overlap, kalkulasi harga, logika loyalty, mapping Midtrans, atau transaction panjang.

## Service Boundary

Satu service = satu domain. Jangan membuat service raksasa.

- `AvailabilityService` — ketersediaan kamar
- `BookingService` — workflow booking
- `PricingService` — kalkulasi harga
- `MidtransPaymentService` — integrasi Midtrans
- `LoyaltyPointService` — poin loyalitas
- `PromotionService` — promo kode
- `InvoiceService` — PDF invoice
- `BookingClaimService` — guest claim

## Enum Usage

Semua status menggunakan PHP backed enum. Jangan menyebarkan magic string. Semua perbandingan status menggunakan enum value.

## Form Request

Setiap endpoint yang menerima input wajib Form Request. Jangan validasi di controller body.

## Authorization

- Member routes: middleware `auth` + Policy ownership check
- Admin routes: middleware `auth:admin` + guard terpisah
- Public routes: tidak memerlukan auth
- Webhook: tanpa session/CSRF, validasi via signature

## Aturan Perubahan

- Jangan menghapus file yang masih direferensikan route, service container, view, atau test.
- Jangan membuat endpoint `update status` generik.
- Aksi status menggunakan method spesifik di service (confirm, expire, cancel, checkIn, dll).
