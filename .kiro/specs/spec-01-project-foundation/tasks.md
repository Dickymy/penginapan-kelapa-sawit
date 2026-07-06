# SPEC 01 — Project Foundation: Tasks

> **Status:** Ready for implementation  
> **Dependency:** Audit selesai, Steering selesai, Requirements & Design approved

---

## Task 1: Git Init & Laravel 12 Bootstrap

- [ ] Initialize git repository di workspace
- [ ] Create Laravel 12 project via `composer create-project laravel/laravel . "^12.0"`
- [ ] Verify `php artisan --version` shows Laravel 12.x
- [ ] Configure `.env.example` dengan semua variable dari design (APP_TIMEZONE=Asia/Makassar, DB, MIDTRANS placeholders, GOOGLE placeholders, MAIL, DEV_ADMIN)
- [ ] Copy `.env.example` ke `.env` dan generate app key
- [ ] Set `APP_TIMEZONE=Asia/Makassar` di `config/app.php`
- [ ] Verify `php artisan serve` returns HTTP 200
- [ ] Commit: "feat: bootstrap Laravel 12 project"

**Depends on:** Nothing  
**REQ traceability:** REQ-1.1, REQ-1.2, REQ-1.3  
**Verification:** Application boots, config timezone correct, git initialized

---

## Task 2: Domain Config Files

- [ ] Create `config/booking.php` (hold_minutes, currency, eligible_sources, check_in_time, check_out_time)
- [ ] Create `config/loyalty.php` (earn_divisor, point_value, min_redeem, max_redemption_percent, expiry_months)
- [ ] Create `config/midtrans.php` (server_key, client_key, is_production, is_sanitized, is_3ds)
- [ ] Add corresponding env placeholders to `.env.example` if not yet present
- [ ] Verify `config('booking.hold_minutes')` returns 30
- [ ] Verify `config('midtrans.is_production')` returns false
- [ ] Commit: "feat: add domain config files (booking, loyalty, midtrans)"

**Depends on:** Task 1  
**REQ traceability:** REQ-2.1  
**Verification:** Config values accessible via artisan tinker, no hardcoded secrets

---

## Task 3: Enum Foundation

- [ ] Create `app/Enums/BookingStatus.php` with all values, labels, transition map, canTransitionTo()
- [ ] Create `app/Enums/PaymentStatus.php` with values and labels
- [ ] Create `app/Enums/BookingSource.php` with values and labels
- [ ] Create `app/Enums/LoyaltyTransactionType.php` with values and labels
- [ ] Create `app/Enums/PromotionType.php` with values and labels
- [ ] Create `app/Enums/PromotionUsageStatus.php` with values and labels
- [ ] Create `app/Enums/RefundStatus.php` with values and labels
- [ ] Create `app/Enums/RoomStatus.php` with values and labels
- [ ] Create `app/Exceptions/InvalidStatusTransitionException.php`
- [ ] Create `tests/Unit/Enums/BookingStatusTest.php` — test valid transitions, invalid transitions, terminal states, labels
- [ ] Run tests, all pass
- [ ] Commit: "feat: add domain enums with transition validation"

**Depends on:** Task 1  
**REQ traceability:** REQ-3.1, REQ-3.2  
**Verification:** Unit tests pass for transition logic

---

## Task 4: Foundation Migrations & Models

- [ ] Modify default `create_users_table` migration to match schema (add whatsapp, avatar_path, avatar_url, loyalty_balance_cache, is_active, last_login_at; allow password nullable)
- [ ] Create `create_admins_table` migration matching schema
- [ ] Create `create_social_accounts_table` migration matching schema
- [ ] Update `app/Models/User.php` — fillable, casts, email normalization mutator, MustVerifyEmail
- [ ] Create `app/Models/Admin.php` — Authenticatable, fillable, casts
- [ ] Create `app/Models/SocialAccount.php` — fillable, casts, belongsTo User
- [ ] Run `php artisan migrate` (fresh start, project baru)
- [ ] Verify all tables created correctly
- [ ] Commit: "feat: foundation migrations and models (users, admins, social_accounts)"

**Depends on:** Task 1  
**REQ traceability:** REQ-4.1, REQ-5.1 (database layer)  
**Verification:** Migration succeeds, models instantiate, relationships work

---

## Task 5: Member Authentication (Fortify)

- [ ] Install `laravel/fortify` via Composer
- [ ] Publish Fortify config and service provider
- [ ] Register FortifyServiceProvider
- [ ] Configure Fortify features: registration, email verification, password reset, update password
- [ ] Create `app/Actions/Fortify/CreateNewUser.php` with validation (name, email unique normalized, whatsapp normalized, password confirmed min 8)
- [ ] Create `app/Actions/Fortify/UpdateUserPassword.php`
- [ ] Create `app/Actions/Fortify/ResetUserPassword.php`
- [ ] Configure Fortify views in `FortifyServiceProvider::boot()` to return custom Blade views
- [ ] Create auth Blade views: `resources/views/auth/register.blade.php`, `login.blade.php`, `forgot-password.blade.php`, `reset-password.blade.php`, `verify-email.blade.php`
- [ ] Add is_active check: customize authentication logic to reject inactive users
- [ ] Add last_login_at update on successful login (via Login event listener or Fortify callback)
- [ ] Create `app/Support/Phone/PhoneNormalizer.php` — normalize WhatsApp to 62-prefixed digits
- [ ] Create `tests/Unit/Support/PhoneNormalizerTest.php`
- [ ] Create `tests/Feature/Auth/RegistrationTest.php` — register success, validation failures, duplicate email
- [ ] Create `tests/Feature/Auth/LoginTest.php` — login success, wrong password, inactive user rejected
- [ ] Run tests, all pass
- [ ] Commit: "feat: member authentication with Fortify"

