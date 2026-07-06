# SPEC 06 — Loyalty & Promotion: Requirements

> **Referensi master:** Fase 9–10, Bagian 9.6, 9.3, 10.7–10.9  
> **Dependency:** SPEC 05 selesai  
> **Scope:** Loyalty point ledger, earn/redeem/expire/reversal, promotion CRUD & validation

---

## REQ-1: Loyalty Earn

### REQ-1.1
**Sebagai** sistem,  
**saya ingin** memberikan poin loyalitas saat booking completed,  
**sehingga** member mendapat reward atas transaksi.

**Acceptance Criteria:**
- Poin diberikan HANYA saat booking berstatus `completed`.
- Formula: `floor(eligible_amount / earn_divisor)` — config default 1000.
- Eligible amount = total yang dibayar setelah diskon, tidak termasuk potongan poin.
- Sumber eligible: website, whatsapp, walk_in. OTA (booking_com, agoda, traveloka) TIDAK eligible.
- Booking harus memiliki `user_id` (member).
- Earn hanya sekali per booking (idempotency key: `earn:booking:{id}`).
- Poin lot memiliki `expires_at = earned_at + 18 bulan`.
- `remaining_points` = jumlah poin saat dibuat.
- Update `user.loyalty_balance_cache`.

---

## REQ-2: Loyalty Redemption

### REQ-2.1
**Sebagai** member,  
**saya ingin** menggunakan poin sebagai potongan saat checkout,  
**sehingga** saya mendapat diskon dari akumulasi poin.

**Acceptance Criteria:**
- Minimum 100 poin untuk redeem.
- 1 poin = Rp50 (config).
- Maksimum potongan: 20% dari subtotal (config).
- Promo dan poin TIDAK boleh bersamaan (V1).
- Redemption menggunakan FIFO (lot expiry terdekat → created_at terdekat).
- Debit poin via `loyalty_transactions` type `redeem` + `loyalty_point_allocations`.
- Decrement `remaining_points` pada lot.
- Update cache saldo user.
- Idempotency key: `redeem:booking:{id}`.

### REQ-2.2
**Sebagai** sistem,  
**saya ingin** poin dikembalikan jika booking dibatalkan/expired,  
**sehingga** member tidak kehilangan poin untuk transaksi yang gagal.

**Acceptance Criteria:**
- Reversal membuat transaksi `reversal` (bukan delete redeem).
- Poin dikembalikan ke lot asal.
- `remaining_points` lot dinaikkan kembali.
- Reversal idempotent.

---

## REQ-3: Loyalty Expiry

### REQ-3.1
**Sebagai** sistem,  
**saya ingin** poin yang sudah lewat masa berlaku otomatis expired,  
**sehingga** saldo loyalitas selalu akurat.

**Acceptance Criteria:**
- Scheduler harian: cari lot `remaining_points > 0` dan `expires_at <= now`.
- Buat transaksi `expire` negatif.
- Create allocation.
- Set `remaining_points = 0`.
- Update cache.
- Idempotency key: `expire:loyalty_transaction:{credit_id}`.

---

## REQ-4: Admin Loyalty Management

### REQ-4.1
**Sebagai** admin,  
**saya ingin** melihat saldo dan riwayat poin member,  
**sehingga** saya dapat memantau dan mengoreksi jika diperlukan.

**Acceptance Criteria:**
- Admin melihat: daftar user dengan saldo, detail ledger per user.
- Admin dapat melakukan adjustment (positif/negatif) dengan reason wajib.
- Adjustment membuat transaksi baru, BUKAN edit transaksi lama.
- Actor dicatat.

---

## REQ-5: Promotion CRUD & Validation

### REQ-5.1
**Sebagai** admin,  
**saya ingin** membuat dan mengelola promo kode,  
**sehingga** pengunjung mendapat insentif untuk memesan.

**Acceptance Criteria:**
- Admin CRUD: code (uppercase), name, type (percentage/fixed), value, starts_at, ends_at, minimum_booking_amount, maximum_discount (cap persen), usage_quota, is_active.
- Code unique.

### REQ-5.2
**Sebagai** sistem,  
**saya ingin** validasi promo sepenuhnya backend,  
**sehingga** tidak ada manipulasi kuota atau diskon.

**Acceptance Criteria:**
- Validasi: aktif, dalam rentang waktu, minimum tercapai, quota tersedia.
- Quota dilindungi transaction + lock.
- `promotion_usages` lifecycle: reserved → consumed (saat paid) / released (saat expired/cancel).
- Per-user limit jika dikonfigurasi.

---

## REQ-6: Pricing Integration (Promo + Poin di Checkout)

### REQ-6.1
**Sebagai** pengunjung/member,  
**saya ingin** memasukkan kode promo atau menggunakan poin saat checkout,  
**sehingga** saya mendapat potongan harga.

**Acceptance Criteria:**
- Checkout form memiliki input kode promo (guest/member) dan input poin (member only).
- Promo dan poin mutually exclusive di V1.
- Server recalculates quote dengan promo/poin yang dipilih.
- Diskon ditampilkan di ringkasan sebelum submit.

---

## Constraints

- **C-1:** Ledger immutable — jangan edit/delete transaksi lama.
- **C-2:** Idempotency key wajib untuk setiap mutasi loyalty.
- **C-3:** Promo + poin mutually exclusive V1.
- **C-4:** Frontend tidak mengirim nominal diskon — server yang menghitung.
- **C-5:** Quota promo dilindungi transaction + lock.
