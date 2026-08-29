# Feature: Payment (Midtrans) — Penginapan Kelapa Sawit

> **Sumber:** `app/Services/MidtransPaymentService.php`, `app/Http/Controllers/Webhook/MidtransWebhookController.php`, `config/midtrans.php`

---

## Konfigurasi

**File:** `config/midtrans.php`

```php
'server_key'   => env('MIDTRANS_SERVER_KEY'),   // Backend only, tidak pernah ke browser
'client_key'   => env('MIDTRANS_CLIENT_KEY'),   // Untuk Snap.js di frontend
'is_production' => env('MIDTRANS_IS_PRODUCTION', false), // Default: sandbox
'is_sanitized'  => true,
'is_3ds'        => true,
```

**SDK:** `midtrans/midtrans-php ^2.6`

---

## Alur Pembayaran

```
1. Tamu klik "Bayar" → GET /booking/{code}/bayar
2. MidtransPaymentService::createOrResumePayment()
   - Cek Payment existing dengan snap_token valid
   - Atau buat Payment baru → generate Snap token
3. Tampilkan Snap popup (frontend, client key)
4. Tamu selesai bayar di popup Snap
5. Midtrans kirim webhook → POST /webhook/midtrans
6. Webhook update status booking → BookingStatus::Confirmed
7. Dispatch PaymentConfirmed event → kirim email
8. Tamu di-redirect ke GET /booking/{code}/selesai
```

---

## Webhook

**Route:** `POST /webhook/midtrans` — tanpa CSRF, throttle 60/menit

**Referensi:** `app/Http/Controllers/Webhook/MidtransWebhookController.php`

### Verifikasi Keamanan

1. **Signature check** — SHA-512 sesuai dokumentasi resmi Midtrans
2. **Amount check** — `gross_amount` payload harus cocok dengan `booking.total_amount`
3. **Idempotency** — webhook duplikat return 2xx tanpa side effect ganda
4. **Tidak log Server Key** — tidak ada secret di log

### Sumber Kebenaran

Webhook Midtrans adalah **satu-satunya** sumber kebenaran status pembayaran.

- Callback JavaScript Snap **bukan** bukti pembayaran
- Frontend hanya untuk display countdown dan redirect

### Kasus Edge: Payment Terlambat

Jika webhook diterima setelah booking expired:
1. Simpan payment dengan status `paid`
2. Set `booking.needs_attention = true`
3. **Admin memutuskan** resolusi — tidak ada tindakan otomatis

---

## Payment Model

**File:** `app/Models/Payment.php`

Satu booking dapat memiliki beberapa attempt payment (`attempt_no`).

| Field penting | Keterangan |
|---|---|
| `provider_order_id` | Order ID ke Midtrans (unique) |
| `snap_token` | Token Snap, ada expiry-nya |
| `gross_amount` | Harus cocok dengan `booking.total_amount` |
| `raw_response` | Response JSON mentah dari Midtrans |

---

## Rekonsiliasi

**File:** `app/Console/Commands/ReconcilePaymentsCommand.php`

- Artisan signature: `payments:reconcile`
- Cek status pembayaran langsung ke Midtrans API (server-to-server)
- Tangani booking dengan payment pending yang tidak menerima webhook
- Dijalankan berkala via scheduler

---

## Refund

**File:** `app/Http/Controllers/Admin/RefundController.php`, `app/Models/Refund.php`

- Hanya admin yang bisa submit permintaan refund
- Route: `GET /admin/bookings/{booking}/refund`, `POST /admin/bookings/{booking}/refund`
- Lifecycle: `requested → processing → succeeded/failed/cancelled`
- Tidak ada refund otomatis — selalu persetujuan admin
