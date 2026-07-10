# Midtrans Sandbox End-to-End Testing Guide

## Architecture Overview

```
Guest/Member → Booking Engine → PaymentController (Snap Token)
                                        ↓
                            MidtransPaymentService.createOrResumePayment()
                                        ↓
                            Midtrans Snap API (server-to-server)
                                        ↓
                            Snap Token → Frontend (snap.js popup)
                                        ↓
                            Guest completes payment in Snap popup
                                        ↓
                    Midtrans sends webhook → POST /webhook/midtrans
                                        ↓
                    MidtransWebhookController → MidtransPaymentService.handleWebhook()
                                        ↓
                    Signature verify → Amount verify → Dedup check → Status update
                                        ↓
                    Payment status = paid → Booking status = confirmed
```

## Environment Variables

File: `.env`

```env
# JANGAN isi value di file ini. Ini hanya template.
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxx     # Sandbox Server Key dari dashboard
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxx     # Sandbox Client Key dari dashboard
MIDTRANS_IS_PRODUCTION=false               # WAJIB false untuk sandbox
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

**Sumber Credential:** https://dashboard.sandbox.midtrans.com → Settings → Access Keys

## Diagnosis Command

```bash
php artisan midtrans:diagnose
```

Output akan menampilkan:
- Environment (Sandbox/Production)
- Key configured (Yes/No tanpa menampilkan value)
- Webhook route
- APP_URL
- Expected Notification URL

## Start Commands

```bash
# 1. Start Laravel
php artisan serve

# 2. Start public tunnel (terminal terpisah)
ngrok http 8000

# 3. Update .env
APP_URL=https://xxxx-xx-xx.ngrok-free.app

# 4. Clear config cache
php artisan optimize:clear

# 5. Verify
php artisan midtrans:diagnose
```

## Tunnel Setup

### ngrok

```bash
ngrok http 8000
```

Salin URL HTTPS yang diberikan (contoh: `https://a1b2c3d4.ngrok-free.app`).

### Notification URL di Midtrans Dashboard

1. Buka https://dashboard.sandbox.midtrans.com
2. Settings → Payment → Notification URL
3. Isi: `https://YOUR-NGROK-URL/webhook/midtrans`
4. Save

**Penting:** Setiap kali restart ngrok, URL berubah. Update kembali di dashboard Midtrans.

## Manual Test Plan — Sandbox Simulator

### Prerequisites
- [ ] Laravel running (`php artisan serve`)
- [ ] ngrok running dan URL di-update di `.env`
- [ ] `php artisan optimize:clear` setelah ubah APP_URL
- [ ] Notification URL di-set di Midtrans Sandbox Dashboard
- [ ] `php artisan midtrans:diagnose` — semua hijau

### Test Steps

1. **Buka website** → `http://localhost:8000` (atau ngrok URL)

2. **Cari kamar** → `/ketersediaan`
   - Pilih tanggal check-in (besok) dan check-out
   - Pilih jumlah tamu

3. **Checkout** → Isi data tamu:
   - Nama: Test Guest
   - Email: test@example.com
   - WhatsApp: 08123456789
   - Centang kebijakan
   - Klik "Pesan Sekarang"

4. **Konfirmasi** → Catat booking code. Klik "Bayar Sekarang"

5. **Payment Page** → Pastikan:
   - [ ] Banner "Mode Uji Coba — Jangan gunakan uang asli." tampil
   - [ ] Countdown terlihat
   - [ ] Total benar
   - [ ] Klik "Bayar Sekarang"

6. **Snap Popup** → Pilih metode pembayaran:
   - Pilih **BRI Virtual Account** (atau Bank Transfer lainnya)
   - Snap akan menampilkan Virtual Account number
   - **Salin nomor VA**

7. **Midtrans Sandbox Simulator** → https://simulator.sandbox.midtrans.com
   - Pilih "Bank Transfer" → BRI
   - Paste nomor VA
   - Klik "Inquire" lalu "Pay"

8. **Verifikasi di Midtrans Dashboard:**
   - https://dashboard.sandbox.midtrans.com → Transactions
   - Status harus "settlement"
   - Cek tab "Notification" → pastikan webhook sent

