# Overview — Penginapan Kelapa Sawit

> **Sumber:** Analisis source code aktual, Agustus 2026.

## Deskripsi Produk

**Penginapan Kelapa Sawit** adalah website penginapan full-stack berbasis Laravel yang mencakup:
- Website publik (informasi, galeri, FAQ, lokasi)
- Booking engine dengan proteksi double-booking 5 lapis
- Integrasi pembayaran Midtrans Snap
- Member area (profil, histori, poin loyalitas)
- Admin management system (reservasi, laporan, konten)

**Lokasi:** Kota Bangun, Kalimantan Timur, Indonesia  
**Timezone bisnis:** `Asia/Makassar` (WITA, UTC+8)  
**Bahasa aplikasi:** Bahasa Indonesia  
**Mata uang:** IDR (disimpan sebagai integer BIGINT UNSIGNED)

---

## Pengguna

| Peran | Akses | Guard |
|---|---|---|
| Guest (tamu tanpa akun) | Website publik, booking, cek booking via token | — |
| Member | Guest + dashboard, histori, poin, claim booking | `web` (tabel `users`) |
| Admin | Seluruh manajemen properti & reservasi | `admin` (tabel `admins`) |

Referensi: `config/auth.php`, `app/Models/User.php`, `app/Models/Admin.php`

---

## Stack Teknologi

| Layer | Teknologi |
|---|---|
| Backend | Laravel 12.x, PHP 8.2.16 |
| Database | MySQL 8.0.30 (InnoDB) |
| Auth member | Laravel Fortify + custom Blade UI |
| Auth admin | Guard terpisah, controller custom |
| OAuth | Laravel Socialite (Google) |
| Payment | Midtrans Snap (`midtrans/midtrans-php ^2.6`) |
| PDF | `barryvdh/laravel-dompdf ^3.1` |
| Image | `intervention/image 3.0` |
| Storage S3 | `league/flysystem-aws-s3-v3 3.0` |
| Frontend | Blade + Tailwind CSS v4 + Alpine.js v3 |
| Build | Vite 6 + laravel-vite-plugin |
| Queue | Database queue (`QUEUE_CONNECTION=database`) |

Referensi: `composer.json`, `package.json`

---

## Prinsip Utama

1. Guest tidak wajib login untuk memesan kamar.
2. Satu booking = satu kamar fisik untuk satu interval menginap.
3. Frontend tidak pernah menjadi sumber kebenaran untuk harga, status pembayaran, atau availability.
4. Webhook Midtrans adalah satu-satunya sumber kebenaran status pembayaran.
5. Integritas data: transaction + pessimistic lock mencegah double booking.
6. Semua secret hanya dari `.env` — tidak hardcode, tidak di tabel `settings`.

---

## Kamar yang Diketahui

- **Tipe:** Twin
- **Kamar fisik:** Twin 01, Twin 02

Data property lain (harga, fasilitas lengkap, kebijakan) dikelola admin via panel.

---

## Konfigurasi Bisnis

| Config | File | Nilai Default |
|---|---|---|
| Hold booking | `config/booking.php` | 30 menit |
| Check-in time | `config/booking.php` | 14:00 |
| Check-out time | `config/booking.php` | 12:00 |
| Latest arrival | `config/booking.php` | 23:30 |
| Eligible loyalty sources | `config/booking.php` | website, whatsapp, walk_in |
| Earn divisor | `config/loyalty.php` | Rp1.000 / poin |
| Point value | `config/loyalty.php` | Rp50 / poin |
| Min redeem | `config/loyalty.php` | 100 poin |
| Max redeem % | `config/loyalty.php` | 20% dari subtotal |
| Expiry poin | `config/loyalty.php` | 18 bulan |
| Midtrans mode | `config/midtrans.php` | Sandbox (default) |
