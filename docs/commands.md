# Console Commands — Penginapan Kelapa Sawit

> **Sumber:** `app/Console/Commands/` — diverifikasi dari source code aktual.

---

## Daftar Commands

### admin:create

**File:** `app/Console/Commands/CreateAdminCommand.php`

Buat akun admin baru secara interaktif. Meminta input nama, email, password, dan role.

```bash
php artisan admin:create
```

---

### booking:expire-pending

**File:** `app/Console/Commands/ExpirePendingBookingsCommand.php`

Expire semua booking dengan status `pending_payment` yang telah melewati `payment_expires_at`.

```bash
php artisan booking:expire-pending
```

**Logika:**
1. Cari booking `status = pending_payment`, `payment_expires_at <= now()`
2. Panggil `BookingService::expirePendingBooking()` untuk setiap booking
3. `expirePendingBooking()` update status ke `Expired`, tulis `BookingStatusHistory`, dispatch `BookingCancelled` event

---

### booking:send-checkin-reminders

**File:** `app/Console/Commands/SendCheckinReminders.php`

Kirim email pengingat check-in untuk tamu yang check-in besok.

```bash
php artisan booking:send-checkin-reminders
```

**Target:** Booking `status = confirmed`, `check_in = tomorrow`, `reminder_email_sent_at IS NULL`

**Jadwal:** Daily 09:00 WITA (di `routes/console.php`)

**Idempotency:** Set `reminder_email_sent_at = now()` setelah kirim. Aman dijalankan berulang di hari yang sama.

---

### booking:send-post-checkout-emails

**File:** `app/Console/Commands/SendPostCheckoutEmails.php`

Kirim email post-checkout untuk tamu yang checkout kemarin.

```bash
php artisan booking:send-post-checkout-emails
```

**Target:** Booking `status = checked_out`, `checked_out_at = yesterday`, `checkout_email_sent_at IS NULL`

**Jadwal:** Daily 10:00 WITA

**Idempotency:** Set `checkout_email_sent_at = now()` setelah kirim.

---

### loyalty:expire-points

**File:** `app/Console/Commands/ExpireLoyaltyPointsCommand.php`

Expire semua lot poin loyalitas yang melewati tanggal kadaluarsa.

```bash
php artisan loyalty:expire-points
```

**Logika:** Panggil `LoyaltyPointService::expirePointsForUser()` untuk setiap user yang punya lot expired.

**Idempotency:** Cek idempotency key per lot sebelum buat transaksi expire.

---

### payments:reconcile

**File:** `app/Console/Commands/ReconcilePaymentsCommand.php`

Rekonsiliasi status pembayaran dengan Midtrans API (server-to-server check).

```bash
php artisan payments:reconcile
```

Tangani booking dengan payment pending yang tidak menerima webhook.

---

### (diagnosa Midtrans)

**File:** `app/Console/Commands/MidtransDiagnoseCommand.php`

Diagnosa koneksi dan konfigurasi Midtrans.

---

## Scheduler

Didefinisikan di `routes/console.php`:

| Command | Jadwal | Opsi |
|---|---|---|
| `booking:send-checkin-reminders` | Daily 09:00 | `withoutOverlapping()` |
| `booking:send-post-checkout-emails` | Daily 10:00 | `withoutOverlapping()` |
| `loyalty:expire-points` | Daily | — |
| `payments:reconcile` | Berkala | — |

---

## Menjalankan Scheduler

```bash
# Lokal — jalankan scheduler setiap menit
php artisan schedule:run

# Production — tambahkan ke crontab
* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1
```
