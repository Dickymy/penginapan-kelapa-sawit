# Dependencies — Penginapan Kelapa Sawit

> **Sumber:** `composer.json`, `package.json` — diverifikasi dari file aktual.

---

## Backend (composer.json)

### Production Dependencies

| Package | Versi | Kegunaan |
|---|---|---|
| `php` | `^8.2` | Runtime — PHP 8.2.16 NTS |
| `laravel/framework` | `^12.0` | Framework utama |
| `laravel/tinker` | `^2.10.1` | REPL untuk debugging |
| `laravel/fortify` | `^1.37` | Auth backend (login, register, verify, reset) |
| `laravel/socialite` | `^5.28` | Google OAuth |
| `midtrans/midtrans-php` | `^2.6` | Midtrans Snap payment integration |
| `barryvdh/laravel-dompdf` | `^3.1` | Generate PDF invoice dari Blade |
| `intervention/image` | `3.0` | Resize & generate varian gambar (thumb, medium, large) |
| `league/flysystem-aws-s3-v3` | `3.0` | S3 compatible storage driver |

### Development Dependencies

| Package | Versi | Kegunaan |
|---|---|---|
| `phpunit/phpunit` | `^11.5.3` | Test framework |
| `fakerphp/faker` | `^1.23` | Data dummy untuk factories |
| `laravel/pint` | `^1.13` | Code style fixer (PHP-CS-Fixer wrapper) |
| `laravel/sail` | `^1.41` | Docker dev environment |
| `laravel/pail` | `^1.2.2` | Real-time log viewer |
| `mockery/mockery` | `^1.6` | Mock objects untuk testing |
| `nunomaduro/collision` | `^8.6` | Better error reporting di CLI |

### Dev Script

`composer dev` menjalankan 4 proses secara paralel via `concurrently`:
1. `php artisan serve` — web server
2. `php artisan queue:listen --tries=1` — queue worker (wajib untuk email)
3. `php artisan pail --timeout=0` — log viewer
4. `npm run dev` — Vite dev server

---

## Frontend (package.json)

### devDependencies

| Package | Versi | Kegunaan |
|---|---|---|
| `tailwindcss` | `^4.3.2` | CSS framework (v4 — PurgeCSS otomatis) |
| `@tailwindcss/vite` | `^4.0.0` | Integrasi Tailwind dengan Vite |
| `@tailwindcss/forms` | `^0.5.11` | Reset style untuk form elements |
| `alpinejs` | `^3.15.12` | Interaksi UI ringan (dropdown, modal, toggle) |
| `@alpinejs/focus` | `^3.15.12` | Plugin Alpine — focus management |
| `vite` | `^6.0.11` | Build tool & HMR |
| `laravel-vite-plugin` | `^1.2.0` | Integrasi Vite dengan Laravel |
| `axios` | `^1.7.4` | HTTP client (tersedia tapi minimal digunakan) |
| `concurrently` | `^9.0.1` | Jalankan multiple proses paralel (dipakai di composer dev) |

### dependencies (runtime)

| Package | Versi | Kegunaan |
|---|---|---|
| `@alpinejs/intersect` | `^3.16.2` | Plugin Alpine — IntersectionObserver untuk lazy load |

---

## Catatan Penting

**Prinsip dependency minimal:** Tidak menambahkan package yang menyelesaikan kebutuhan yang sama. Utamakan package resmi dan aktif Laravel.

**Yang sengaja tidak digunakan:**
- React, Vue, Inertia (tidak perlu SPA)
- Livewire (Alpine.js sudah cukup untuk interaksi ringan)
- Spatie Permission (admin guard terpisah lebih sederhana untuk 2 peran)
- Package ORM alternatif (Eloquent sudah cukup)
