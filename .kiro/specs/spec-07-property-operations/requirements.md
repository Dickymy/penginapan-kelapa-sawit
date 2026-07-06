# SPEC 07 — Property Operations: Requirements

> **Referensi master:** Fase 11–12, Bagian 9.7, 13.7–13.11, 15–16  
> **Dependency:** SPEC 06 selesai  
> **Scope:** Invoice PDF, cancellation workflow, refund, WhatsApp link, complete+award integration

---

## REQ-1: Invoice PDF

### REQ-1.1
**Sebagai** tamu/member/admin,  
**saya ingin** mengunduh invoice booking dalam format PDF,  
**sehingga** saya memiliki bukti transaksi resmi.

**Acceptance Criteria:**
- Invoice hanya tersedia untuk booking yang sudah paid/confirmed/completed.
- Invoice berisi: nama penginapan, invoice number (INV-YYYYMM-XXXX), booking code, guest info, room snapshot, dates, nights, price/night, subtotal, diskon promo/poin, total, metode pembayaran, tanggal bayar.
- Data dari snapshot booking, BUKAN dari room type terbaru.
- Invoice number generated saat pertama kali diminta (lazy generate).
- Authorization: member hanya miliknya, guest perlu token/verifikasi, admin semua.

---

## REQ-2: Complete + Loyalty Award Integration

### REQ-2.1
**Sebagai** sistem,  
**saya ingin** otomatis memberikan poin saat booking di-complete,  
**sehingga** member tidak perlu menunggu proses manual.

**Acceptance Criteria:**
- Saat admin menandai booking `completed`, LoyaltyPointService.awardForCompletedBooking dipanggil.
- Idempotent (tidak double award).
- Hanya jika booking punya user_id dan source eligible.

---

## REQ-3: Cancellation Workflow

### REQ-3.1
**Sebagai** admin,  
**saya ingin** proses cancel yang terstruktur,  
**sehingga** semua side effect (promo release, point reversal) ditangani.

**Acceptance Criteria:**
- Cancel booking: transition status + release promo reservation + reverse point redemption.
- Jika booking sudah paid → set `needs_attention` untuk evaluasi refund (tidak auto-refund).
- Cancel reason + notes wajib.
- Status history dicatat.

---

## REQ-4: Refund (Admin Initiated)

### REQ-4.1
**Sebagai** admin,  
**saya ingin** mencatat refund untuk booking yang dibatalkan,  
**sehingga** keuangan terlacak.

**Acceptance Criteria:**
- Admin memilih payment paid → input amount + reason.
- Refund amount tidak boleh melebihi gross_amount.
- Refund dicatat di tabel `refunds` (migration baru).
- Status payment diupdate jika full refund.
- Jika poin sudah di-earn → evaluasi point reversal.

---

## REQ-5: WhatsApp Integration (Direct Link)

### REQ-5.1
**Sebagai** tamu/admin,  
**saya ingin** membuka WhatsApp dengan pesan pre-filled,  
**sehingga** komunikasi tentang booking lebih mudah.

**Acceptance Criteria:**
- Link WhatsApp menggunakan `https://wa.me/{number}?text={encoded_message}`.
- Nomor dari settings.
- Template: ringkasan booking (code, room, dates, total).
- Tidak ada API WhatsApp berbayar.
- Token akses TIDAK disertakan dalam pesan.

---

## REQ-6: Audit Log (Aksi Sensitif)

### REQ-6.1
**Sebagai** sistem,  
**saya ingin** aksi sensitif dicatat di audit_logs,  
**sehingga** ada trail untuk investigasi.

**Acceptance Criteria:**
- Tabel `audit_logs` (migration baru).
- Dicatat: cancel, refund, complete, check-in, check-out, point adjustment, manual claim.
- Log: actor, action, subject, before/after (aman), IP, timestamp.
- Tidak log password/token/secret.

---

## Constraints

- **C-1:** Invoice menggunakan data snapshot, bukan harga terbaru.
- **C-2:** Tidak auto-refund; admin memutuskan.
- **C-3:** WhatsApp hanya direct link, bukan API.
- **C-4:** Audit log tidak menyimpan data sensitif.