9. **Verifikasi di database aplikasi:**
   ```bash
   php artisan tinker
   ```
   ```php
   $booking = \App\Models\Booking::where('booking_code', 'BKG-XXXXXX-XXXX')->first();
   $booking->status;          // Harus: "confirmed"
   $booking->payment_status;  // Harus: "paid"
   
   $payment = $booking->payments()->latest()->first();
   $payment->status;          // Harus: "paid"
   $payment->paid_at;         // Harus terisi
   ```

10. **Verifikasi webhook event:**
    ```php
    \App\Models\PaymentWebhookEvent::latest()->first()->toArray();
    // processing_status: "processed"
    // signature_valid: true
    // amount_valid: true
    ```

11. **Verifikasi admin view:**
    - Login admin → `/admin/bookings`
    - Booking harus status "Dikonfirmasi"

12. **Verifikasi user view (cek booking):**
    - `/cek-booking` → masukkan kode booking + email
    - Status harus "Dikonfirmasi"

### Expected State Transitions

```
Booking dibuat          → status: pending_payment, payment_status: unpaid
Snap token dibuat       → payment record: status unpaid, snap_token filled
Guest bayar di sandbox  → Midtrans: settlement
Webhook diterima        → payment: paid, booking: confirmed
```

## Troubleshooting

### A. Snap Popup Tidak Terbuka
- Periksa browser console untuk error JavaScript
- Pastikan Client Key benar (`SB-Mid-client-...`)
- Pastikan `snap.js` URL sandbox: `https://app.sandbox.midtrans.com/snap/snap.js`
- Pastikan snap_token valid (bukan expired)

### B. Transaction Token Gagal
- Periksa `storage/logs/laravel.log` untuk error Midtrans Snap API
- Pastikan Server Key benar
- Pastikan `gross_amount` = integer > 0
- Pastikan `order_id` belum pernah dipakai

### C. Simulator Berhasil Tapi Webhook Tidak Datang
- Pastikan Notification URL di Midtrans Dashboard benar
- Pastikan ngrok aktif dan URL cocok
- Pastikan APP_URL di `.env` cocok dengan ngrok
- Cek ngrok web inspector: `http://127.0.0.1:4040`
- Cek di Midtrans Dashboard → Transaction → Notification tab

### D. Webhook Datang Tapi Ditolak
- Periksa `payment_webhook_events` table: `processing_status`, `error_message`
- `Invalid signature` → Server Key di .env tidak cocok dengan dashboard
- `Amount mismatch` → gross_amount webhook != payment.gross_amount
- `Payment not found` → order_id tidak cocok dengan provider_order_id

### E. Webhook Valid Tapi Payment Tidak Berubah
- Cek apakah webhook sudah pernah diproses (deduplication)
- Cek apakah payment sudah di status terminal (paid/refunded)
- Periksa `payment_webhook_events.processing_status` = "processed"

### F. Payment Berubah Tapi Booking Tidak Confirmed
- Booking sudah expired? → needs_attention = true, admin harus resolve
- Booking sudah cancelled? → payment paid tapi booking tetap cancelled
- Check `booking_status_histories` table

### G. Admin dan User Menampilkan Status Berbeda
- `php artisan optimize:clear` (config cache stale?)
- Periksa langsung di database
- Refresh halaman (browser cache)

## Production Switch Checklist

**JANGAN aktifkan production sebelum semua terpenuhi:**

- [ ] Midtrans account review approved
- [ ] Sandbox E2E test berhasil (booking confirmed via simulator)
- [ ] Webhook signature verification lulus
- [ ] Amount verification lulus
- [ ] Duplicate webhook idempotent
- [ ] Mobile payment flow ditest (375px)
- [ ] Semua automated test lulus
- [ ] Build lulus (`npm run build`)
- [ ] Tidak ada critical payment bug yang diketahui

**Langkah switch ke production:**

1. Dapatkan Production keys dari dashboard.midtrans.com
2. Update `.env`:
   ```env
   MIDTRANS_SERVER_KEY=Mid-server-xxxx
   MIDTRANS_CLIENT_KEY=Mid-client-xxxx
   MIDTRANS_IS_PRODUCTION=true
   ```
3. Set Payment Notification URL di **production** dashboard
4. `php artisan optimize:clear`
5. `php artisan midtrans:diagnose` — harus menunjukkan "PRODUCTION"
6. Test dengan nominal kecil (transaksi nyata)
