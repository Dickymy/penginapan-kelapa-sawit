# SPEC 01 — Project Foundation (Rencana Scope)

> **Status:** Rencana — belum diimplementasikan  
> **Tanggal:** 7 Juli 2026  
> **Dependency:** Audit selesai, Steering selesai  
> **Fase dokumen master:** Fase 0 (task 0.5) + Fase 1

---

## Scope

SPEC 01 mencakup fondasi minimum agar project dapat berjalan dan siap menerima fitur domain (room, booking, payment) pada Spec berikutnya.

### Yang TERMASUK dalam SPEC 01

1. Bootstrap project Laravel 12
2. Konfigurasi environment (timezone, locale, currency)
3. Config domain (`booking.php`, `loyalty.php`, `midtrans.php`)
4. Git initialization + `.gitignore` yang benar
5. Enum/status foundation (semua enum domain)
6. State transition guard (BookingStatus transition validation)
7. Migration fondasi: `users`, `admins`, `social_accounts`
8. Autentikasi member (register, login, logout, email verify, password reset)
9. Autentikasi admin terpisah (guard `admin`, tabel `admins`, login)
10. Layout Blade: public, member, admin (shell UI + komponen dasar)
11. Tailwind CSS + Alpine.js setup
12. Route structure placeholder
13. Test foundation (PHPUnit config, contoh test)
14. Seeder admin development

### Yang TIDAK TERMASUK dalam SPEC 01

- Room type / room / facility / room image (→ SPEC 02)
- Halaman publik dengan data kamar (→ SPEC 02)
- Availability engine (→ SPEC 03)
- Booking workflow (→ SPEC 03)
- Midtrans integration (→ SPEC 04)
- Google OAuth (→ SPEC 05)
- Member dashboard dengan data booking/poin (→ SPEC 05)
- Loyalty system (→ SPEC 06)
- Promotion system (→ SPEC 06)
- Admin calendar / reservation management (→ SPEC 05)
- Reports (→ SPEC 08)

---

## Requirements (Ringkasan)

### REQ-01: Project Bootstrap
- Sebagai developer, project Laravel 12 dapat di-boot dan merespons request.
- `.env.example` berisi semua variable yang diperlukan tanpa secret.
- Timezone bisnis `Asia/Makassar` terkonfigurasi.

### REQ-02: Domain Configuration
- Config `booking.php`: hold_minutes=30, currency=IDR, eligible_sources.
- Config `loyalty.php`: earn_divisor=1000, point_value=50, min_redeem=100, max_percent=20, expiry_months=18.
- Config `midtrans.php`: keys dari env, sandbox default.
- Semua config membaca dari `.env`, tidak hardcode secret.

### REQ-03: Enum Foundation
- Semua status domain tersedia sebagai PHP backed enum.
- BookingStatus memiliki transition map yang validated.
- Invalid transition melempar exception.

### REQ-04: Member Authentication
- Member dapat register dengan nama, email, WhatsApp, password.
- Member dapat login dengan email/password.
- Email verification dikirim saat register.
- Password reset tersedia.
- Inactive member ditolak login.

### REQ-05: Admin Authentication
- Admin login terpisah di route `/admin/login`.
- Guard `admin` menggunakan tabel `admins`.
- Tidak ada public registration admin.
- Admin inactive ditolak login.
- Member tidak dapat mengakses route admin.

### REQ-06: Layout Foundation
- Tiga layout terpisah: public, member, admin.
- Mobile-first responsive.
- Warna: hijau alami, putih, netral hangat.
- Navigasi publik: Beranda, Kamar, Tentang, Lokasi, Kebijakan, Cek Booking, Login.
- Navigasi admin: sidebar dengan menu (item fitur belum jadi disabled).
- Komponen reusable: alert, button, badge, form-error, empty-state.

### REQ-07: Test Foundation
- PHPUnit terkonfigurasi.
- Minimal test: application boots, enum transition, auth register/login.
- Test database terpisah atau RefreshDatabase trait.

---

## Design (Ringkasan)

### Arsitektur

```text
Laravel 12 + PHP 8.2 + MySQL 8.0
├── Blade + Tailwind + Alpine (frontend)
├── Fortify (auth backend, custom UI)
├── Guard web (member) + Guard admin (admin)
├── Backed Enums (semua status)
└── Config-driven business constants
```

### Database (Fase 1)

