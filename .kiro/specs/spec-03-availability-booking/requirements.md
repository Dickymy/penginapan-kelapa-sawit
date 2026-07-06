# SPEC 03 — Availability & Guest Booking Engine: Requirements

> **Referensi master:** Fase 4–5, Bagian 9.1–9.4, Bagian 10.1–10.6  
> **Dependency:** SPEC 02 selesai (room inventory, public pages)  
> **Scope:** Availability engine, pricing, booking creation, hold/expiry, guest checkout

---

## REQ-1: Availability Search

### REQ-1.1
**Sebagai** pengunjung,  
**saya ingin** mencari kamar yang tersedia berdasarkan tanggal dan jumlah tamu,  
**sehingga** saya hanya melihat kamar yang bisa dipesan.

**Acceptance Criteria:**
- Form menerima: check_in (DATE), check_out (DATE), guest_count (integer ≥ 1).
- Validasi: check_out > check_in, minimal 1 malam, check_in ≥ hari ini.
- Hasil menampilkan tipe kamar aktif yang memiliki minimal 1 kamar fisik tersedia.
- Per tipe kamar ditampilkan: nama, harga/malam, total preview, jumlah unit tersedia, CTA pilih.
- Kamar yang sedang di-booking (confirmed, checked_in, pending_payment yang belum expired) tidak ditampilkan.
- Kamar yang di-room-block pada interval tersebut tidak ditampilkan.

### REQ-1.2
**Sebagai** sistem,  
**saya ingin** interval overlap menggunakan half-open `[check_in, check_out)`,  
**sehingga** guest checkout dan guest berikutnya check-in di hari yang sama tidak bentrok.

**Acceptance Criteria:**
- Overlap: `existing.check_in < new.check_out AND existing.check_out > new.check_in`.
- Booking A (10–12 Juli) dan Booking B (12–14 Juli) TIDAK overlap.
- Booking A (10–12 Juli) dan Booking C (11–13 Juli) OVERLAP.
- Room block menggunakan rumus overlap yang sama.

---

## REQ-2: Pricing

### REQ-2.1
**Sebagai** sistem,  
**saya ingin** harga dikalkulasi sepenuhnya di server,  
**sehingga** tidak ada manipulasi harga dari frontend.

**Acceptance Criteria:**
- `nights = check_out - check_in` (jumlah hari).
- `subtotal = nights × price_per_night` (dari room_type.base_price).
- `total_amount = subtotal` (V1 tanpa promo/poin di SPEC ini, promo di SPEC 06).
- Frontend TIDAK mengirim harga; server menghitung ulang saat create booking.
- PricingService menghasilkan quote object/array terstruktur.

---

## REQ-3: Guest Booking (Tanpa Login)

### REQ-3.1
**Sebagai** pengunjung,  
**saya ingin** memesan kamar tanpa wajib login,  
**sehingga** proses pemesanan cepat dan mudah.

**Acceptance Criteria:**
- Form checkout: guest_name (required), guest_email (optional), guest_whatsapp (required), arrival_estimate (optional), special_request (optional), policy checkbox (required).
- Booking dibuat dengan `user_id = NULL` untuk guest.
- Guest menerima booking code dan access token untuk mengecek status.
- Akun tidak wajib dibuat untuk memesan.

### REQ-3.2
**Sebagai** member (login),  
**saya ingin** data saya ter-autofill saat checkout,  
**sehingga** pemesanan lebih cepat.

**Acceptance Criteria:**
- Jika user logged in, form pre-fill: name, email, whatsapp dari profil.
- Booking dibuat dengan `user_id` terisi.
- Member tetap dapat mengubah data guest sebelum submit.

---

## REQ-4: Double Booking Protection

### REQ-4.1
**Sebagai** sistem,  
**saya ingin** mencegah dua booking pada kamar dan tanggal yang sama,  
**sehingga** integritas inventory terjaga.

