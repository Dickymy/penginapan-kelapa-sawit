# 🏨 Rencana Penyempurnaan Website Penginapan Kelapa Sawit (v3 — Final)
### Dokumen Kerja untuk AI Coding Agent — Terarah, Bertahap, Anti-Bug

> Dokumen ini adalah versi final dari rencana penyempurnaan. Setiap asumsi teknis sudah di-cross-check terhadap **codebase aktual** (23 model, 11 service, 8 enum, 27 migrasi, 21 file test). Tujuannya: instruksi kerja yang bisa langsung dieksekusi oleh AI agent tanpa menebak-nebak, tanpa merusak fitur yang sudah berjalan, dan menghasilkan tampilan yang lebih rapi.

---

## 0. Prinsip Kerja Wajib untuk AI Agent

Sebelum mengerjakan fitur apa pun, agent **HARUS** mengikuti aturan berikut di setiap fase:

1. **Baseline dulu, baru ubah.**
   Jalankan `php artisan test` dan catat hasilnya secara eksak (jumlah tests DAN assertions) **sebelum** menyentuh kode. Angka ini menjadi baseline — setiap perubahan tidak boleh mengurangi angka ini. Jika ada test yang sudah gagal sebelum perubahan, laporkan ke user — jangan diperbaiki bersamaan dengan fitur baru agar mudah dilacak.

2. **Satu fitur = satu branch/PR, satu langkah kerja.**
   Jangan mengerjakan dua fitur besar sekaligus dalam satu sesi perubahan. Urutan wajib mengikuti **Roadmap Fase** di Bagian 2 — jangan loncat fase kecuali user memerintahkan.

3. **Migration harus reversible.**
   Setiap migration baru wajib punya method `down()` yang benar-benar membatalkan `up()`. Jalankan `php artisan migrate:rollback` sebagai uji coba sebelum lanjut, lalu `migrate` lagi.

4. **Jangan ubah struktur tabel yang sudah dipakai fitur lain tanpa memeriksa dependency.**
   Sebelum mengubah tabel `bookings`, `room_types`, `payments`, dsb — cari semua file yang mereferensikannya (`grep -r` pada model, controller, view, service) agar tidak ada query/relasi yang patah.

5. **Validasi di server, bukan hanya di Alpine.js/JS.**
   Semua form baru (review, kontak, add-on, dsb) wajib memakai Laravel Form Request (`php artisan make:request`) dengan aturan validasi eksplisit. Jangan andalkan validasi client-side saja. **Catatan**: controller Public dan Member existing saat ini memakai inline `$request->validate()` — untuk fitur baru, gunakan Form Request sebagai improvement dari pattern lama.

6. **Otorisasi eksplisit — ikuti pattern existing.**
   Codebase saat ini **TIDAK** menggunakan Laravel Policy (`app/Policies/` tidak ada). Otorisasi dilakukan via middleware (`auth`, `auth:admin`, `verified`) dan inline checks di controller/service (contoh: `BookingAccessService`). Untuk fitur baru, **ikuti pattern ini** — cek kepemilikan data di controller atau Form Request. JANGAN buat Policy baru kecuali diminta eksplisit, agar arsitektur konsisten.

7. **Idempotensi untuk proses otomatis.**
   Event email, scheduled command (H-1 check-in, dsb) wajib aman dijalankan berulang (tidak mengirim email dobel jika command sempat jalan 2x). Gunakan flag seperti `reminder_sent_at` di database.

8. **Jangan mengubah alur pembayaran/booking engine yang sudah "anti-double-booking".**
   Fitur baru (add-on, harga dinamis) harus **menambah** ke `PricingService`/`BookingService` yang sudah ada, bukan menulis ulang logic kunci row-locking yang sudah terbukti aman. Khusus `BookingService.createBooking()` — method ini menggunakan `DB::transaction()` + `lockForUpdate()` + `AvailabilityService.assertRoomAvailableForBooking()` — JANGAN ubah alur ini.

9. **Setiap fitur baru wajib punya:**
   - Migration (jika perlu tabel/kolom baru), dengan `down()` yang benar
   - Model + relasi + **Factory** (untuk testing)
   - Form Request (validasi server-side)
   - Controller (thin — logic berat taruh di Service)
   - Test (minimal: 1 happy path, 1 validation failure, 1 authorization failure)
   - View yang konsisten dengan design system (lihat Bagian 4)

10. **Setelah setiap fitur selesai:**
    Jalankan ulang `php artisan test`. Semua test lama harus tetap PASS + test baru harus PASS. Jika ada yang merah, **perbaiki sebelum lanjut ke fitur berikutnya.**

11. **Jangan hardcode data dummy ke production seed** kecuali diminta eksplisit (mis. FAQ atau nearby places awal boleh diisi contoh, tapi tandai jelas sebagai "contoh, silakan admin edit").

12. **Bahasa, mata uang, dan format — pakai ulang pattern existing:**
    - **Rupiah**: Gunakan pola `'Rp' . number_format($amount, 0, ',', '.')` yang sudah dipakai di `Booking::getFormattedTotalAttribute()` dan `RoomType::getFormattedBasePriceAttribute()`. Untuk view, pakai `Rp{{ number_format($amount, 0, ',', '.') }}`.
    - **Tanggal**: Gunakan `->format('d M Y')` untuk tanggal, `->format('d M Y, H:i') . ' WITA'` untuk datetime, via Carbon (sama dengan view existing).
    - **Bahasa**: Semua UI baru pakai Bahasa Indonesia. `APP_LOCALE=id` dan `APP_FAKER_LOCALE=id_ID` sudah dikonfigurasi.

13. **Email WAJIB async via queue.**
    Semua Mailable class baru harus implements `ShouldQueue` agar tidak memblokir response HTTP. Queue database sudah dikonfigurasi (`QUEUE_CONNECTION=database`) dan `composer dev` sudah menjalankan `php artisan queue:listen`. Pastikan queue worker aktif saat testing email, atau gunakan `QUEUE_CONNECTION=sync` di `.env.testing`.

---

## 1. Status Saat Ini (Referensi Teknis — Tidak Diubah)

### Fondasi yang Sudah Solid

Fondasi berikut **tidak perlu disentuh** kecuali disebutkan dalam fitur baru:

| Komponen | Detail |
|---|---|
| Booking engine | 5 lapis anti-double-booking (`DB::transaction`, `lockForUpdate`, `AvailabilityService`, idempotency key, `BookingStatusHistory`) |
| Payment | Midtrans Snap (webhook + rekonsiliasi otomatis tiap 5 menit) |
| Loyalty | Poin FIFO dengan expiry otomatis harian |
| Promo | Kode promo dengan reserve/consume/release lifecycle |
| Invoice | PDF via DomPDF |
| Auth | Fortify (login/register/forgot/verify) + Google OAuth via Socialite |
| Dashboard | Member (booking, poin, profil, claim) & Admin (full CRUD, laporan) |
| Galeri | Multi-varian foto (thumb/medium/large) via Intervention Image |
| Laporan | Revenue, okupansi, profit, sources |
| Lainnya | Custom error pages, rate limiting, responsive Tailwind v4 |

### Arsitektur Kunci (Referensi untuk AI Agent)

