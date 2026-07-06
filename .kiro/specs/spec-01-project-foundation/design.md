# SPEC 01 — Project Foundation: Design

> **Referensi:** requirements.md SPEC 01, Master Requirements Fase 0–1, PROJECT_AUDIT.md

---

## 1. Architecture Overview

```text
┌─────────────────────────────────────────────────────┐
│                    Browser                           │
├─────────────────────────────────────────────────────┤
│  Blade + Tailwind CSS + Alpine.js (Vite build)      │
├─────────────────────────────────────────────────────┤
│              Laravel 12.x (PHP 8.2)                 │
│  ┌───────────┐  ┌──────────┐  ┌─────────────────┐  │
│  │  Routes   │→ │Controller│→ │  Form Request   │  │
│  └───────────┘  └──────────┘  └─────────────────┘  │
│                       │                             │
│                       ▼                             │
│  ┌──────────────────────────────────────────────┐   │
│  │           Services (future specs)            │   │
│  └──────────────────────────────────────────────┘   │
│                       │                             │
│                       ▼                             │
│  ┌──────────────────────────────────────────────┐   │
│  │     Eloquent Models + Enums + Policies       │   │
│  └──────────────────────────────────────────────┘   │
│                       │                             │
│                       ▼                             │
│  ┌──────────────────────────────────────────────┐   │
│  │            MySQL 8.0 (InnoDB)                │   │
│  └──────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────┘
```

---

## 2. Database Schema (Fase 1)

