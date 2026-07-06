# SPEC 04 — Midtrans Payment: Requirements

> **Referensi master:** Fase 6, Bagian 9.5, Bagian 10.12–10.14  
> **Dependency:** SPEC 03 selesai (booking engine)  
> **Scope:** Payment attempt, Snap token, webhook, status mapping, reconciliation

---

## REQ-1: Payment Attempt Creation

### REQ-1.1
**Sebagai** guest/member yang telah membuat booking,  
**saya ingin** mendapatkan halaman pembayaran Midtrans Snap,  
**sehingga** saya dapat memilih metode pembayaran dan menyelesaikan transaksi.

**Acceptance Criteria:**
- Saat user klik "Bayar Sekarang" pada booking pending, backend membuat payment attempt.
- Payment attempt menyimpan: booking_id, provider (midtrans), provider_order_id (unique), attempt_no, gross_amount.
- Backend memanggil Midtrans API untuk mendapatkan Snap token.
- Snap token disimpan pada payment attempt.
- Frontend membuka Snap popup menggunakan token.
- `gross_amount` HARUS sama dengan `booking.total_amount`.

### REQ-1.2
**Sebagai** sistem,  
**saya ingin** provider_order_id unik per payment attempt,  
**sehingga** setiap transaksi dapat dilacak dengan jelas.

**Acceptance Criteria:**
- Format: `{booking_code}-{attempt_no}` (contoh: `BKG-202607-0001-1`).
- Unique constraint pada `(provider, provider_order_id)`.
- Attempt baru dibuat hanya jika booking masih pending dan hold belum expired.

### REQ-1.3
**Sebagai** guest/member,  
**saya ingin** melanjutkan pembayaran yang sudah dimulai,  
**sehingga** saya tidak perlu membuat attempt baru jika Snap token masih valid.

**Acceptance Criteria:**
- Jika sudah ada payment attempt dengan Snap token yang belum expired, return token tersebut.
- Jika attempt sebelumnya sudah expired/failed, buat attempt baru.
- Booking harus masih `pending_payment` dan `payment_expires_at > now`.

---

## REQ-2: Webhook Notification

### REQ-2.1
**Sebagai** sistem,  
**saya ingin** menerima notifikasi dari Midtrans secara aman,  
**sehingga** status pembayaran selalu akurat.

**Acceptance Criteria:**
- Endpoint webhook publik menerima POST JSON tanpa session/CSRF.
- Verifikasi signature sesuai dokumentasi resmi Midtrans (SHA-512 hash).
- Cocokkan nominal (`gross_amount` webhook == booking `total_amount`).
- Cocokkan `order_id` ke payment attempt yang ada.
- Simpan event ke `payment_webhook_events` untuk audit/dedup.
- Idempotent: duplicate notification menghasilkan 200 tanpa side effect ganda.
- Return HTTP 200 cepat setelah proses selesai.

### REQ-2.2
**Sebagai** sistem,  
**saya ingin** status payment di-mapping dari status Midtrans ke status aplikasi,  
**sehingga** status konsisten di seluruh sistem.

**Acceptance Criteria:**
- settlement/capture (fraud=accept) → `paid`
- pending → `pending`
- expire → `expired`
- deny/cancel → `failed`
- Status lain yang tidak dikenal ditangani gracefully (log, tidak crash).

### REQ-2.3
**Sebagai** sistem,  
**saya ingin** booking dikonfirmasi otomatis saat payment berhasil,  
**sehingga** tamu mendapat kepastian reservasi.

**Acceptance Criteria:**
- Ketika payment menjadi `paid` dan booking masih `pending_payment`: transisi booking ke `confirmed`.
- Tulis status history.
- Jika booking sudah `expired` saat webhook paid masuk: payment tetap dicatat `paid`, booking TIDAK diubah ke confirmed, set `needs_attention = true` dengan `attention_reason = late_payment_after_booking_expired`.

---

## REQ-3: Payment Security

### REQ-3.1
**Sebagai** sistem,  
**saya ingin** Server Key tidak terekspos,  
**sehingga** transaksi aman dari manipulasi.

**Acceptance Criteria:**
- Server Key hanya di `.env`, tidak di frontend/log/database.
- Client Key hanya digunakan untuk Snap.js di frontend.
- Signature verification menggunakan Server Key di backend.
- Raw webhook payload (kecuali data sensitif) disimpan untuk debugging.

### REQ-3.2
**Sebagai** sistem,  
**saya ingin** callback JavaScript BUKAN bukti pembayaran,  
**sehingga** hanya webhook yang mengubah status payment.

**Acceptance Criteria:**
- `onSuccess`/`onPending` callback Snap hanya untuk UX redirect.
- Status payment HANYA berubah melalui webhook atau server-to-server status check.
- Frontend tidak dapat memanggil endpoint yang mengubah payment status.

---

## REQ-4: Reconciliation

### REQ-4.1
**Sebagai** admin,  
**saya ingin** sistem dapat mengecek status pembayaran ke Midtrans,  
**sehingga** pembayaran yang webhooknya tertunda tetap terproses.

**Acceptance Criteria:**
- Command `payment:reconcile` mengecek payment pending yang sudah lama.
- Fetch status server-to-server via Midtrans Status API.
- Proses melalui mapping yang sama dengan webhook.
- Idempotent.

---

## Constraints

- **C-1:** Midtrans Sandbox default, production memerlukan konfigurasi eksplisit.
- **C-2:** Server Key tidak di source code, log, atau database.
- **C-3:** Webhook harus verified, idempotent, dan amount-checked.
- **C-4:** Callback JavaScript bukan bukti pembayaran.
- **C-5:** Payment status dan booking status adalah dua hal berbeda.
- **C-6:** gross_amount payment HARUS sama dengan total_amount booking.