**Services (11 file di `app/Services/`):**
- `PricingService` — `calculateQuote()`, `calculateQuoteWithPromo()`, `calculateQuoteWithPoints()`
- `BookingService` — `createGuestBooking()`, `createMemberBooking()`, `createManualBooking()`, `expirePendingBooking()`
- `MidtransPaymentService` — `createOrResumePayment()`, `handleWebhook()`, `reconcilePayment()`
- `AvailabilityService` — `searchAvailableRoomTypes()`, `findAvailableRooms()`, `isRoomAvailable()`, `assertRoomAvailableForBooking()`
- `BookingAccessService`, `BookingClaimService`, `DocumentSequenceService`, `InvoiceService`, `LoyaltyPointService`, `PromotionService`, `ImageUploadService`

**Enum status booking (`App\Enums\BookingStatus`):**
`PendingPayment` → `Confirmed` → `CheckedIn` → `CheckedOut` → `Completed`
Terminal: `Cancelled`, `Expired`, `NoShow`

**Events/Listeners: BELUM ADA.**
`app/Events/` dan `app/Listeners/` tidak ada. Side effects saat ini dijalankan secara imperatif di dalam service methods.

**Policies: BELUM ADA.**
`app/Policies/` tidak ada. Otorisasi via middleware + inline checks.

**Factories: HANYA `UserFactory`.**
Model lain (Booking, Room, RoomType, Payment, dll) belum punya factory.

**Layouts Blade (3 layout):** `layouts/public.blade.php`, `layouts/admin.blade.php`, `layouts/member.blade.php`

