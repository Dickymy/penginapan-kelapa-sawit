# Deployment Guide — Laravel Cloud

## Penginapan Kelapa Sawit

Repository: `Dickymy/penginapan-kelapa-sawit`

---

## 1. Environment Variables

Set di Laravel Cloud dashboard. **JANGAN commit nilai secret.**

### Application

| Key | Value | Catatan |
|-----|-------|---------|
| `APP_NAME` | Penginapan Kelapa Sawit | |
| `APP_ENV` | production | |
| `APP_DEBUG` | false | WAJIB false |
| `APP_KEY` | base64:... | Generate via `php artisan key:generate --show` |
| `APP_URL` | https://DOMAIN_ANDA | URL public staging |
| `APP_TIMEZONE` | Asia/Makassar | |
| `APP_LOCALE` | id | |
| `APP_MAINTENANCE_DRIVER` | cache | Multi-instance safe |

### Database (MySQL)

| Key | Value |
|-----|-------|
| `DB_CONNECTION` | mysql |
| `DB_HOST` | (dari Laravel Cloud) |
| `DB_PORT` | 3306 |
| `DB_DATABASE` | (dari Laravel Cloud) |
| `DB_USERNAME` | (dari Laravel Cloud) |
| `DB_PASSWORD` | (dari Laravel Cloud) |

### Session, Cache, Queue

| Key | Value | Catatan |
|-----|-------|---------|
| `SESSION_DRIVER` | database | |
| `CACHE_STORE` | database | |
| `QUEUE_CONNECTION` | database | |
| `SESSION_DOMAIN` | .DOMAIN_ANDA | Sesuaikan |

### Storage (S3-Compatible Object Storage)

| Key | Value |
|-----|-------|
| `FILESYSTEM_DISK` | public |
| `FILESYSTEM_PUBLIC_DRIVER` | s3 |
| `FILESYSTEM_PUBLIC_URL` | https://BUCKET.s3.REGION.amazonaws.com |
| `AWS_ACCESS_KEY_ID` | (dari Laravel Cloud) |
| `AWS_SECRET_ACCESS_KEY` | (dari Laravel Cloud) |
| `AWS_DEFAULT_REGION` | ap-southeast-1 |
| `AWS_BUCKET` | nama-bucket |

> Jika Laravel Cloud menyediakan object storage built-in, ikuti dokumentasi resmi mereka.
> Untuk staging awal tanpa S3, gunakan `FILESYSTEM_PUBLIC_DRIVER=local` dan jalankan `storage:link`.

### Mail (Gmail SMTP)

| Key | Value |
|-----|-------|
| `MAIL_MAILER` | smtp |
| `MAIL_HOST` | smtp.gmail.com |
| `MAIL_PORT` | 587 |
| `MAIL_USERNAME` | email@gmail.com |
| `MAIL_PASSWORD` | (App Password Gmail) |
| `MAIL_FROM_ADDRESS` | email@gmail.com |
| `MAIL_FROM_NAME` | Penginapan Kelapa Sawit |

### Google OAuth

| Key | Value |
|-----|-------|
| `GOOGLE_CLIENT_ID` | (dari Google Console) |
| `GOOGLE_CLIENT_SECRET` | (dari Google Console) |
| `GOOGLE_REDIRECT_URI` | https://DOMAIN_ANDA/auth/google/callback |

> Update Authorized redirect URIs di Google Cloud Console dengan URL staging.

### Midtrans (SANDBOX)

| Key | Value |
|-----|-------|
| `MIDTRANS_SERVER_KEY` | SB-Mid-server-xxx |
| `MIDTRANS_CLIENT_KEY` | SB-Mid-client-xxx |
| `MIDTRANS_IS_PRODUCTION` | false |

> **JANGAN ubah ke production.** Ini staging/public test.

### Webhook Midtrans

URL notifikasi di Midtrans Sandbox Dashboard:
```
https://DOMAIN_ANDA/webhook/midtrans
```

---

## 2. Build Commands

Laravel Cloud build step:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 3. Deploy Commands

Post-deploy (one-time & per-deploy):

```bash
php artisan migrate --force
php artisan storage:link
```

> `storage:link` hanya diperlukan jika menggunakan local filesystem.
> Jika menggunakan S3, symlink tidak diperlukan.

---

## 4. Database Setup

- Laravel Cloud menyediakan MySQL managed.
- Migration otomatis jalan via deploy command.
- **JANGAN jalankan `migrate:fresh` atau `db:seed` di production.**
- Tidak ada dummy/test data yang diperlukan.

### Admin Pertama

Buat admin production pertama via Artisan:

```bash
php artisan admin:create
```