### 2.1 `users`

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(191) NOT NULL,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    whatsapp VARCHAR(32) NULL DEFAULT NULL,
    avatar_path VARCHAR(255) NULL DEFAULT NULL,
    avatar_url VARCHAR(500) NULL DEFAULT NULL,
    password VARCHAR(255) NULL DEFAULT NULL,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    loyalty_balance_cache BIGINT NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    last_login_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    UNIQUE KEY users_email_unique (email),
    INDEX users_is_active_index (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.2 `admins`

```sql
CREATE TABLE admins (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(191) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'super_admin',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    last_login_at TIMESTAMP NULL DEFAULT NULL,
    password_changed_at TIMESTAMP NULL DEFAULT NULL,
    remember_token VARCHAR(100) NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    UNIQUE KEY admins_email_unique (email),
    INDEX admins_is_active_role_index (is_active, role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.3 `social_accounts`

```sql
CREATE TABLE social_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    provider VARCHAR(50) NOT NULL,
    provider_user_id VARCHAR(191) NOT NULL,
    provider_email VARCHAR(191) NULL DEFAULT NULL,
    provider_email_verified BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    UNIQUE KEY social_provider_user_unique (provider, provider_user_id),
    UNIQUE KEY social_user_provider_unique (user_id, provider),
    INDEX social_provider_email_index (provider_email),
    CONSTRAINT social_accounts_user_id_foreign
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2.4 Migration Order

1. `create_users_table` (extends Laravel default)
2. `create_admins_table`
3. `create_social_accounts_table`

Laravel default tables (`password_reset_tokens`, `sessions`, `cache`, `jobs`) tetap dibuat oleh migration bawaan framework.

---

## 3. Authentication Design

### 3.1 Guards & Providers

```php
// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'admins' => [
        'driver' => 'eloquent',
        'model' => App\Models\Admin::class,
    ],
],
```

### 3.2 Member Auth (Fortify)

- **Package:** `laravel/fortify`
- **Features enabled:** Registration, Email Verification, Password Reset, Update Password
- **UI:** Custom Blade views (tidak menggunakan starter kit)
- **Login validation:** Email normalized lowercase, password, check `is_active`
- **Registration:** Custom `CreateNewUser` action dengan WhatsApp normalization
- **Session:** Regenerate on login

### 3.3 Admin Auth (Custom)

- **Controller:** `App\Http\Controllers\Admin\Auth\LoginController`
- **Form Request:** `App\Http\Requests\Admin\AdminLoginRequest`
- **Guard:** `admin`
- **Session:** Menggunakan session yang sama namun guard berbeda
- **Route prefix:** `/admin`
- **Middleware stack:** `auth:admin`
- **No registration:** Tidak ada route register publik
- **Seeder:** `AdminSeeder` membaca `DEV_ADMIN_*` env vars

### 3.4 Auth Flow Diagram

```text
Member:
  GET /register → show form
  POST /register → validate → create user → send verify → redirect dashboard
  GET /login → show form
  POST /login → Fortify → check is_active → regenerate session → redirect
  POST /logout → invalidate session → redirect home
  GET /forgot-password → show form
  POST /forgot-password → send reset link
  GET /reset-password/{token} → show form
  POST /reset-password → reset → redirect login

Admin:
  GET /admin/login → show form
  POST /admin/login → validate → check is_active → auth attempt → redirect admin dashboard
  POST /admin/logout → invalidate → redirect admin login
```

---

## 4. Enum Design

### 4.1 Base Pattern

```php
namespace App\Enums;

enum BookingStatus: string
{
    case PendingPayment = 'pending_payment';
    case Confirmed = 'confirmed';
    // ...

    public function label(): string
    {
        return match($this) {
            self::PendingPayment => 'Menunggu Pembayaran',
            self::Confirmed => 'Dikonfirmasi',
            // ...
        };
    }

    public function allowedTransitions(): array
    {
        return match($this) {
            self::PendingPayment => [self::Confirmed, self::Expired, self::Cancelled],
            self::Confirmed => [self::CheckedIn, self::Cancelled, self::NoShow],
            // ...
            default => [], // terminal states
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions());
    }
}
```

### 4.2 Enum List

| Enum | Values |
|---|---|
| BookingStatus | pending_payment, confirmed, checked_in, checked_out, completed, cancelled, expired, no_show |
| PaymentStatus | unpaid, pending, paid, failed, expired, refunded, partial_refund |
| BookingSource | website, whatsapp, booking_com, agoda, traveloka, walk_in, phone, other |
| LoyaltyTransactionType | earn, redeem, expire, adjustment, reversal |
| PromotionType | percentage, fixed |
| PromotionUsageStatus | reserved, consumed, released |
| RefundStatus | requested, processing, succeeded, failed, cancelled |
| RoomStatus | active, inactive, maintenance |

---

## 5. Config Design

### 5.1 `config/booking.php`

```php
return [
    'hold_minutes' => (int) env('BOOKING_HOLD_MINUTES', 30),
    'currency' => 'IDR',
    'eligible_sources' => ['website', 'whatsapp', 'walk_in'],
    'check_in_time' => env('BOOKING_CHECK_IN_TIME', '14:00'),
    'check_out_time' => env('BOOKING_CHECK_OUT_TIME', '12:00'),
];
```

### 5.2 `config/loyalty.php`

```php
return [
    'earn_divisor' => (int) env('LOYALTY_EARN_DIVISOR', 1000),
    'point_value' => (int) env('LOYALTY_POINT_VALUE', 50),
    'min_redeem' => (int) env('LOYALTY_MIN_REDEEM', 100),
    'max_redemption_percent' => (int) env('LOYALTY_MAX_REDEMPTION_PERCENT', 20),
    'expiry_months' => (int) env('LOYALTY_EXPIRY_MONTHS', 18),
];
```

### 5.3 `config/midtrans.php`

```php
return [
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => (bool) env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => (bool) env('MIDTRANS_IS_3DS', true),
];
```

---

## 6. Frontend Architecture

### 6.1 Build Pipeline

```text
resources/
├── css/app.css          → Tailwind directives + custom theme
├── js/app.js            → Alpine.js import
└── views/
    ├── layouts/
    │   ├── public.blade.php
    │   ├── member.blade.php
    │   └── admin.blade.php
    └── components/
        ├── alert.blade.php
        ├── button.blade.php
        ├── badge.blade.php
        ├── form-error.blade.php
        └── empty-state.blade.php

Build: Vite (npm run build / npm run dev)
```

### 6.2 Tailwind Theme

```javascript
// tailwind.config.js
colors: {
    primary: {
        50: '#f0fdf4',   // lightest green
        // ... green natural shades
        600: '#16a34a',  // main primary
        700: '#15803d',
        800: '#166534',
    },
    // neutral warm tones for text/background
}
```

### 6.3 Layout Structure

**Public Layout:**
```text
┌─────────────────────────────┐
│  Header: Logo | Nav | Login │
├─────────────────────────────┤
│                             │
│         @yield('content')   │
│                             │
├─────────────────────────────┤
│  Footer: Info | Links       │
└─────────────────────────────┘
```

**Admin Layout:**
```text
┌──────────┬──────────────────┐
│          │  Topbar: Admin   │
│ Sidebar  ├──────────────────┤
│  Menu    │                  │
│          │  @yield('content')│
│          │                  │
└──────────┴──────────────────┘
```

---

## 7. Route Structure

```php
// routes/web.php

// Public (no auth)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth (Fortify handles most, custom views)
// GET/POST /login, /register, /forgot-password, /reset-password, /email/verify

// Member (auth:web + verified)
Route::middleware(['auth', 'verified'])->prefix('member')->name('member.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // future: bookings, points, profile
});

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest admin routes
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login']);

    // Authenticated admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        // future: rooms, bookings, calendar, etc.
    });
});
```

---

## 8. Model Design

### 8.1 User Model

```php
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'whatsapp', 'password',
        'avatar_path', 'avatar_url', 'is_active', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'loyalty_balance_cache' => 'integer',
            'password' => 'hashed',
        ];
    }

    // Mutator: normalize email to lowercase on set
    // Relationships: hasMany(SocialAccount), hasMany(Booking), hasMany(LoyaltyTransaction)
}
```

### 8.2 Admin Model

```php
class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'is_active', 'last_login_at', 'password_changed_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

### 8.3 SocialAccount Model

```php
class SocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'provider', 'provider_user_id',
        'provider_email', 'provider_email_verified',
    ];

    protected function casts(): array
    {
        return [
            'provider_email_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## 9. Error Handling

- Domain exceptions (e.g., `InvalidStatusTransitionException`) extend base application exception.
- Controllers catch domain exceptions and convert to user-friendly messages.
- Auth errors use generic messages (tidak membocorkan informasi akun).
- Validation errors menggunakan Form Request dan ditampilkan via `<x-form-error>` component.

---

## 10. Test Strategy

| Layer | Tool | Scope |
|---|---|---|
| Unit | PHPUnit | Enum transitions, helpers, normalization |
| Feature | PHPUnit + Laravel HTTP | Auth flows, route protection, middleware |
| Browser | Manual | Responsive, Alpine.js interactions |

### Test Database

- Development/test menggunakan SQLite in-memory untuk kecepatan (non-locking tests).
- Future critical locking tests (Spec 03+) harus terhadap MySQL.

### Test Structure

```text
tests/
├── Unit/
│   ├── Enums/
│   │   └── BookingStatusTest.php
│   └── Support/
│       └── PhoneNormalizerTest.php
└── Feature/
    ├── Auth/
    │   ├── RegistrationTest.php
    │   ├── LoginTest.php
    │   ├── PasswordResetTest.php
    │   └── EmailVerificationTest.php
    └── Admin/
        └── AdminAuthTest.php
```

---

## 11. Security Considerations (Fase 1)

- CSRF: Aktif di seluruh form (Laravel default).
- XSS: Blade `{{ }}` escaped by default.
- Password: Laravel Hash facade (bcrypt/argon2).
- Rate limiting: Login endpoints menggunakan `ThrottleRequests` middleware.
- Session: Regenerate on auth, secure cookie settings di production.
- Mass assignment: Explicit `$fillable` pada semua model.
- Admin isolation: Guard terpisah, route terpisah, middleware terpisah.

---

## 12. Decisions & Trade-offs

| Decision | Rationale |
|---|---|
| Laravel 12 bukan 13 | PHP 8.2 environment tidak mendukung Laravel 13 (butuh 8.3) |
| Fortify bukan Breeze/Jetstream | Kontrol penuh atas UI Blade tanpa scaffolding opinionated |
| Guard admin terpisah | Security boundary, bukan boolean di user table |
| SQLite test default | Kecepatan; MySQL hanya untuk locking tests nanti |
| Social accounts table siap | Struktur dibuat sekarang meski OAuth belum aktif (Spec 05) |
| Enum dengan transition map | Mencegah status transition liar sejak awal |
