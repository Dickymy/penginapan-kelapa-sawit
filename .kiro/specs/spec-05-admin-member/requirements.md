# SPEC 05 — Admin Reservation & Member: Requirements

> **Referensi master:** Fase 7–8, Bagian 9.8, 10.10, 12.1–12.6, 13.1–13.5  
> **Dependency:** SPEC 04 selesai  
> **Scope:** Admin booking manual, kalender, room block, Google OAuth, member dashboard, guest claim

---

## REQ-1: Admin Booking Manual

### REQ-1.1
**Sebagai** admin,  
**saya ingin** membuat booking untuk tamu tanpa melalui website checkout,  
**sehingga** booking dari WhatsApp, telepon, walk-in, dan OTA dapat dicatat.

**Acceptance Criteria:**
- Admin memilih: source (whatsapp/walk_in/phone/booking_com/agoda/traveloka/other), room, dates, guest info, guest count, price (override diizinkan + audit), payment status, notes.
- Booking tetap melalui lock + overlap check.
- Booking manual dapat langsung `confirmed` jika admin menandai sudah dibayar.
- Source bukan `website` tidak wajib Midtrans.
- Admin actor dicatat di `created_by_admin_id`.

---

## REQ-2: Admin Reservasi Management

### REQ-2.1
**Sebagai** admin,  
**saya ingin** melihat dan mengelola seluruh reservasi,  
**sehingga** operasional harian berjalan lancar.

**Acceptance Criteria:**
- List reservasi dengan filter: tanggal, status, source, payment status, room.
- Actions per booking sesuai state: detail, cancel, check-in, check-out, complete, no-show.
- Check-in menyimpan `checked_in_at`, check-out menyimpan `checked_out_at`, complete menyimpan `completed_at`.
- Setiap transisi menulis BookingStatusHistory.

---

## REQ-3: Room Block

### REQ-3.1
**Sebagai** admin,  
**saya ingin** memblokir kamar untuk maintenance atau alasan lain,  
**sehingga** kamar tidak bisa dipesan selama periode tersebut.

**Acceptance Criteria:**
- Admin membuat room block: room, start_date, end_date, reason_type, reason, notes.
- Room block conflict dengan booking yang ada harus ditampilkan sebelum save.
- Availability engine sudah memperhitungkan room block (SPEC 03).
- Admin bisa delete room block.

---

## REQ-4: Google OAuth (Member)

### REQ-4.1
**Sebagai** pengunjung,  
**saya ingin** login/register menggunakan akun Google,  
**sehingga** prosesnya lebih cepat tanpa mengingat password.

**Acceptance Criteria:**
- Tombol "Masuk dengan Google" di halaman login dan register.
- Flow: redirect ke Google → callback → cek social_account → login atau create user.
- Jika email Google terverifikasi dan user belum ada → buat user + social_account + auto-verify email.
- Jika email Google cocok user existing dan provider verified → link + login.
- Jika tidak aman untuk auto-link → minta flow eksplisit.
- Tidak menyimpan access/refresh token jika tidak diperlukan.
- Laravel Socialite digunakan.

---

## REQ-5: Member Dashboard

### REQ-5.1
**Sebagai** member,  
**saya ingin** melihat booking aktif, riwayat, dan poin saya,  
**sehingga** saya dapat memantau reservasi dan manfaat membership.

**Acceptance Criteria:**
- Dashboard: nama, saldo poin (cache), estimasi nilai Rupiah, jumlah booking aktif.
- Booking Saya: tab aktif / selesai / batal. Per booking: code, room, dates, status, total.
- Poin Saya: saldo, nilai, transaksi (placeholder — detail di SPEC 06).
- Profil: nama, email, WhatsApp, avatar. Edit nama/WA. Perubahan email memerlukan re-verifikasi.

---

## REQ-6: Guest Booking Claim

### REQ-6.1
**Sebagai** member,  
**saya ingin** mengklaim guest booking yang saya buat sebelum register,  
**sehingga** booking muncul di riwayat saya dan saya mendapat poin.

**Acceptance Criteria:**
- Claim hanya jika: booking `user_id IS NULL`, email member terverifikasi cocok dengan `guest_email` booking.
- Token claim atau email match sebagai verifikasi.
- Setelah claim: `user_id` terisi, `claimed_at` terisi, `claim_method` tercatat.
- Booking yang sudah di-claim tidak bisa di-claim lagi.
- Claim berdasarkan nama saja TIDAK diizinkan.

---

## Constraints

- **C-1:** Admin booking tetap wajib overlap check + lock.
- **C-2:** Google OAuth menggunakan Socialite, state protection aktif.
- **C-3:** Claim hanya via email terverifikasi, bukan nama/WhatsApp.
- **C-4:** Room block tidak boleh dibuat diam-diam jika ada booking conflict.
- **C-5:** Setiap transisi booking dicatat di status history.