**Homepage sections yang sudah ada:** Hero, Search Widget (#cari-kamar), Booking Options (langsung vs member), Tipe Kamar (3-kolom grid), Galeri Preview, Tentang, Lokasi, WhatsApp CTA, Informasi Penting (kebijakan).

**Cakupan total:** 78 view, 31 controller, 23 model, 11 service, 8 enum, 27 migrasi, 21 file test.

---

## 2. Roadmap Fase (Wajib Berurutan)

Setiap fase dikerjakan **tuntas dan lulus test** sebelum lanjut ke fase berikutnya. Jangan gabungkan fase.

| Fase | Fitur | Alasan Urutan |
|---|---|---|
| **Fase 0** | Prerequisites (Factories + Baseline) | Fondasi testing — dibutuhkan oleh semua fase |
| **Fase 1** | Notifikasi Email Otomatis | Fondasi komunikasi — dipakai fase-fase berikutnya (review, reminder) |
| **Fase 2** | Sistem Ulasan & Rating | Social proof, butuh trigger email pasca-checkout dari Fase 1 |
| **Fase 3** | Halaman FAQ | Cepat dikerjakan, dampak tinggi ke konversi |
| **Fase 4** | Harga Dinamis (weekend/libur) | Berdampak revenue, tapi berisiko sedang — perlu hati-hati di `PricingService` |
| **Fase 5** | Layanan Tambahan (Add-ons) | Bangun di atas `PricingService` yang sudah diperluas di Fase 4 |
| **Fase 6** | Kalender Admin Visual | Efisiensi operasional |
| **Fase 7** | Filter & Sortir Kamar | UX kecil, low-risk |
| **Fase 8** | Formulir Kontak | Butuh email dari Fase 1 untuk auto-reply |
| **Fase 9** | Informasi Sekitar/Atraksi | Konten tambahan, low-risk |
| **Fase 10** | Modifikasi Booking oleh Tamu | Risiko tinggi (berkaitan dengan pricing + booking engine) — dikerjakan setelah semua fondasi matang |
| **Fase 11** | Export Data Admin (CSV/Excel) | Utilitas, low-risk, dikerjakan kapan saja tapi taruh terakhir karena non-guest-facing |

> **Rekomendasi:** Kerjakan **Fase 0–3 dulu** sebagai paket pertama (dampak besar, risiko rendah–sedang), evaluasi bersama user, baru lanjut Fase 4 dst.

---

## 3. Spesifikasi Teknis per Fitur

### Fase 0 — Prerequisites (Factories + Baseline) 🔧

**Tujuan:** Menyiapkan fondasi testing yang dibutuhkan oleh semua fase berikutnya.

**Langkah 1 — Catat baseline test:**
```bash
php artisan test
# Catat eksak: "Tests: XX, Assertions: YY — OK"
# Angka ini menjadi baseline yang tidak boleh berkurang
```

**Langkah 2 — Buat factories untuk model inti:**

Factory yang perlu dibuat (pakai `php artisan make:factory`):

1. **`RoomTypeFactory`** — dengan data: name, slug, base_price, capacity, bed_count, bed_type, short_description, description, is_active
2. **`RoomFactory`** — relasi ke RoomType, dengan data: name, floor, status (RoomStatus enum)
3. **`BookingFactory`** — relasi ke Room (dan User opsional), dengan:
   - States untuk setiap `BookingStatus`: `pendingPayment()`, `confirmed()`, `checkedIn()`, `checkedOut()`, `completed()`, `cancelled()`, `expired()`
   - Default mengisi semua snapshot fields (`room_type_name_snapshot`, `room_name_snapshot`, `price_per_night_snapshot`, `subtotal`, `total_amount`, dll)
   - Default `guest_name`, `guest_email`, `guest_whatsapp`, `check_in` (besok), `check_out` (lusa), `nights` (1)
4. **`PaymentFactory`** — relasi ke Booking, dengan data: provider, gross_amount, status (PaymentStatus enum)

**Langkah 3 — Verifikasi factory berfungsi:**
Buat test sederhana yang membuat instance dari setiap factory dan assert berhasil disimpan ke database. Jalankan `php artisan test` — baseline + test baru semua harus PASS.

**Acceptance criteria:**
- 4 factory baru berfungsi tanpa error
- Semua test baseline tetap PASS
- Factory BookingFactory bisa membuat booking dengan semua state yang valid

---

### Fase 1 — Notifikasi Email Otomatis 📧

**Migration — Tambahkan kolom penanda pengiriman ke `bookings`:**
Satu migration baru, tambahkan kolom-kolom berikut (semua nullable timestamp):
- `confirmation_email_sent_at`
- `payment_email_sent_at`
- `reminder_email_sent_at`
- `checkout_email_sent_at`
- `cancellation_email_sent_at`

Method `down()` harus `dropColumn()` untuk semua 5 kolom.

**Events & Listeners — Buat dari Nol:**

> **PENTING:** Saat ini codebase BELUM menggunakan Events/Listeners sama sekali. Side effects dijalankan secara imperatif di dalam service methods. Untuk Fase 1, buat Events & Listeners **HANYA untuk kebutuhan email**. JANGAN memindahkan logic non-email yang sudah ada ke event/listener — jangan refactor arsitektur existing.

Buat 3 events dan 3 listeners:

| Event | Dispatch dari | Listener |
|---|---|---|
| `BookingCreated` (property: `Booking $booking`) | `BookingService` — setelah booking berhasil dibuat di akhir `createBooking()`, di luar transaction | `SendBookingConfirmationListener` → dispatch `BookingConfirmationMail` |
| `PaymentConfirmed` (property: `Booking $booking`, `Payment $payment`) | `MidtransPaymentService::handleWebhook()` — setelah status booking diubah ke `confirmed` | `SendPaymentSuccessListener` → dispatch `PaymentSuccessMail` |
| `BookingCancelled` (property: `Booking $booking`) | `BookingService::expirePendingBooking()` dan controller cancel booking di Admin | `SendBookingCancelledListener` → dispatch `BookingCancelledMail` |

**Cara dispatch event:** Tambahkan `event(new BookingCreated($booking))` di akhir method yang relevan. JANGAN dispatch di dalam `DB::transaction()` — dispatch setelah transaction commit agar email tidak terkirim jika transaction rollback. Gunakan `DB::afterCommit()` atau dispatch di luar block transaction.

**Mailable classes** (`app/Mail/`, semua `implements ShouldQueue`):
1. `BookingConfirmationMail` — trigger: setelah booking dibuat (status `pending_payment`)
2. `PaymentSuccessMail` — trigger: dari webhook saat status jadi `confirmed`, lampirkan link invoice PDF (`route('booking.invoice', $booking->booking_code)`)
3. `CheckinReminderMail` — trigger: scheduled command, kirim H-1 untuk booking `confirmed`
4. `PostCheckoutMail` — trigger: scheduled command, kirim untuk booking yang `checked_out` kemarin, berisi ajakan ulasan (link ke form review, Fase 2) + info poin
5. `BookingCancelledMail` — trigger: saat booking dibatalkan manual atau expired otomatis

**Email target:** Kirim ke `$booking->guest_email`. Jika `guest_email` null (booking tanpa email), **skip pengiriman** — jangan error/crash. Cek `if ($booking->guest_email)` sebelum dispatch.

**Template email:** Satu layout dasar `resources/views/mail/layout.blade.php` bergaya konsisten dengan branding situs:
- Logo/nama property di header
- Warna utama green/emerald (palet `primary-600`)
- Footer: alamat property, link WhatsApp, "Penginapan Kelapa Sawit"
- Dipakai ulang oleh 5 email di atas via `@extends('mail.layout')` + `@section` — bukan 5 template terpisah tanpa struktur bersama

**Scheduled commands** (`app/Console/Commands/`):
1. `SendCheckinReminders` — cari booking `status = confirmed` dengan `check_in = tomorrow` DAN `reminder_email_sent_at IS NULL`, kirim email, set `reminder_email_sent_at = now()`
2. `SendPostCheckoutEmails` — cari booking `status = checked_out` dengan `checked_out_at` = kemarin DAN `checkout_email_sent_at IS NULL`, kirim email, set `checkout_email_sent_at = now()`

**Daftarkan di `routes/console.php`:**
```php
Schedule::command('email:send-checkin-reminders')->dailyAt('09:00')->withoutOverlapping();
Schedule::command('email:send-post-checkout')->dailyAt('10:00')->withoutOverlapping();
```

**Konfigurasi `.env.example`:**
Variabel MAIL sudah ada di `.env.example` (MAIL_MAILER=log, dst). **Jangan ubah** — cukup pastikan sistem tetap berjalan tanpa error saat `MAIL_MAILER=log` (email masuk log, bukan SMTP).

**Listener harus idempoten:**
Sebelum mengirim email, cek flag `*_email_sent_at`:
```php
if ($booking->confirmation_email_sent_at) return; // sudah dikirim
// ... kirim email ...
$booking->update(['confirmation_email_sent_at' => now()]);
```

**Test wajib:**
1. Happy path: Booking dibuat → event `BookingCreated` di-dispatch → email masuk queue
2. Idempotensi: Listener dipanggil 2x → email hanya terkirim 1x
3. Null email: Booking tanpa `guest_email` → tidak error, email di-skip
4. Command reminder: Hanya kirim ke booking `confirmed` yang check-in besok dan belum dikirim
5. Command post-checkout: Hanya kirim ke booking `checked_out` kemarin dan belum dikirim

**Acceptance criteria:**
- Booking baru → email konfirmasi masuk queue (cek log/Mailtrap)
- Webhook payment sukses → email pembayaran + link invoice valid
- Command reminder tidak mengirim dobel jika dijalankan 2x di hari yang sama
- Booking tanpa email guest tidak menyebabkan error
- Semua test baseline tetap PASS + test baru PASS

---

### Fase 2 — Sistem Ulasan & Rating ⭐

**Migration `reviews`:**
```
id, user_id (FK users), booking_id (FK bookings, unique — 1 booking = 1 review),
rating (tinyInteger unsigned, 1-5, not null), title (string 150, nullable),
comment (text, not null, max 2000 karakter di validasi),
is_published (boolean, default false),
admin_reply (text, nullable), replied_at (timestamp, nullable),
timestamps, softDeletes
```
Gunakan `softDeletes` agar review yang dihapus admin bisa dipulihkan jika perlu.
Unique constraint pada `booking_id` — satu booking hanya boleh satu review.

**Aturan bisnis penting (jangan dilewatkan):**
- Review hanya bisa dibuat jika `$booking->status === BookingStatus::CheckedOut` ATAU `BookingStatus::Completed` DAN `$booking->user_id === auth()->id()`
- Satu booking hanya boleh direview satu kali (unique constraint + validasi di Form Request: `Rule::unique('reviews', 'booking_id')`)
- Review baru default `is_published = false` (masuk moderasi admin) — **jangan langsung tampil publik**
- Admin bisa publish/unpublish dan membalas (`admin_reply`)
- Rate limit submit review: pakai throttle middleware (mis. `throttle:5,60` — max 5 review per jam per user)

**Model `Review`:**
```php
// Review.php
belongsTo(User::class)
belongsTo(Booking::class)
// Accessor: booking.room -> room.roomType untuk dapat RoomType

// Scope
scopePublished($query) { $query->where('is_published', true); }
```

**Relasi Review → RoomType (penting — bukan hasMany langsung):**
Review terhubung ke RoomType melalui chain: `Review → Booking → Room → RoomType`. Untuk menampilkan rating rata-rata per room type, **JANGAN** buat `hasMany` langsung di RoomType. Gunakan salah satu pendekatan:

*Opsi A (Rekomendasi):* Query scope / accessor di RoomType:
```php
// RoomType.php
public function getAverageRatingAttribute(): ?float
{
    return Review::published()
        ->whereHas('booking.room', fn($q) => $q->where('room_type_id', $this->id))
        ->avg('rating');
}
public function getReviewCountAttribute(): int
{
    return Review::published()
        ->whereHas('booking.room', fn($q) => $q->where('room_type_id', $this->id))
        ->count();
}
```

*Opsi B:* Cache rating di kolom `room_types.average_rating` dan `room_types.review_count`, update via observer/event saat review dipublish/unpublish.

Pilih Opsi A untuk fase awal (simpler), migrate ke Opsi B jika performa jadi masalah.

**Factory `ReviewFactory`:**
Buat factory dengan relasi ke User dan Booking (gunakan `BookingFactory` dari Fase 0 dengan state `checkedOut`).

**Form Request `StoreReviewRequest`:**
```php
'booking_id' => ['required', 'exists:bookings,id', Rule::unique('reviews', 'booking_id')],
'rating' => ['required', 'integer', 'min:1', 'max:5'],
'title' => ['nullable', 'string', 'max:150'],
'comment' => ['required', 'string', 'min:10', 'max:2000'],
```
Tambahkan validasi otorisasi di `authorize()`: booking milik user yang login DAN status `checked_out`/`completed`.

**Controller:**
- `Member\ReviewController@create` — tampilkan form review, validasi booking eligible
- `Member\ReviewController@store` — simpan review (via Form Request), kirim notifikasi email ke admin (gunakan infrastruktur Mail Fase 1: `NewReviewNotificationMail` ke alamat admin/`MAIL_FROM_ADDRESS`)
- `Admin\ReviewController@index` — daftar review (filter: semua/pending/published)
- `Admin\ReviewController@publish` — toggle `is_published`
- `Admin\ReviewController@reply` — simpan `admin_reply` dan `replied_at`

**UI:**
1. **Kartu tipe kamar** (`rooms/index.blade.php` & `rooms/show.blade.php`): Tampilkan rata-rata bintang (ikon SVG) + jumlah ulasan (hanya `is_published = true`). Jika belum ada review, tampilkan "Belum ada ulasan".
2. **Widget "Ulasan Tamu" di homepage:** Tambahkan section baru setelah section "Tentang Penginapan" — ambil 3–6 review published dengan rating tertinggi/terbaru. Gunakan kartu review dengan: nama tamu (first name + initial), bintang, kutipan komentar (truncate 150 karakter), nama tipe kamar.
3. **Dashboard member** (`member/dashboard`): Daftar booking yang eligible untuk direview (status `checked_out`/`completed`, belum ada review), dengan tombol "Tulis Ulasan".
4. **Rating bintang SVG:** Sediakan partial `resources/views/components/star-rating.blade.php` yang menerima parameter `$rating` (float) dan menampilkan 5 bintang SVG (terisi/kosong/setengah). Gunakan konsisten di semua tempat — **jangan pakai emoji ⭐ di production**.
5. **Empty state:** Halaman/widget tanpa review: tampilkan pesan ramah "Belum ada ulasan. Jadilah yang pertama!" dengan ikon ilustratif.

**Test wajib:**
1. Happy path: User dengan booking `checked_out` berhasil submit review
2. Authorization: User A tidak bisa review booking milik User B → 403
3. Status guard: User tidak bisa review booking yang masih `confirmed` → validation error
4. Uniqueness: Submit review 2x untuk booking yang sama → validation error
5. Visibility: Review `is_published = false` tidak muncul di halaman publik
6. Admin: Admin bisa publish dan reply review

**Acceptance criteria:**
- Tamu tidak bisa review booking orang lain
- Tamu tidak bisa review booking yang belum checkout
- Review tidak tampil publik sebelum di-approve admin
- Rating rata-rata terhitung benar di kartu kamar
- Empty state informatif saat belum ada review
- Admin mendapat notifikasi email saat review baru masuk

---

### Fase 3 — Halaman FAQ ❓

**Migration `faqs`:**
```
id, question (string, not null), answer (text, not null),
category (string 100, nullable — mis. "Pemesanan", "Fasilitas", "Pembayaran"),
sort_order (unsignedInteger, default 0),
is_active (boolean, default true),
timestamps
```

**Model `Faq`:** Scope `scopeActive()`, `scopeOrdered()` (`orderBy('sort_order')`).

**Factory `FaqFactory`:** Dengan data dummy pertanyaan/jawaban penginapan.

**Admin CRUD:**
- `Admin\FaqController` — index, create, store, edit, update, destroy
- Form Request `StoreFaqRequest` dan `UpdateFaqRequest`
- Urutkan berdasarkan `sort_order` — input angka manual di form (opsional: drag-reorder dengan Alpine.js menggunakan `x-sort` jika tersedia, tapi input angka sudah cukup sebagai MVP)
- Halaman admin: tabel dengan kolom pertanyaan (truncate), kategori, urutan, status aktif, tombol edit/hapus

**Halaman publik `/faq`:**
- Route: `GET /faq` → `Public\FaqController@index`
- Tampilan: accordion buka/tutup tiap pertanyaan menggunakan Alpine.js `x-show` + `x-transition` untuk animasi halus (bukan tampil/hilang instan)
- Dikelompokkan per `category` (jika ada) — judul kategori sebagai heading di atas grup FAQ
- Hanya tampilkan `is_active = true`
- Empty state: "Belum ada FAQ. Hubungi kami via WhatsApp jika ada pertanyaan!"

**Section ringkas di homepage:**
Tambahkan section baru setelah section "Informasi Penting" (atau sebelum WhatsApp CTA):
- Tampilkan 4–6 FAQ teratas (aktif, urutan `sort_order` terendah)
- Tombol "Lihat semua FAQ" → link ke `/faq`
- Judul section: "Pertanyaan yang Sering Diajukan"

**Navigasi:**
- Tambahkan link "FAQ" di navigasi publik (header desktop & mobile drawer) dan footer Quick Links

**Seeder (opsional):**
Sediakan seeder `FaqSeeder` dengan 5–8 FAQ contoh bertanda jelas sebagai data contoh. Kategori contoh: "Pemesanan", "Fasilitas", "Pembayaran", "Kebijakan". **Jangan jalankan seeder otomatis di production** — hanya untuk development.

**Test wajib:**
1. Admin CRUD: create, update, delete FAQ berhasil
2. Publik: FAQ aktif tampil, FAQ nonaktif tidak tampil
3. Urutan: FAQ tampil sesuai `sort_order`
4. Kategori: FAQ dikelompokkan benar per kategori

**Acceptance criteria:** admin bisa CRUD, urutan tampil sesuai `sort_order`, FAQ nonaktif tidak muncul di publik, accordion animasi halus.

---

### Fase 4 — Harga Dinamis (Weekend/Hari Libur) 💰

> ⚠️ **Ini fase paling berisiko — perlu kehati-hatian ekstra pada `PricingService` dan model penyimpanan harga.**

**Masalah arsitektur yang harus diselesaikan:**
Saat ini `bookings.price_per_night_snapshot` adalah **satu angka** (asumsi harga seragam per malam). Dengan harga dinamis per malam, satu angka tidak lagi cukup. Perlu penyimpanan breakdown per malam.

**Migration 1 — `rate_overrides`:**
```
id, room_type_id (FK room_types), date (date), price (unsignedBigInteger),
label (string 100, nullable — mis. "Weekend", "Lebaran"),
timestamps
```
Tambahkan **unique constraint** pada `(room_type_id, date)` agar tidak ada dua harga untuk tanggal yang sama.

**Migration 2 — `booking_night_prices` (snapshot breakdown per malam):**
```
id, booking_id (FK bookings), date (date), price (unsignedBigInteger),
label (string 100, nullable — mis. "Weekend" atau null untuk harga normal),
timestamps
```
Tabel ini menyimpan **snapshot harga per malam saat booking dibuat** — mirip konsep `price_per_night_snapshot` tapi per malam. Kolom `bookings.price_per_night_snapshot` tetap diisi (dengan harga malam pertama atau rata-rata) untuk backward compatibility, tapi breakdown detail ada di tabel ini.

**Model:**
- `RateOverride` — `belongsTo(RoomType::class)`, scope `scopeForDate($date)`, `scopeForRange($startDate, $endDate)`
- `BookingNightPrice` — `belongsTo(Booking::class)`
- Tambahkan `hasMany(BookingNightPrice::class)` di model `Booking`

**Logic wajib di `PricingService` — EXTEND, jangan rewrite:**

Ubah method `calculateQuote()` agar mendukung harga per malam:
```php
public function calculateQuote(RoomType $roomType, Carbon $checkIn, Carbon $checkOut): array
{
    $nights = $this->calculateNights($checkIn, $checkOut);
    $basePrice = $roomType->base_price;
    
    // Ambil semua rate_overrides untuk room_type + rentang tanggal
    $overrides = RateOverride::where('room_type_id', $roomType->id)
        ->whereBetween('date', [$checkIn, $checkOut->copy()->subDay()])
        ->pluck('price', 'date')
        ->toArray();
    
    // Hitung harga per malam
    $nightPrices = [];
    $subtotal = 0;
    for ($i = 0; $i < $nights; $i++) {
        $date = $checkIn->copy()->addDays($i);
        $dateKey = $date->toDateString();
        $price = $overrides[$dateKey] ?? $basePrice;
        $nightPrices[] = ['date' => $dateKey, 'price' => $price, 'label' => isset($overrides[$dateKey]) ? 'Override' : null];
        $subtotal += $price;
    }
    
    return [
        'nights' => $nights,
        'price_per_night' => $basePrice, // base price untuk backward compat
        'night_prices' => $nightPrices,  // BARU: breakdown per malam
        'subtotal' => $subtotal,
        'promotion_discount' => 0,
        'points_discount' => 0,
        'points_redeemed' => 0,
        'total_amount' => $subtotal,
        'eligible_loyalty_amount' => $subtotal,
        'promotion' => null,
    ];
}
```

**PENTING:** Method `calculateQuoteWithPromo()` dan `calculateQuoteWithPoints()` memanggil `calculateQuote()` secara internal — pastikan mereka tetap berfungsi karena `$quote['subtotal']` tetap ada. Cek dan test keduanya.

**Update `BookingService.createBooking()`:**
Setelah membuat booking record, simpan breakdown ke `booking_night_prices`:
```php
foreach ($quote['night_prices'] as $np) {
    BookingNightPrice::create([
        'booking_id' => $booking->id,
        'date' => $np['date'],
        'price' => $np['price'],
        'label' => $np['label'],
    ]);
}
```

**Admin UI:**
- Route: `admin/rate-overrides` → `Admin\RateOverrideController`
- Form untuk set harga per tanggal atau per **rentang tanggal** (input start_date, end_date, price, label)
- Bulk-insert: isi semua tanggal dalam rentang sekaligus (mis. "semua Jumat & Sabtu bulan depan") — sediakan opsi checkboxes hari (Senin–Minggu) + rentang bulan
- Tampilkan kalender/tabel bulan berjalan dengan harga per tanggal (highlight yang punya override)

**Checkout UI & Konfirmasi:**
- Breakdown harga per malam WAJIB ditampilkan (bukan hanya total), agar tamu paham kenapa harga berbeda-beda
- Format: tabel tanggal | harga per malam, dengan total di bawah
- Update view: `checkout`, `konfirmasi`, `booking detail` (member & admin), invoice PDF

**Regression test WAJIB:**
1. Booking tanpa override → total IDENTIK dengan sebelum fitur ini ada (base_price × nights)
2. Booking dengan 1 malam override + 2 malam normal → total = override + (2 × base_price)
3. Booking 3 malam semua override → total = sum(3 override prices)
4. `calculateQuoteWithPromo()` tetap berfungsi benar dengan harga dinamis
5. `calculateQuoteWithPoints()` tetap berfungsi benar dengan harga dinamis
6. Tidak ada dua `rate_overrides` untuk kombinasi room_type+date yang sama (unique constraint test)

**Acceptance criteria:**
- Booking tanpa override tetap menghasilkan total identik dengan sebelum fitur ini
- Booking dengan override menghasilkan total yang benar sesuai kombinasi harga per malam
- Breakdown harga per malam tampil di checkout, konfirmasi, dan invoice
- Admin bisa bulk-insert harga weekend/libur

---

### Fase 5 — Layanan Tambahan (Add-ons) 🛎️

**Migration `addons`:**
```
id, name (string, not null), description (text, nullable),
price (unsignedBigInteger, not null),
is_active (boolean, default true), sort_order (unsignedInteger, default 0),
timestamps
```

**Migration `booking_addons`:**
```
id, booking_id (FK bookings), addon_id (FK addons),
quantity (unsignedSmallInteger, default 1),
unit_price (unsignedBigInteger — snapshot harga saat dipesan),
subtotal (unsignedBigInteger — quantity × unit_price),
timestamps
```
**KRITIS:** `unit_price` adalah snapshot harga saat checkout, JANGAN ambil harga live dari tabel `addons` agar histori tidak berubah jika harga addon diubah admin nanti.

**Model:**
- `Addon` — scope `scopeActive()`, `scopeOrdered()`
- `BookingAddon` — `belongsTo(Booking::class)`, `belongsTo(Addon::class)`
- Tambahkan `hasMany(BookingAddon::class)` di model `Booking`

**Logic — Extend `PricingService`:**
Tambahkan method baru (JANGAN ubah `calculateQuote()` untuk ini):
```php
public function calculateQuoteWithAddons(array $baseQuote, array $selectedAddons): array
{
    // $selectedAddons = [['addon_id' => 1, 'quantity' => 2], ...]
    $addonTotal = 0;
    $addonDetails = [];
    foreach ($selectedAddons as $item) {
        $addon = Addon::active()->findOrFail($item['addon_id']);
        $subtotal = $addon->price * $item['quantity'];
        $addonTotal += $subtotal;
        $addonDetails[] = [
            'addon_id' => $addon->id,
            'name' => $addon->name,
            'quantity' => $item['quantity'],
            'unit_price' => $addon->price,
            'subtotal' => $subtotal,
        ];
    }
    
    $baseQuote['addon_total'] = $addonTotal;
    $baseQuote['addon_details'] = $addonDetails;
    $baseQuote['total_amount'] += $addonTotal;
    return $baseQuote;
}
```

**Update `BookingService`:** Setelah booking dibuat, simpan `booking_addons` dengan snapshot harga.

**Admin CRUD** untuk `addons` — `Admin\AddonController` standar (index/create/edit/update/delete).

**Checkout UI:**
- Tampilkan daftar add-on aktif sebagai checkbox + quantity selector
- Update total harga secara real-time via Alpine.js saat user toggle/ubah quantity
- **Validasi ulang total di server** saat submit — jangan percaya angka dari client

**View update:**
- Checkout, konfirmasi, booking detail: tampilkan daftar add-on yang dipilih + harga masing-masing
- Invoice PDF: tambahkan section add-on di invoice

**Test wajib:**
1. Booking dengan add-on: total = harga kamar (dari Fase 4) + total add-on
2. Snapshot: ubah harga addon di admin → booking yang sudah ada tidak terpengaruh
3. Validasi: addon_id yang tidak aktif/tidak ada → validation error
4. Quantity: quantity 0 atau negatif → validation error

**Acceptance criteria:**
- Total booking dengan add-on = harga kamar (Fase 4) + total add-on, dihitung ulang & divalidasi di server
- Perubahan harga addon oleh admin tidak mengubah harga booking yang sudah ada (snapshot bekerja)
- Add-on tampil di invoice PDF

---

### Fase 6 — Kalender Admin Visual 📅

**Route:**
- `GET admin/calendar` → `Admin\CalendarController@index` (tampilkan view)
- `GET admin/calendar/data` → `Admin\CalendarController@data` (JSON API, terima `start_date` & `end_date` parameter)

**JSON Response format:**
```json
{
  "rooms": [
    {"id": 1, "name": "Kamar 101", "room_type": "Deluxe"},
    ...
  ],
  "bookings": [
    {
      "id": 1, "booking_code": "BKG-202608-0001",
      "room_id": 1, "guest_name": "John",
      "check_in": "2026-08-18", "check_out": "2026-08-20",
      "status": "confirmed", "status_label": "Dikonfirmasi"
    },
    ...
  ],
  "room_blocks": [
    {"id": 1, "room_id": 1, "start_date": "2026-08-25", "end_date": "2026-08-26", "reason": "Maintenance"}
  ]
}
```

**View:**
- Grid timeline — kolom = tanggal, baris = kamar individual (bukan room type)
- Cell = status booking (warna berbeda per status: hijau = confirmed, biru = checked_in, abu = checkout, merah muda = pending_payment)
- Cell room_block = warna abu-abu dengan pattern (maintenance)
- Dibangun dengan Alpine.js + `fetch()` ke endpoint data
- Lazy load per rentang tanggal: default tampil bulan berjalan, navigasi prev/next bulan
- Klik pada booking → link ke halaman detail booking admin
- Hover tooltip: nama tamu, tanggal, status

**Performa:**
- Endpoint data hanya query booking yang overlap dengan rentang tanggal diminta (bukan semua booking)
- Gunakan eager loading (`with('room')`) untuk hindari N+1

**Drag-and-drop pindah kamar: JANGAN dikerjakan di fase ini.** Tandai sebagai stretch goal untuk masa depan. Terlalu berisiko untuk kalender pertama.

**Test wajib:**
1. Endpoint mengembalikan booking yang benar sesuai rentang tanggal
2. Booking di luar rentang tidak masuk response
3. Room blocks tampil di response
4. Endpoint hanya bisa diakses admin (middleware `auth:admin`)

**Acceptance criteria:** kalender menampilkan data akurat sesuai booking real, performa tetap baik untuk rentang 1 bulan (response < 1 detik).

---

### Fase 7 — Filter & Sortir Kamar 🔍

Client-side filtering dengan Alpine.js di `resources/views/public/rooms/index.blade.php`:
- Filter: rentang harga (slider/input min-max), kapasitas minimum, fasilitas (checkboxes)
- Sortir: harga terendah, harga tertinggi, kapasitas terbesar

**TIDAK perlu migration baru** — pakai data yang sudah ada di `room_types` dan relasi `facilities`.

**Implementasi:**
- Pass data room types + facilities sebagai JSON ke Alpine.js component via `x-data`
- Filter/sort di client side (JavaScript array filter/sort) — reload halaman tidak diperlukan
- Tampilkan jumlah hasil ("Menampilkan X dari Y tipe kamar")
- Tombol "Reset Filter" untuk kembali ke tampilan awal
- Transition halus saat kartu muncul/hilang: `x-transition`

**Mobile:** Filter panel collapse di mobile (toggle button "Filter" yang membuka/menutup panel filter) agar tidak memakan ruang layar.

**Test wajib:** (Manual/E2E)
1. Filter harga: hanya kamar dalam rentang yang tampil
2. Filter kapasitas: hanya kamar yang memenuhi minimum yang tampil
3. Sort: urutan berubah sesuai pilihan
4. Reset: semua kamar tampil kembali

**Acceptance criteria:** filter/sortir bekerja tanpa reload halaman, tidak merusak listing yang sudah ada, responsif di mobile.

---

### Fase 8 — Formulir Kontak 📞

**Migration `contact_messages`:**
```
id, name (string, not null), email (string, not null),
phone (string 32, nullable), subject (string, not null),
message (text, not null),
is_read (boolean, default false),
admin_notes (text, nullable),
replied_at (timestamp, nullable),
timestamps
```

**Halaman publik:**
- Route: `GET /hubungi` → `Public\ContactController@create`
- Route: `POST /hubungi` → `Public\ContactController@store`
- Form Request `StoreContactMessageRequest`: name (required), email (required, email), phone (nullable), subject (required, max:150), message (required, min:10, max:3000)
- Rate-limit: pakai middleware `throttle:3,10` (max 3 submit per 10 menit per IP) — cegah spam
- Setelah submit: redirect dengan flash message "Pesan Anda telah terkirim!"

**Auto-reply email:**
`ContactAutoReplyMail` (implements `ShouldQueue`) — kirim ke email pengirim menggunakan infrastruktur Mail dari Fase 1. Isi: "Terima kasih, pesan Anda telah kami terima. Kami akan merespons dalam 1×24 jam."

**Admin panel:**
- `Admin\ContactMessageController` — index (daftar pesan, filter: semua/belum dibaca/sudah dibaca), show (detail pesan), markRead, destroy
- Badge jumlah pesan belum dibaca di navigasi admin (opsional)

**Navigasi:**
- Tambahkan link "Hubungi" di navigasi publik dan footer

**Test wajib:**
1. Submit form valid → pesan tersimpan + auto-reply email masuk queue
2. Validasi: field kosong/email invalid → validation error
3. Rate limit: submit > 3x dalam 10 menit → 429 Too Many Requests
4. Admin: halaman index menampilkan pesan, markRead mengubah status

**Acceptance criteria:** submit form tervalidasi, auto-reply terkirim, admin bisa melihat & menandai pesan.

---

### Fase 9 — Informasi Sekitar/Atraksi 🗺️

**Migration `nearby_places`:**
```
id, name (string, not null),
category (string 100 — mis. "Wisata", "Kuliner", "Transportasi", "Kesehatan"),
distance (string 50, nullable — mis. "2.5 km", "15 menit"),
description (text, nullable),
image (string 255, nullable — path relatif, pakai mekanisme upload ImageUploadService yang sudah ada),
map_link (string 500, nullable — URL Google Maps),
sort_order (unsignedInteger, default 0),
is_active (boolean, default true),
timestamps
```

**Model `NearbyPlace`:** Scope `scopeActive()`, `scopeOrdered()`.

**Admin CRUD:**
- `Admin\NearbyPlaceController` — index/create/store/edit/update/destroy
- Upload gambar: **pakai `ImageUploadService` yang sudah ada** (method `upload()`) — JANGAN buat mekanisme upload baru
- Form Request `StoreNearbyPlaceRequest`, `UpdateNearbyPlaceRequest`

**Halaman publik:**
- Route: `GET /sekitar` → `Public\NearbyPlaceController@index`
- Tampilkan dikelompokkan per kategori (heading per kategori)
- Kartu: gambar (atau placeholder jika tidak ada), nama, jarak, deskripsi singkat, tombol "Lihat di Maps" jika `map_link` ada
- Hanya tampilkan `is_active = true`, urut `sort_order`

**Section di homepage (opsional):**
Tambahkan section "Tempat Menarik di Sekitar" setelah section Lokasi — tampilkan 4–6 tempat teratas + link "Lihat semua".

**Navigasi:**
- Tambahkan link "Sekitar" di navigasi publik dan footer

**Seeder:** `NearbyPlaceSeeder` dengan 4–6 contoh tempat di sekitar Kota Bangun II (tandai sebagai data contoh).

**Test wajib:**
1. Admin CRUD: create, update, delete berhasil
2. Upload gambar: file tersimpan via `ImageUploadService`
3. Publik: hanya `is_active = true` yang tampil
4. Urutan: tampil sesuai `sort_order`

**Acceptance criteria:** admin bisa kelola data, publik menampilkan sesuai `sort_order`, gambar ter-upload dengan benar via `ImageUploadService`.

---

### Fase 10 — Modifikasi Booking oleh Tamu ✏️ (Risiko Tinggi)

> ⚠️ **Fase ini memiliki risiko tertinggi karena bersinggungan langsung dengan booking engine dan payment.** Kerjakan dengan sangat hati-hati.

**Migration `booking_change_requests`:**
```
id, booking_id (FK bookings),
user_id (FK users — yang mengajukan),
type (string — 'reschedule', 'room_change', 'guest_update'),
original_data (json — snapshot data booking sebelum perubahan),
requested_data (json — data perubahan yang diajukan),
price_difference (bigInteger, signed — positif = tamu perlu bayar tambahan, negatif = tamu dapat refund),
status (string, default 'pending' — 'pending', 'approved', 'rejected', 'cancelled'),
admin_notes (text, nullable),
processed_by_admin_id (FK admins, nullable),
processed_at (timestamp, nullable),
timestamps
```

**Alur wajib (JANGAN disingkat):**
1. **Tamu mengajukan perubahan** via form di dashboard member — status booking asli **TIDAK langsung berubah**. Buat `BookingChangeRequest` dengan `status = pending`.
2. **Validasi ketersediaan** (untuk reschedule/room_change): gunakan `AvailabilityService::isRoomAvailable()` atau `searchAvailableRoomTypes()` — **service yang sama** dengan booking engine, BUKAN logic baru.
3. **Hitung selisih harga:** pakai `PricingService::calculateQuote()` untuk tanggal/tipe baru, bandingkan dengan `booking.total_amount`. Simpan `price_difference` di change request.
4. **Tampilkan ke tamu** preview perubahan + selisih harga sebelum submit final.
5. **Notifikasi admin:** kirim email (Mailable baru `BookingChangeRequestMail`, pakai infrastruktur Fase 1) agar admin tahu ada request masuk.
6. **Admin approve/reject** via panel admin:
   - Approve: update booking asli sesuai `requested_data`, gunakan `DB::transaction()` + cek ulang ketersediaan (mungkin sudah berubah sejak request dibuat). Jika ada `price_difference > 0`, buat payment baru via Midtrans. Jika `price_difference < 0`, tandai untuk refund sesuai kebijakan existing.
   - Reject: set status = `rejected`, kirim email notifikasi ke tamu.
7. **Guard:** Hanya booking dengan status `Confirmed` yang boleh diajukan perubahan (bukan `PendingPayment`, `CheckedIn`, atau terminal states). Validasi di Form Request.

**Acceptance criteria:**
- Tidak ada race condition yang menciptakan double booking (cek ulang ketersediaan saat admin approve)
- Riwayat perubahan tercatat di tabel `booking_change_requests`
- Selisih harga dihitung benar menggunakan `PricingService` (termasuk harga dinamis Fase 4 + add-on Fase 5)
- Alur pembayaran tambahan terintegrasi dengan Midtrans yang sudah ada
- Booking yang sudah checked-in/terminal tidak bisa diajukan perubahan

---

### Fase 11 — Export Data Admin 📊

**Dependency check:** `maatwebsite/excel` TIDAK ada di `composer.json` saat ini.

**Pendekatan:**
- Gunakan **CSV manual** dengan PHP native (`fputcsv()`) atau `League\Csv` (lightweight) — hindari dependency berat `maatwebsite/excel` kecuali user meminta fitur Excel spesifik (formatting, multiple sheets, dll)
- Jika user meminta Excel: `composer require maatwebsite/excel` dulu

**Implementasi:**
Tambahkan method `export()` di controller admin yang sudah ada (JANGAN buat controller baru terpisah):
- `Admin\ReportController@exportRevenue` — export laporan pendapatan
- `Admin\ReportController@exportOccupancy` — export laporan okupansi
- `Admin\BookingController@export` — export daftar booking

**Tombol UI:** Tambahkan tombol "Download CSV" di setiap halaman laporan, di samping filter yang sudah ada.

**Penting:** File yang diunduh harus berisi data **sesuai filter aktif** di halaman (tanggal, status, dll) — bukan seluruh data tanpa filter. Terima parameter filter yang sama dengan halaman web.

**Format:**
- Tanggal: `d/m/Y` (konsisten dengan tampilan web)
- Angka rupiah: angka polos tanpa "Rp" atau titik (agar bisa diolah di spreadsheet)
- Header kolom: Bahasa Indonesia
- Encoding: UTF-8 dengan BOM (`\xEF\xBB\xBF`) agar Excel tidak rusak karakter Indonesia

**Test wajib:**
1. Export menghasilkan file CSV valid dengan header yang benar
2. Data sesuai filter: export dengan filter tanggal → hanya data dalam rentang tersebut
3. Endpoint hanya bisa diakses admin

**Acceptance criteria:** file terunduh berisi data yang sesuai filter aktif, format angka & tanggal konsisten, karakter Indonesia tidak rusak.

---

## 4. Panduan Tampilan (Agar Website "Tambah Bagus")

### Design System yang Sudah Ada (WAJIB Diikuti)

Project menggunakan **Tailwind CSS v4** (bukan v3). Konfigurasi ada di `resources/css/app.css` menggunakan `@theme` directive. **JANGAN buat `tailwind.config.js`** — tidak dipakai di v4.

| Token | Nilai |
|---|---|
| Font | `Inter` (via `--font-sans`) |
| Warna utama | Green/Emerald: `primary-50` (#f0fdf4) sampai `primary-950` (#052e16) |
| Plugin | `@tailwindcss/forms` |
| Alpine.js | `[x-cloak] { display: none !important; }` sudah ada |

### Aturan Visual untuk Semua Fitur Baru

1. **Gunakan ulang design token yang sudah ada** — semua fitur baru (FAQ accordion, kartu review, badge harga weekend, kartu nearby place) harus memakai palet `primary-*` dan utility Tailwind yang sama dengan halaman existing. **Jangan** tambah warna baru ke `@theme` kecuali benar-benar perlu.

2. **Konsistensi komponen kartu:** Kartu review, kartu FAQ, kartu nearby-place sebaiknya memakai struktur visual yang mirip dengan kartu tipe kamar di `rooms/index.blade.php` (padding, shadow, rounded corner, hover effect) agar terasa satu sistem. Referensi: kartu di homepage section "Tipe Kamar" menggunakan `rounded-xl`, `shadow`, `overflow-hidden`, `hover:shadow-lg transition`.

3. **Micro-interaction ringan:** Gunakan Alpine.js `x-show` + `x-transition` untuk transisi buka/tutup (accordion FAQ, filter kamar) dengan animasi halus, bukan tampil/hilang instan. Contoh:
   ```html
   <div x-show="open" x-transition:enter="transition ease-out duration-200" ...>
   ```

4. **Rating bintang:** Gunakan ikon SVG konsisten — buat Blade component `<x-star-rating :rating="4.5" />` dengan state "terisi" (kuning), "kosong" (abu-abu), dan "setengah" (half-fill) untuk rating desimal. **JANGAN pakai emoji ⭐ di production.**

5. **Empty state yang informatif:** Halaman ulasan tanpa review, FAQ kosong, daftar pesan kontak kosong — jangan biarkan kosong polos. Tampilkan pesan ramah + ikon ilustratif sederhana (SVG). Contoh: "Belum ada ulasan, jadilah yang pertama!" dengan ikon bintang outline.

6. **Mobile-first check:** Setiap fitur baru wajib dicek tampilannya di lebar layar mobile (≤ 390px) sebelum dianggap selesai — terutama:
   - Kalender admin (Fase 6): horizontal scroll jika tanggal > lebar layar
   - Filter kamar (Fase 7): panel filter collapse di mobile
   - Tabel harga per malam (Fase 4): stack vertical di mobile, bukan tabel horizontal

7. **Loading & error state:** Untuk data yang di-fetch via Alpine/JS (kalender admin, filter), sediakan:
   - Loading: skeleton/spinner saat fetch
   - Error: pesan jelas "Gagal memuat data, coba lagi" + tombol retry
   - Jangan biarkan layar kosong/putih saat gagal fetch

8. **Navbar & footer update:** Setiap halaman publik baru (/faq, /hubungi, /sekitar) harus ditambahkan link-nya di:
   - Desktop navigation (header `layouts/public.blade.php`)
   - Mobile drawer menu
   - Footer Quick Links
   Pastikan link aktif ter-highlight (cek dengan `request()->routeIs()`)

---

## 5. Checklist Anti-Bug (Wajib Dicentang Sebelum Fitur Dianggap Selesai)

Untuk **setiap fase**, semua item berikut harus tercentang:

- [ ] `php artisan test` — semua test baseline tetap PASS, test baru PASS, jumlah test/assertions tidak berkurang
- [ ] Migration sudah diuji: `migrate:rollback` lalu `migrate` ulang tanpa error
- [ ] Tidak ada N+1 query baru (cek dengan `DB::listen` atau debugbar pada halaman yang menampilkan list — review, FAQ, nearby places)
- [ ] Semua form baru punya Form Request dengan validasi server-side lengkap
- [ ] Otorisasi diuji: user A tidak bisa mengakses/mengubah data milik user B (review, booking change request)
- [ ] Tidak ada harga/perhitungan yang dipercaya dari input client tanpa dihitung ulang di server
- [ ] Fitur baru dicek di mobile (≤ 390px) & desktop (≥ 1280px)
- [ ] Tidak ada kredensial (SMTP, API key) yang ter-commit ke repository
- [ ] Email aman dijalankan berulang (idempoten) — scheduled command tidak kirim dobel
- [ ] Perubahan pada `PricingService`/`BookingService` tidak mengubah hasil perhitungan untuk skenario lama (regression test eksplisit)
- [ ] Halaman baru punya `@section('title')` yang deskriptif untuk SEO
- [ ] Empty state informatif untuk daftar/halaman yang bisa kosong
- [ ] Link navigasi (header, mobile, footer) sudah ditambahkan untuk halaman publik baru

---

## 6. Rencana Verifikasi

### Automated
- `php artisan test` dijalankan sebelum dan sesudah setiap fase — hasil dibandingkan dengan baseline
- Test baru minimal mencakup: happy path, validasi gagal, otorisasi gagal, untuk setiap fitur
- Regression test eksplisit untuk setiap perubahan di `PricingService`

### Manual
- **Setelah Fase 0–3:** Uji alur member: login → dashboard → lihat booking checkout → tulis review → cek review masuk moderasi admin → admin publish → review tampil publik
- **Setelah Fase 4–5:** Uji alur booking penuh: cari kamar → lihat harga dinamis per malam → pilih add-on → checkout dengan breakdown harga → bayar via Midtrans → terima email konfirmasi → cek invoice PDF menampilkan breakdown + add-on
- **Setelah Fase 1:** Uji email: pastikan queue worker aktif (`php artisan queue:work` atau cek `composer dev`), buat booking → cek email di log/Mailtrap, jalankan command reminder → cek log
- **Setelah Fase 6:** Uji kalender admin dengan data booking padat (banyak kamar, rentang tanggal panjang) untuk performa — response < 1 detik untuk 1 bulan
- **Responsif:** Setiap fase, cek di Chrome DevTools: 390px (mobile), 768px (tablet), 1280px (desktop)

---

## 7. Pertanyaan yang Perlu Dijawab User Sebelum Mulai

1. **Prioritas:** Setuju mulai dari **Fase 0–3** (Prerequisites → Email → Review → FAQ) sebagai paket pertama?
2. **Email provider:** Sudah punya akun SMTP (Gmail App Password, Mailtrap, Mailgun, dll)? Jika belum, mau pakai apa untuk testing? (Rekomendasi: `MAIL_MAILER=log` dulu untuk development, Mailtrap gratis untuk staging)
3. **Harga weekend/libur (Fase 4):** Apakah penginapan memang menerapkan harga berbeda? Jika ya, berapa persen kira-kira kenaikannya, dan hari libur mana saja yang biasanya lebih mahal?
4. **Add-on (Fase 5):** Layanan tambahan apa saja yang tersedia (sarapan, extra bed, antar-jemput, laundry, dll) beserta perkiraan harganya?
5. **Bahasa:** Perlu versi Bahasa Inggris untuk tamu asing, atau cukup Bahasa Indonesia saja?
6. **Moderasi review:** Setuju review masuk moderasi dulu (tidak langsung tampil) sebelum publik bisa lihat?
7. **Harga breakdown (Fase 4):** Untuk menyimpan harga per malam di booking, setuju pakai tabel terpisah `booking_night_prices`? (Opsi lain: kolom JSON di tabel bookings — tapi tabel terpisah lebih fleksibel untuk query/report)

---

## 8. Catatan Penutup untuk AI Agent

1. Jangan mengerjakan lebih dari **satu fase** dalam satu sesi kerja besar.

2. Setelah tiap fase selesai dan checklist Bagian 5 tercentang semua, **berhenti dan laporkan ringkasan ke user:**
   - Apa yang dibuat (file baru, migration, test)
   - File apa saja yang diubah (dari existing)
   - Hasil `php artisan test` (jumlah tests & assertions, semua PASS?)
   - Screenshot/preview jika ada perubahan UI

3. **Jangan lanjut ke fase berikutnya** tanpa persetujuan user.

4. Jika menemukan bug atau inkonsistensi di kode existing saat mengerjakan fitur baru, **laporkan terpisah** — jangan perbaiki bersamaan agar mudah dilacak.

5. **Referensi cepat file kunci:**
   - Pricing: `app/Services/PricingService.php`
   - Booking: `app/Services/BookingService.php`
   - Payment: `app/Services/MidtransPaymentService.php`
   - Availability: `app/Services/AvailabilityService.php`
   - Booking model: `app/Models/Booking.php`
   - Enums: `app/Enums/BookingStatus.php`
   - CSS/Design: `resources/css/app.css` (@theme)
   - Layout publik: `resources/views/layouts/public.blade.php`
   - Layout admin: `resources/views/layouts/admin.blade.php`
   - Layout member: `resources/views/layouts/member.blade.php`
   - Homepage: `resources/views/public/home.blade.php`
   - Room listing: `resources/views/public/rooms/index.blade.php`
   - Scheduler: `routes/console.php`