**Acceptance Criteria:**
- **Layer 1 (Search):** Hanya tampilkan kamar yang tersedia.
- **Layer 2 (Checkout):** Recheck sebelum tampilkan ringkasan.
- **Layer 3 (Create):** DB transaction + `lockForUpdate` pada row kamar + overlap recheck.
- **Layer 4 (Idempotency):** `idempotency_key` unique; submit ulang key sama tidak membuat booking kedua.
- **Layer 5 (Unique Constraint):** `booking_code` unique di database.
- Test concurrency: 2 request bersamaan untuk kamar+tanggal sama → tepat 1 sukses, 1 gagal.

### REQ-4.2
**Sebagai** sistem,  
**saya ingin** room assignment otomatis memilih kamar fisik yang tersedia,  
**sehingga** guest memilih tipe kamar, bukan nomor fisik.

**Acceptance Criteria:**
- Guest memilih room type, bukan room ID.
- Saat create booking, sistem memilih kamar fisik tersedia pertama (by sort_order, lalu ID).
- Lock kandidat secara deterministik untuk menghindari deadlock.

---

## REQ-5: Booking Hold & Expiry

### REQ-5.1
**Sebagai** sistem,  
**saya ingin** booking pending mengunci kamar selama 30 menit,  
**sehingga** tamu punya waktu membayar tanpa kamar diambil orang lain.

**Acceptance Criteria:**
- Booking baru status `pending_payment` dengan `payment_expires_at = now + 30 menit`.
- Availability menganggap pending booking memblokir hanya jika `payment_expires_at > now`.
- Setelah expired, booking otomatis berstatus `expired` dan kamar tersedia kembali.

### REQ-5.2
**Sebagai** sistem,  
**saya ingin** scheduler mengexpire booking yang melewati batas waktu,  
**sehingga** kamar tidak terkunci selamanya.

**Acceptance Criteria:**
- Command/scheduler berjalan periodik (setiap menit).
- Query: booking `pending_payment` dengan `payment_expires_at <= now`.
- Per booking: lock, recheck status, jika masih pending dan belum paid → expire.
- Tulis status history.
- Idempotent (aman jika dijalankan berulang).

---

## REQ-6: Booking Code & Sequence

### REQ-6.1
**Sebagai** sistem,  
**saya ingin** booking code unik dan terformat `BKG-YYYYMM-0001`,  
**sehingga** kode mudah dibaca dan tidak bentrok.

**Acceptance Criteria:**
- Tabel `document_sequences` menyimpan counter per type+period.
- Generate: lock row → increment → format padded.
- Unique constraint pada `booking_code` sebagai last defense.
- Tidak menggunakan `count() + 1`.

---

## REQ-7: Guest Access Token

### REQ-7.1
**Sebagai** guest,  
**saya ingin** mengecek booking saya dengan aman,  
**sehingga** hanya saya yang bisa melihat detail booking.

**Acceptance Criteria:**
- Saat booking dibuat, raw access token di-generate (cryptographic random).
- Database menyimpan hash (SHA-256) token, bukan raw.
- Guest mengakses booking via booking_code + raw token.
- Token dibandingkan: `hash(input) === stored_hash`.
- Rate limit pada endpoint cek booking.

---

## REQ-8: Booking Confirmation Page

### REQ-8.1
**Sebagai** guest,  
**setelah** booking berhasil dibuat,  
**saya ingin** melihat halaman konfirmasi dengan instruksi pembayaran,  
**sehingga** saya tahu langkah selanjutnya.

**Acceptance Criteria:**
- Halaman menampilkan: booking code, detail kamar, tanggal, total, countdown sisa waktu.
- CTA: bayar sekarang (menuju Midtrans — akan aktif di SPEC 04).
- Info: batas waktu pembayaran, apa yang terjadi jika expired.
- Link cek booking untuk diakses nanti.

---

## Constraints

- **C-1:** Harga dihitung server-side, frontend tidak mengirim nominal.
- **C-2:** DB transaction + locking wajib untuk create booking.
- **C-3:** Guest booking valid tanpa user_id.
- **C-4:** Interval half-open `[check_in, check_out)`.
- **C-5:** Booking code unique, format BKG-YYYYMM-XXXX.
- **C-6:** Access token hanya disimpan sebagai hash.
- **C-7:** Pending hold 30 menit (configurable).
- **C-8:** Promo dan poin BELUM diimplementasikan di SPEC ini (placeholder 0).
