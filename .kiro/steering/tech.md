# Technology Stack — Penginapan Kelapa Sawit

## Backend

| Teknologi | Versi | Catatan |
|---|---|---|
| Laravel | 12.x | PHP 8.2 tidak mendukung Laravel 13 (butuh PHP 8.3) |
| PHP | 8.2.16 | NTS, Laragon |
| MySQL | 8.0.30 | InnoDB, foreign keys, Laragon |
| Composer | 2.8.6 | — |

## Frontend

| Teknologi | Kegunaan |
|---|---|
| Blade | Templating |
| Tailwind CSS | Styling (mobile-first) |
| Alpine.js | Interaksi ringan (dropdown, modal, toggle) |
| Vite | Build tool |

Tidak menggunakan React, Vue, Inertia, atau SPA.

## Authentication

- Member: guard `web`, tabel `users`, Laravel Fortify (backend) + custom Blade UI
- Admin: guard `admin`, tabel `admins`, terpisah dari member
- Google OAuth: Laravel Socialite
- Email verification & password reset: mekanisme resmi Laravel

## Payment

- Midtrans Snap (sandbox-first)
- SDK: `midtrans/midtrans-php` (resmi)
- Webhook sebagai sumber kebenaran pembayaran
- Server Key hanya backend, Client Key sesuai mekanisme Snap

## PDF

- `barryvdh/laravel-dompdf` untuk invoice
- Render dari Blade template

## Testing

- PHPUnit (bawaan Laravel)
- Feature test untuk endpoint/workflow
- Unit test untuk business logic (pricing, overlap, transition)
- Critical locking test harus terhadap MySQL, bukan SQLite

## Konfigurasi Penting

- Timezone: `Asia/Makassar`
- Mata uang: IDR, disimpan sebagai integer (BIGINT UNSIGNED)
- Tanggal menginap: DATE, interval `[check_in, check_out)`
- Secret: hanya dari `.env`, tidak di database settings atau source code
- Midtrans: default sandbox, production memerlukan konfigurasi eksplisit

## Dependency Minimal

Jangan menambahkan package yang menyelesaikan kebutuhan yang sama. Utamakan package resmi dan aktif. Jangan menggunakan package Laravel lama yang tidak terpelihara.
