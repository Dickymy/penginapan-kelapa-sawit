# SPEC 08 — Reports, Security & Release: Requirements

> **Referensi master:** Fase 13–15, Bagian 14, 18, 20–22  
> **Dependency:** SPEC 07 selesai  
> **Scope:** Pengeluaran, laporan, admin dashboard real data, hardening, production readiness

---

## REQ-1: Expense Management

### REQ-1.1
**Sebagai** admin,  
**saya ingin** mencatat pengeluaran operasional,  
**sehingga** estimasi laba bersih dapat dihitung.

**Acceptance Criteria:**
- Admin CRUD expense: expense_date, category, amount (Rupiah integer), description, receipt (upload optional).
- Kategori: listrik, air, internet, laundry, perlengkapan_kamar, perbaikan, gaji, other.
- Tabel `expenses` (migration sudah di design SPEC 02 tapi belum dibuat — perlu dibuat sekarang).

---

## REQ-2: Reports

### REQ-2.1
**Sebagai** admin,  
**saya ingin** melihat laporan pendapatan dan hunian,  
**sehingga** saya memahami performa bisnis.

**Acceptance Criteria:**
- Laporan Pendapatan: filter tanggal, total pendapatan per source, jumlah booking.
- Laporan Hunian: occupied room nights / available room nights × 100%.
- Laporan Estimasi Laba: pendapatan - pengeluaran = estimasi laba (dengan disclaimer).
- Laporan Sumber Booking: count + revenue per source.

---

## REQ-3: Admin Dashboard Real Data

### REQ-3.1
**Sebagai** admin,  
**saya ingin** dashboard menampilkan data operasional real-time,  
**sehingga** saya tahu kondisi penginapan saat ini.

**Acceptance Criteria:**
- Booking hari ini (check-in hari ini).
- Check-in hari ini (yang sudah check-in).
- Kamar terisi saat ini (checked_in count).
- Pendapatan bulan berjalan (paid bookings).
- Booking pending attention (needs_attention = true).
- Booking terbaru (5 terakhir).

---

## REQ-4: Security Hardening

### REQ-4.1
**Sebagai** sistem,  
**saya ingin** keamanan diperkuat sebelum production,  
**sehingga** data tamu dan transaksi terlindungi.

**Acceptance Criteria:**
- Rate limiting pada: login, register, forgot-password, cek-booking, webhook.
- CSRF aktif di seluruh form (sudah default).
- Session secure cookie settings documented.
- .env.example lengkap tanpa secret.
- Tidak ada secret di committed files (verified).
- noindex pada halaman booking sensitif.

---

## Constraints

- **C-1:** Laporan laba adalah estimasi, bukan akuntansi resmi (disclaimer wajib).
- **C-2:** Pengeluaran sederhana, bukan modul akuntansi.
- **C-3:** Rate limiting tidak boleh memblokir Midtrans webhook.