Command akan meminta:
- Nama
- Email
- Password (tersembunyi, min 8 karakter)
- Konfirmasi password

---

## 5. Storage Setup

Project menggunakan `public` disk untuk upload gambar (room, gallery).

**Opsi A: Local + Symlink (staging sederhana)**
```
FILESYSTEM_DISK=public
FILESYSTEM_PUBLIC_DRIVER=local
```
Jalankan `php artisan storage:link` setelah deploy.

**Opsi B: S3-Compatible (production recommended)**
```
FILESYSTEM_DISK=public
FILESYSTEM_PUBLIC_DRIVER=s3
FILESYSTEM_PUBLIC_URL=https://BUCKET.s3.REGION.amazonaws.com
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=...
```

---

## 6. Queue Setup

| Setting | Value |
|---------|-------|
| Connection | database |
| Queue name | default |
| Worker command | `php artisan queue:work --tries=3 --timeout=60` |
| Max attempts | 3 |
| Timeout | 60 seconds |

Failed jobs disimpan di tabel `failed_jobs`.

Lihat failed jobs:
```bash
php artisan queue:failed
```

Retry:
```bash
php artisan queue:retry all
```

---

## 7. Scheduler Setup

Laravel Cloud Scheduled Tasks — tambahkan:

```
php artisan schedule:run
```

Interval: **Every minute**

Scheduled commands:
| Command | Interval | Fungsi |
|---------|----------|--------|
| `booking:expire-pending` | Every minute | Expire booking yang melewati batas bayar |
| `payment:reconcile` | Every 5 minutes | Cek status payment ke Midtrans |
| `loyalty:expire-points` | Daily | Expire poin yang sudah kedaluwarsa |

Semua command menggunakan `withoutOverlapping()` untuk keamanan multi-instance.

---

## 8. Google OAuth Update

1. Buka [Google Cloud Console](https://console.cloud.google.com/apis/credentials)
2. Edit OAuth 2.0 Client
3. Tambahkan Authorized redirect URI:
   ```
   https://DOMAIN_ANDA/auth/google/callback
   ```
4. Tambahkan domain ke Authorized JavaScript origins (jika diminta)
5. Set env `GOOGLE_REDIRECT_URI=https://DOMAIN_ANDA/auth/google/callback`

---

## 9. Midtrans Update

1. Login ke [Midtrans Sandbox Dashboard](https://dashboard.sandbox.midtrans.com)
2. Settings → Configuration
3. Set Payment Notification URL:
   ```
   https://DOMAIN_ANDA/webhook/midtrans
   ```
4. Redirect URL (opsional): `https://DOMAIN_ANDA/booking/{order_id}/selesai`
5. Pastikan Server Key dan Client Key sudah sesuai di env

---

## 10. Smoke Test Checklist

Setelah deploy, verifikasi:

- [ ] `https://DOMAIN/up` → 200 OK
- [ ] Homepage load dengan benar
- [ ] Halaman kamar tampil (tanpa gambar jika belum upload)
- [ ] Cek ketersediaan berfungsi
- [ ] Register member baru
- [ ] Login member
- [ ] Login Google OAuth
- [ ] Login admin (`/admin/login`)
- [ ] Admin dashboard load
- [ ] Buat test booking → Checkout → Payment (Sandbox)
- [ ] Verifikasi webhook Midtrans tiba
- [ ] Upload gambar kamar (admin)
- [ ] Email verifikasi terkirim
- [ ] Queue job diproses (cek `jobs` table)
- [ ] Scheduler jalan (cek apakah pending booking expire)

---

## 11. Rollback Procedure

Laravel Cloud mendukung rollback ke deployment sebelumnya.

Jika perlu rollback database:
1. **JANGAN** gunakan `migrate:rollback` di production
2. Buat migration baru yang me-reverse perubahan
3. Deploy ulang dengan migration baru

---

## 12. Catatan Keamanan

- `APP_DEBUG=false` di production → error tidak expose stack trace
- `MIDTRANS_IS_PRODUCTION=false` → tetap sandbox
- Tidak ada default password admin
- Secret hanya via environment variable
- Webhook diverifikasi via signature
- Session menggunakan `database` driver (aman multi-instance)
- Trust proxies dikonfigurasi untuk load balancer (wildcard `*`)

---

## 13. Upgrade ke Production (FUTURE)

Ketika siap pindah dari staging ke production:

1. Ganti `MIDTRANS_IS_PRODUCTION=true`
2. Gunakan Production Server Key & Client Key
3. Update webhook URL di Midtrans Production Dashboard
4. Update Google OAuth credentials untuk domain final
5. Pertimbangkan Redis untuk cache/queue/session
6. Setup CDN untuk asset statis
7. Konfigurasi backup database reguler