| Tabel | Kegunaan |
|---|---|
| users | Akun member |
| admins | Akun admin terpisah |
| social_accounts | OAuth linking (struktur siap, OAuth belum aktif) |
| password_reset_tokens | Reset password (Laravel default) |
| sessions | Session driver jika database |
| cache | Cache driver jika database |
| jobs | Queue jika database |

### Auth Flow

- Member: Fortify handles backend → custom Blade views
- Admin: Custom guard + controller, session terpisah atau prefix

### Frontend Build

- Vite + Tailwind CSS + Alpine.js
- Entry: `resources/css/app.css`, `resources/js/app.js`
- Build: `npm run build`

---

## Tasks (Rencana Urutan)

### Task 1: Git Init + Project Bootstrap
- Initialize git repository
- Create Laravel 12 project (composer create-project)
- Configure `.env.example` sesuai master requirements
- Set timezone, locale, currency
- Verify application boots
- **Test:** `php artisan serve` responds, config reads correctly

### Task 2: Domain Config Files
- Create `config/booking.php`
- Create `config/loyalty.php`
- Create `config/midtrans.php`
- All values from env with safe defaults
- **Test:** Config values accessible, no hardcoded secrets

### Task 3: Enum Foundation
- Create all 8 enum files (BookingStatus, PaymentStatus, BookingSource, LoyaltyTransactionType, PromotionType, PromotionUsageStatus, RefundStatus, RoomStatus)
- Implement BookingStatus transition map with validation method
- Add label helpers (Bahasa Indonesia)
- **Test:** Unit test transition valid/invalid

### Task 4: Migration Users + Admins + Social Accounts
- Create users migration sesuai schema dokumen master
- Create admins migration sesuai schema
- Create social_accounts migration sesuai schema
- Create User model dengan fillable/casts
- Create Admin model dengan fillable/casts/Authenticatable
- **Test:** Migration runs, models instantiate

### Task 5: Member Authentication (Fortify)
- Install Laravel Fortify
- Configure Fortify features (registration, email verification, password reset)
- Create custom Blade views: register, login, verify-email, forgot-password, reset-password
- Configure User model (MustVerifyEmail)
- Add WhatsApp normalization
- **Test:** Feature test register, login, logout, verify email, reset password

### Task 6: Admin Authentication
- Configure admin guard in `config/auth.php`
- Create AdminLoginController + Form Request
- Create admin login Blade view
- Create admin auth middleware
- Create admin routes with guard
- Create development admin seeder (env-based)
- **Test:** Admin login, member cannot access admin, admin inactive rejected

### Task 7: Frontend Setup + Layouts
- Configure Tailwind CSS (colors, theme)
- Configure Alpine.js
- Create `layouts/public.blade.php` (header, nav, footer)
- Create `layouts/member.blade.php` (nav, sidebar/menu)
- Create `layouts/admin.blade.php` (sidebar, topbar)
- Create Blade components (alert, button, badge, form-error, empty-state)
- Create placeholder pages: home, login, register, admin dashboard
- **Test:** Frontend builds, pages render without error, mobile responsive

### Task 8: Route Structure + Final Verification
- Organize routes: public, auth, member (guarded), admin (admin guard)
- Verify all route names work
- Run full test suite
- Verify migration status clean
- Verify frontend build
- Document any remaining issues
- **Test:** `php artisan route:list`, `php artisan test`, `npm run build`

---

## Dependency Antar Task

```text
Task 1 (bootstrap)
  └─→ Task 2 (config)
       └─→ Task 3 (enum)
            └─→ Task 4 (migration + model)
                 ├─→ Task 5 (member auth)
                 └─→ Task 6 (admin auth)  [depends on Task 4]
                      └─→ Task 7 (frontend + layout) [depends on Task 5 & 6]
                           └─→ Task 8 (routes + verification)
```

---

## Exit Criteria SPEC 01

- [ ] Laravel 12 project boots tanpa error
- [ ] Git initialized dengan `.gitignore` yang benar
- [ ] Config domain (booking, loyalty, midtrans) tersedia
- [ ] Semua enum domain tersedia dan tested
- [ ] BookingStatus transition validated
- [ ] Member register/login/verify/reset berjalan
- [ ] Admin login terpisah berjalan
- [ ] Guard admin memblokir member
- [ ] Tiga layout (public/member/admin) render tanpa error
- [ ] Frontend build sukses (Tailwind + Alpine)
- [ ] Test suite passes
- [ ] Tidak ada secret di source code
- [ ] Tidak ada fitur domain (room/booking/payment) yang diimplementasikan
