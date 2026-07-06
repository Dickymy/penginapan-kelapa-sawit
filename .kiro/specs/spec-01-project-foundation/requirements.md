# SPEC 01 — Project Foundation: Requirements

> **Referensi master:** Fase 0 (Task 0.1–0.5) + Fase 1 (Task 1.1–1.4)  
> **Scope:** Bootstrap project, konfigurasi, enum, auth member+admin, layout foundation

---

## REQ-1: Project Bootstrap & Environment

### REQ-1.1
**Sebagai** developer,  
**saya ingin** project Laravel 12 ter-bootstrap dengan benar,  
**sehingga** aplikasi dapat menerima HTTP request dan menjalankan Artisan command.

**Acceptance Criteria:**
- Laravel 12.x terinstal melalui Composer pada workspace project.
- `php artisan serve` merespons HTTP 200 pada route default.
- PHP 8.2+ terkonfirmasi sebagai runtime.
- MySQL 8.0 terkonfigurasi sebagai database connection.
- Git repository telah diinisialisasi dengan `.gitignore` yang mencakup `.env`, `vendor/`, `node_modules/`, dan `storage/*.key`.

### REQ-1.2
**Sebagai** developer,  
**saya ingin** `.env.example` berisi seluruh variable yang dibutuhkan project tanpa secret,  
**sehingga** developer baru dapat menyalin dan mengisi credential sendiri.

**Acceptance Criteria:**
- `.env.example` berisi: APP_NAME, APP_TIMEZONE=Asia/Makassar, DB config, MIDTRANS keys placeholder, GOOGLE OAuth placeholder, MAIL config, DEV_ADMIN credentials placeholder.
- Tidak ada secret/key nyata dalam file yang di-commit.

### REQ-1.3
**Sebagai** sistem,  
**saya ingin** timezone bisnis `Asia/Makassar` terkonfigurasi secara konsisten,  
**sehingga** seluruh tampilan waktu dan penjadwalan menggunakan zona yang benar.

**Acceptance Criteria:**
- `config('app.timezone')` mengembalikan `Asia/Makassar`.
- Scheduler dan timestamp menggunakan timezone tersebut.

---

## REQ-2: Domain Configuration

### REQ-2.1
**Sebagai** developer,  
**saya ingin** business constant terpusat di config files,  
**sehingga** perubahan parameter bisnis tidak memerlukan pencarian di banyak file.

**Acceptance Criteria:**
- `config/booking.php` berisi: `hold_minutes` (default 30), `currency` (IDR), `eligible_sources` (array).
- `config/loyalty.php` berisi: `earn_divisor` (1000), `point_value` (50), `min_redeem` (100), `max_redemption_percent` (20), `expiry_months` (18).
- `config/midtrans.php` berisi: `server_key`, `client_key`, `is_production` (default false), `is_sanitized` (true), `is_3ds` (true) — semua key dari `env()`.
- Tidak ada secret yang hardcoded di config file.

---

## REQ-3: Enum & State Transition Foundation

### REQ-3.1
**Sebagai** developer,  
**saya ingin** seluruh status domain tersedia sebagai PHP backed enum,  
**sehingga** tidak ada magic string tersebar dan IDE dapat memberikan type-safety.

**Acceptance Criteria:**
- 8 enum file tersedia: `BookingStatus`, `PaymentStatus`, `BookingSource`, `LoyaltyTransactionType`, `PromotionType`, `PromotionUsageStatus`, `RefundStatus`, `RoomStatus`.
- Setiap enum menggunakan `string` backed values.
- Setiap enum memiliki method `label(): string` yang mengembalikan label Bahasa Indonesia.

### REQ-3.2
**Sebagai** sistem,  
**saya ingin** transisi BookingStatus divalidasi secara terpusat,  
**sehingga** status tidak dapat berpindah ke state yang tidak diizinkan.

**Acceptance Criteria:**
- `BookingStatus` memiliki method `allowedTransitions(): array` yang mengembalikan daftar status tujuan valid.
- Method `canTransitionTo(BookingStatus $target): bool` tersedia.
- Transisi invalid melempar `InvalidStatusTransitionException` atau domain exception sejenis.
- Terminal states (`completed`, `cancelled`, `expired`, `no_show`) mengembalikan array kosong untuk allowed transitions.
- Unit test membuktikan transisi valid diterima dan invalid ditolak.

---

## REQ-4: Member Authentication

### REQ-4.1
**Sebagai** pengunjung,  
**saya ingin** mendaftar sebagai member dengan nama, email, WhatsApp, dan password,  
**sehingga** saya memiliki akun untuk menikmati fitur member.

**Acceptance Criteria:**
- Form register meminta: nama (required), email (required, unique, normalized lowercase), WhatsApp (required, normalized), password (required, confirmed, min 8 karakter).
- Setelah register, email verification dikirim.
- User tersimpan dengan password ter-hash (bcrypt/argon2 Laravel default).
- WhatsApp disimpan dalam format normalized (angka saja, awalan 62).

### REQ-4.2
**Sebagai** member,  
**saya ingin** login dengan email dan password,  
**sehingga** saya dapat mengakses dashboard dan fitur member.

**Acceptance Criteria:**
- Login menggunakan email (case-insensitive) dan password.
- Login gagal menampilkan pesan generik (tidak membocorkan apakah email terdaftar).
- Akun inactive (`is_active = false`) ditolak login dengan pesan yang sesuai.
- Session diregenerasi setelah login berhasil.
- Rate limiting aktif pada endpoint login.

### REQ-4.3
**Sebagai** member,  
**saya ingin** dapat mereset password melalui email,  
**sehingga** saya tidak kehilangan akses jika lupa password.