**Depends on:** Task 4  
**REQ traceability:** REQ-4.1, REQ-4.2, REQ-4.3, REQ-4.4  
**Verification:** Feature tests pass, register/login/verify/reset work

---

## Task 6: Admin Authentication

- [ ] Add admin guard and provider to `config/auth.php`
- [ ] Create `app/Http/Controllers/Admin/Auth/LoginController.php` — showLoginForm, login, logout
- [ ] Create `app/Http/Requests/Admin/AdminLoginRequest.php` — email required, password required, rate limit
- [ ] Create `resources/views/admin/auth/login.blade.php`
- [ ] Add admin routes in `routes/web.php` (prefix admin, name admin.)
- [ ] Create `app/Http/Middleware/RedirectIfNotAdmin.php` or use `auth:admin` middleware
- [ ] Ensure admin login checks `is_active`
- [ ] Update `last_login_at` on admin login
- [ ] Create `database/seeders/AdminSeeder.php` — reads DEV_ADMIN_NAME, DEV_ADMIN_EMAIL, DEV_ADMIN_PASSWORD from env; aborts if empty
- [ ] Register AdminSeeder in DatabaseSeeder
- [ ] Create `tests/Feature/Admin/AdminAuthTest.php` — login success, wrong password, inactive rejected, member cannot access admin route, guest redirected to admin login
- [ ] Run tests, all pass
- [ ] Commit: "feat: admin authentication with separate guard"

**Depends on:** Task 4  
**REQ traceability:** REQ-5.1, REQ-5.2, REQ-5.3  
**Verification:** Feature tests pass, guard isolation confirmed

---

## Task 7: Frontend Setup & Layouts

- [ ] Install npm dependencies (`npm install`)
- [ ] Configure `tailwind.config.js` — colors (primary green, neutral warm), content paths
- [ ] Configure `postcss.config.js`
- [ ] Update `resources/css/app.css` — Tailwind directives (@tailwind base/components/utilities) + custom utilities
- [ ] Update `resources/js/app.js` — import Alpine.js, initialize
- [ ] Update `vite.config.js` jika perlu
- [ ] Create `resources/views/layouts/public.blade.php` — header, nav (responsive hamburger), content slot, footer
- [ ] Create `resources/views/layouts/member.blade.php` — nav with member menu, content slot
- [ ] Create `resources/views/layouts/admin.blade.php` — sidebar (collapsible), topbar, content slot
- [ ] Create Blade components:
  - [ ] `resources/views/components/alert.blade.php` (type: success/error/warning/info)
  - [ ] `resources/views/components/button.blade.php` (variant: primary/secondary/danger)
  - [ ] `resources/views/components/badge.blade.php` (color prop)
  - [ ] `resources/views/components/form-error.blade.php` (field prop)
  - [ ] `resources/views/components/empty-state.blade.php` (message, icon)
- [ ] Create placeholder pages: `resources/views/public/home.blade.php`, `resources/views/member/dashboard.blade.php`, `resources/views/admin/dashboard.blade.php`
- [ ] Update auth views (Task 5) to use public layout
- [ ] Update admin login view (Task 6) to use minimal/admin layout
- [ ] Verify `npm run build` succeeds without error
- [ ] Verify pages render correctly in browser (manual check)
- [ ] Commit: "feat: frontend setup with Tailwind, Alpine.js, and layouts"

**Depends on:** Task 5, Task 6  
**REQ traceability:** REQ-6.1, REQ-6.2, REQ-6.3, REQ-6.4  
**Verification:** npm run build passes, pages render, mobile responsive

---

## Task 8: Route Structure & Final Verification

- [ ] Organize routes in `routes/web.php`:
  - Public: home route
  - Auth: Fortify handles (configured views)
  - Member: prefix `member`, middleware `auth` + `verified`, name `member.*`
  - Admin: prefix `admin`, guest routes (login), guarded routes (middleware `auth:admin`)
- [ ] Ensure all named routes resolve correctly
- [ ] Run `php artisan route:list` — verify no conflicts or errors
- [ ] Run `php artisan migrate:status` — all migrations ran
- [ ] Run `php artisan test` — all tests pass
- [ ] Run `npm run build` — no errors
- [ ] Verify `.gitignore` includes: .env, vendor/, node_modules/, storage/*.key, public/build/ (atau sesuai convention)
- [ ] Review: no secrets in committed files
- [ ] Final commit: "chore: finalize route structure and verify foundation"
- [ ] Tag/document: SPEC 01 complete

**Depends on:** Task 7  
**REQ traceability:** REQ-7.1, all REQs (integration verification)  
**Verification:** All tests pass, build passes, routes clean, no secrets exposed

---

## Summary

| Task | Scope | Dependencies |
|---|---|---|
| 1 | Git + Laravel bootstrap | — |
| 2 | Config files | Task 1 |
| 3 | Enums + transitions | Task 1 |
| 4 | Migrations + Models | Task 1 |
| 5 | Member auth (Fortify) | Task 4 |
| 6 | Admin auth (guard) | Task 4 |
| 7 | Frontend + Layouts | Task 5, 6 |
| 8 | Routes + Final check | Task 7 |

**Parallelizable:** Task 2, 3, 4 dapat dikerjakan secara paralel setelah Task 1.  
**Sequential:** Task 5 & 6 butuh Task 4. Task 7 butuh Task 5 & 6. Task 8 butuh Task 7.
