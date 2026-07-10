# Penginapan Kelapa Sawit

Website resmi **Penginapan Kelapa Sawit** — penginapan di Kota Bangun II, Kutai Kartanegara, Kalimantan Timur.

## Fitur

### Publik
- Beranda dengan pencarian ketersediaan kamar
- Daftar kamar dan detail tipe kamar
- Booking tanpa login (guest booking)
- Pembayaran online via Midtrans Snap
- Cek status booking dengan verifikasi identitas
- Invoice PDF
- Halaman informasi: lokasi, tentang, kebijakan

### Member
- Login/Register (email + Google OAuth)
- Dashboard booking dan histori
- Loyalty points (earn & redeem)
- Claim guest booking ke akun member
- Profil dan pengaturan WhatsApp

### Admin
- Guard terpisah (`admin`)
- Dashboard operasional (check-in/out hari ini, pending payment, okupansi)
- Manajemen reservasi (manual booking, status transitions)
- Manajemen kamar (tipe, kamar fisik, foto, fasilitas)
- Blokir kamar (maintenance/renovasi)
- Promosi dan loyalty points
- Pengeluaran dan laporan (pendapatan, okupansi, laba, sumber booking)
- Kebijakan dan pengaturan properti
- Galeri foto

## Stack Teknologi

| Layer | Teknologi |
|-------|-----------|
| Backend | Laravel 12.x, PHP 8.2+ |
| Database | MySQL 8.0 |
| Frontend | Blade, Tailwind CSS 4, Alpine.js 3 |
| Build | Vite |
| Auth | Laravel Fortify + Socialite (Google OAuth) |
| Payment | Midtrans Snap (`midtrans/midtrans-php`) |
| PDF | `barryvdh/laravel-dompdf` |
| Testing | PHPUnit 11 |

## Requirement

- PHP 8.2+
- MySQL 8.0+
- Composer 2.x
- Node.js 18+ & npm
- Ekstensi PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD/Imagick

## Instalasi

```bash
# Clone repository
git clone <repository-url>
cd penginapan-kelapa-sawit

# Install dependencies
composer install
npm install

# Copy environment
cp .env.example .env
php artisan key:generate

# Konfigurasi .env (lihat bagian Konfigurasi di bawah)

# Database
php artisan migrate
php artisan db:seed  # (jika tersedia seeder)

# Storage link untuk foto
php artisan storage:link

# Build frontend
npm run build
```

## Konfigurasi Environment

Variabel penting di `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=Asia/Makassar

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=penginapan_kelapa_sawit
DB_USERNAME=
DB_PASSWORD=

# Midtrans
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

# Booking
BOOKING_HOLD_MINUTES=30
BOOKING_CHECK_IN_TIME=14:00
BOOKING_CHECK_OUT_TIME=12:00

# Loyalty
LOYALTY_EARN_DIVISOR=1000
LOYALTY_POINT_VALUE=50
LOYALTY_MIN_REDEEM=100
LOYALTY_MAX_REDEMPTION_PERCENT=20
LOYALTY_EXPIRY_MONTHS=18
```

## Google OAuth

1. Buat project di [Google Cloud Console](https://console.cloud.google.com/)
2. Aktifkan Google+ API
3. Buat OAuth 2.0 Client ID (Web application)
4. Set redirect URI: `https://domain.com/auth/google/callback`
5. Masukkan Client ID dan Secret ke `.env`

## Midtrans

1. Daftar di [Midtrans Dashboard](https://dashboard.midtrans.com/)
2. Gunakan mode Sandbox untuk testing
3. Salin Server Key dan Client Key ke `.env`
4. Konfigurasi webhook URL: `https://domain.com/webhook/midtrans`
5. Untuk production: set `MIDTRANS_IS_PRODUCTION=true`

## Scheduler

Tambahkan cron job untuk menjalankan Laravel scheduler:

```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks:
- `booking:expire-pending` — Expire booking yang melewati batas waktu pembayaran
- `loyalty:expire-points` — Expire poin loyalitas yang kedaluwarsa

## Testing

```bash
# Jalankan semua test
php artisan test

# Dengan coverage
php artisan test --coverage
```

## Build Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Deployment Checklist

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_TIMEZONE=Asia/Makassar`
- [ ] Semua secret terisi di `.env`
- [ ] `MIDTRANS_IS_PRODUCTION=true` (jika live)
- [ ] Webhook URL terdaftar di Midtrans Dashboard
- [ ] Google OAuth redirect URI sesuai domain production
- [ ] `php artisan migrate` berhasil
- [ ] `php artisan storage:link`
- [ ] `npm run build`
- [ ] Config/route/view cached
- [ ] Cron scheduler aktif
- [ ] HTTPS aktif
- [ ] File `.env` tidak accessible dari web

## Arsitektur

```
app/
├── Enums/          # PHP backed string enums (BookingStatus, PaymentStatus, dll)
├── Http/Controllers/
│   ├── Public/     # Guest booking, availability, payment
│   ├── Member/     # Dashboard, booking history, points, profile
│   ├── Admin/      # Reservasi, kamar, keuangan, laporan
│   └── Webhook/    # Midtrans webhook
├── Services/       # Domain logic (Booking, Availability, Pricing, Midtrans, Loyalty)
├── Models/         # Eloquent models
└── Console/Commands/  # Expire bookings, expire points, reconcile payments
```

## Lisensi

Hak cipta © Penginapan Kelapa Sawit. Seluruh hak dilindungi.