**Acceptance Criteria:**
- Forgot password mengirim link reset ke email terdaftar.
- Link reset memiliki expiry.
- Reset berhasil mengubah password dan menginvalidasi token.
- Flow menggunakan mekanisme resmi Laravel.

### REQ-4.4
**Sebagai** member,  
**saya ingin** email saya terverifikasi sebelum fitur tertentu aktif,  
**sehingga** sistem memiliki kepastian identitas untuk operasi sensitif.

**Acceptance Criteria:**
- Email verification link dikirim saat register.
- Resend verification tersedia.
- `email_verified_at` terisi setelah verifikasi berhasil.
- Route yang memerlukan verified email dilindungi middleware.

---

## REQ-5: Admin Authentication

### REQ-5.1
**Sebagai** admin,  
**saya ingin** login di area terpisah dari member,  
**sehingga** area admin terisolasi dan lebih aman.

**Acceptance Criteria:**
- Admin login tersedia di route `/admin/login`.
- Guard `admin` menggunakan tabel `admins` dan model `Admin`.
- Admin inactive ditolak login.
- Session admin terpisah dari session member (guard berbeda).

### REQ-5.2
**Sebagai** sistem,  
**saya ingin** tidak ada registrasi admin publik,  
**sehingga** akun admin hanya dapat dibuat melalui seeder atau admin existing.

**Acceptance Criteria:**
- Tidak ada route `/admin/register` yang dapat diakses publik.
- Admin development dibuat melalui seeder dengan credential dari environment variable.
- Seeder gagal aman jika env variable kosong (tidak membuat admin dengan password default hardcoded).

### REQ-5.3
**Sebagai** sistem,  
**saya ingin** member tidak dapat mengakses route admin,  
**sehingga** boundary otorisasi terjaga.

**Acceptance Criteria:**
- Route admin dilindungi middleware `auth:admin`.
- Member terautentikasi yang mencoba akses admin route mendapat 403 atau redirect ke halaman yang sesuai.
- Guest yang mengakses admin route di-redirect ke `/admin/login`.

---

## REQ-6: Layout Foundation

### REQ-6.1
**Sebagai** pengunjung,  
**saya ingin** website memiliki tampilan publik yang profesional dan mobile-friendly,  
**sehingga** saya dapat menjelajahi informasi penginapan dengan nyaman.

**Acceptance Criteria:**
- Layout publik memiliki: header dengan logo/nama + navigasi, content area, footer.
- Navigasi publik: Beranda, Kamar, Tentang, Lokasi, Kebijakan, Cek Booking, Login/Daftar.
- Responsive: mobile-first, navigasi collapse pada layar kecil.
- Tema warna: hijau alami, putih, netral hangat.
- Bahasa Indonesia.

### REQ-6.2
**Sebagai** member,  
**saya ingin** area member memiliki navigasi yang jelas,  
**sehingga** saya dapat mengakses fitur-fitur member dengan mudah.

**Acceptance Criteria:**
- Layout member memiliki navigasi: Dashboard, Booking Saya, Poin Saya, Profil, Logout.
- Menampilkan nama member yang sedang login.
- Responsive dan konsisten dengan tema publik.

### REQ-6.3
**Sebagai** admin,  
**saya ingin** area admin memiliki sidebar navigasi,  
**sehingga** saya dapat mengakses seluruh fitur manajemen.

**Acceptance Criteria:**
- Layout admin memiliki sidebar dengan menu: Dashboard, Reservasi, Kalender, Kamar, Tamu, Pembayaran, Promo, Loyalty, Room Block, Galeri, Kebijakan, Pengeluaran, Laporan, Pengaturan.
- Item menu yang fiturnya belum diimplementasikan ditampilkan sebagai disabled/placeholder.
- Topbar menampilkan nama admin dan logout.
- Sidebar collapsible pada layar kecil.

### REQ-6.4
**Sebagai** developer,  
**saya ingin** komponen UI reusable tersedia,  
**sehingga** konsistensi visual terjaga dan development lebih cepat.

**Acceptance Criteria:**
- Blade component tersedia untuk: alert (success/error/warning/info), button (primary/secondary/danger), badge (status), form-error, empty-state.
- Komponen menggunakan Tailwind CSS classes.
- Frontend build (Vite) berhasil tanpa error.
- Alpine.js tersedia untuk interaksi (toggle menu, dropdown).

---

## REQ-7: Test Foundation

### REQ-7.1
**Sebagai** developer,  
**saya ingin** test infrastructure siap digunakan,  
**sehingga** setiap fitur dapat diverifikasi secara otomatis.

**Acceptance Criteria:**
- PHPUnit terkonfigurasi dengan database testing (SQLite in-memory atau MySQL test DB).
- `php artisan test` dapat dijalankan tanpa error.
- Minimal test tersedia: app boots, enum transition valid/invalid, register member, login member, login admin, member cannot access admin route.
- `npm run build` berhasil tanpa error.

---

## Constraints & Non-Functional Requirements

- **C-1:** Tidak ada fitur domain (room, booking, payment, loyalty) yang diimplementasikan pada Spec ini.
- **C-2:** Secret tidak boleh ada di source code atau repository.
- **C-3:** Seluruh password menggunakan hashing bawaan Laravel.
- **C-4:** CSRF protection aktif untuk seluruh form.
- **C-5:** Output Blade menggunakan escaped syntax default (`{{ }}`).
- **C-6:** Bahasa interface: Bahasa Indonesia.
- **C-7:** Tidak ada data bisnis yang dikarang (harga, fasilitas, alamat).
