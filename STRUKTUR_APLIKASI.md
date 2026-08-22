# Dokumentasi & Struktur Aplikasi: Penginapan Kelapa Sawit

## 1. Nama Aplikasi dan Tujuan
**Nama Aplikasi:** Penginapan Kelapa Sawit
**Tujuan:** Sistem reservasi (booking) kamar penginapan secara online untuk "Penginapan Kelapa Sawit" yang berlokasi di Kota Bangun II, Kutai Kartanegara. Aplikasi ini memungkinkan tamu untuk melihat kamar, mengecek ketersediaan, melakukan pemesanan, dan melakukan pembayaran secara online terintegrasi (Midtrans). Sistem ini juga mencakup portal manajemen (Admin) untuk mengelola reservasi, kamar, dan laporan, serta portal Member untuk tamu yang terdaftar (loyalitas/poin).

## 2. Teknologi yang Digunakan
- **Backend:** Laravel 12.0 (PHP ^8.2)
- **Frontend / Templating:** Laravel Blade
- **CSS Framework:** Tailwind CSS v4
- **Interactivity / JavaScript:** Alpine.js (digunakan untuk modal, dropdown, tabs, drawer, dll)
- **Build Tool:** Vite
- **Autentikasi:** Laravel Fortify & Laravel Socialite (Google OAuth)
- **Payment Gateway:** Midtrans (midtrans-php)
- **PDF Generator:** barryvdh/laravel-dompdf (untuk Invoice/Laporan)
- **Image Processing:** intervention/image v3
- **File Storage:** Local & AWS S3 (dikonfigurasi via Flysystem)

## 3. Daftar Semua Halaman & Fungsi Utamanya

### Halaman Publik (Frontend)
| Route / URL | Fungsi Utama |
|---|---|
| `/` (Home) | Halaman beranda yang menampilkan pencarian ketersediaan, daftar tipe kamar unggulan, dan informasi singkat penginapan. |
| `/kamar` | Menampilkan daftar seluruh tipe kamar yang tersedia beserta filter pencarian. |
| `/kamar/{slug}` | Menampilkan detail spesifik dari suatu tipe kamar, termasuk galeri gambar, fasilitas, dan form pemesanan langsung. |
| `/tentang` | Menampilkan informasi profil dan sejarah singkat penginapan. |
| `/lokasi` | Menampilkan informasi peta dan detail lokasi penginapan. |
| `/kebijakan` | Menampilkan syarat & ketentuan serta kebijakan privasi penginapan. |
| `/hubungi` | Form kontak bagi tamu yang ingin mengirim pesan langsung ke pengelola. |
| `/galeri` | Menampilkan seluruh foto galeri penginapan. |
| `/faq` | Menampilkan pertanyaan yang sering diajukan (Frequently Asked Questions). |
| `/sekitar` | Menampilkan tempat-tempat menarik atau fasilitas umum di sekitar lokasi penginapan. |
| `/ketersediaan` | Halaman hasil pencarian kamar berdasarkan tanggal *check-in* dan *check-out*. |
| `/checkout` | Halaman form pengisian data tamu untuk menyelesaikan proses booking kamar. |
| `/booking/{code}/konfirmasi` | Menampilkan ringkasan pesanan setelah checkout berhasil dilakukan, sebelum pembayaran. |
| `/booking/{code}/bayar` | Halaman proses pembayaran yang terintegrasi dengan Midtrans (Payment Gateway). |
| `/booking/{code}/selesai` | Halaman status akhir setelah pembayaran berhasil atau gagal (Callback dari Midtrans). |
| `/cek-booking` | Halaman bagi *guest* (tamu tanpa akun) untuk melacak status pesanan mereka menggunakan kode booking dan email. |
| `/booking-saya` | Menampilkan daftar pesanan yang pernah dibuat (menyimpan session atau untuk tamu login). |
| `/booking/{code}/invoice` | Mengunduh (download) tagihan/invoice dalam format PDF. |

### Halaman Member (Tamu Terdaftar)
| Route / URL | Fungsi Utama |
|---|---|
| `/member/dashboard` | Dasbor utama member, melihat ringkasan pesanan aktif dan poin loyalitas. |
| `/member/bookings` | Riwayat seluruh pemesanan yang dilakukan oleh member tersebut. |
| `/member/profile` | Mengelola data diri, password, dan integrasi nomor WhatsApp. |
| `/member/claim` | Fitur untuk mengklaim poin dari pemesanan yang sebelumnya dilakukan tanpa login (*guest booking*). |
| `/member/points` | Riwayat perolehan dan penggunaan poin loyalitas (Loyalty Points). |
| `/member/reviews/create` | Form bagi member untuk memberikan ulasan (rating & review) setelah menginap. |
| `/member/bookings/{code}/change`| Form pengajuan perubahan jadwal/pesanan (*Booking Change Request*). |

### Halaman Admin (Backend)
| Route / URL | Fungsi Utama |
|---|---|
| `/admin/login` | Halaman autentikasi khusus untuk administrator. |
| `/admin/dashboard` | Dasbor analitik utama admin (statistik pendapatan, pesanan hari ini, okupansi). |
| `/admin/room-types` | CRUD Tipe Kamar (contoh: Standar, VIP). |
| `/admin/rooms` | CRUD Kamar fisik spesifik berdasarkan nomor kamar. |
| `/admin/facilities` | CRUD Fasilitas yang bisa di-assign ke tipe kamar. |
| `/admin/bookings` | Manajemen seluruh data pesanan, proses Check-in, Check-out, No-show, dan Batal. |
| `/admin/calendar` | Tampilan kalender (Calendar View) untuk melihat ketersediaan kamar secara visual per tanggal. |
| `/admin/booking-changes` | Menyetujui atau menolak permintaan perubahan jadwal (reschedule) dari tamu. |
| `/admin/room-blocks` | Melakukan blokir pada kamar tertentu (misal: karena perbaikan/maintenance). |
| `/admin/rate-overrides` | Mengatur harga khusus (naik/turun) pada tanggal-tanggal tertentu (misal: *Peak Season* / Lebaran). |
| `/admin/promotions` | Manajemen kode promo atau diskon. |
| `/admin/loyalty` | Melihat dan menyesuaikan secara manual poin loyalitas pelanggan. |
| `/admin/refunds` & `expenses`| Manajemen pengembalian dana (refund) dan pencatatan pengeluaran operasional penginapan. |
| `/admin/reports/*` | Generate dan Export laporan (Pendapatan, Okupansi, Laba, dan Sumber Pesanan). |
| `/admin/settings` | Konfigurasi sistem global (kontak, logo, limit booking, dll). |

## 4. Alur Pengguna (User Flow)

### A. Alur Pemesanan Reguler (Guest/Member)
1. **Pencarian:** Tamu mengakses beranda (`/`) dan memasukkan tanggal Check-in & Check-out.
2. **Pilih Kamar:** Sistem mengarahkan ke halaman hasil pencarian (`/ketersediaan`), tamu memilih tipe kamar yang tersedia.
3. **Checkout:** Tamu mengisi form data diri (Nama, Email, No. HP, Catatan). Jika login, data terisi otomatis.
4. **Konfirmasi:** Sistem membuat Kode Booking dan menampilkan halaman konfirmasi reservasi (`/booking/{code}/konfirmasi`).
5. **Pembayaran:** Tamu klik "Bayar Sekarang" (`/booking/{code}/bayar`). Snap Midtrans muncul untuk memilih metode pembayaran (Transfer Bank, QRIS, dll).
6. **Selesai:** Setelah dibayar, Midtrans mengirim *Webhook* (ke `/webhook/midtrans`) mengubah status jadi `paid`. Tamu diarahkan ke halaman Sukses (`/booking/{code}/selesai`).
7. **Pasca-Pemesanan:** Tamu dapat mengunduh Invoice PDF, mengecek status via `/cek-booking`, atau bagi member dapat memberikan ulasan setelah check-out.

### B. Alur Check-in & Check-out (Admin)
1. Admin membuka menu **Bookings** (`/admin/bookings`) atau melihat pesanan hari ini di **Dashboard**.
2. Saat tamu datang, Admin mencari nama atau kode booking tamu.
3. Admin menekan tombol **Check-In** untuk mengubah status pesanan. Kamar fisik (nomor kamar) dialokasikan jika belum diatur sebelumnya.
4. Saat tamu pulang, Admin menekan tombol **Check-Out** (atau menandai pesanan selesai). Poin loyalitas secara otomatis diberikan kepada tamu jika mereka adalah Member.

## 5. Daftar Komponen UI (Blade Components)
Aplikasi ini dibangun menggunakan arsitektur komponen Blade untuk memastikan konsistensi UI. Seluruh komponen berada di direktori `resources/views/components/`:

- `x-alert`: Untuk menampilkan notifikasi pesan sukses, peringatan, atau error (Flash Message).
- `x-badge`: Label teks berukuran kecil untuk kategori/tag.
- `x-button`: Komponen standar tombol (Button) dengan berbagai variasi warna (Primary, Secondary, Danger, dll).
- `x-loading-button`: Tombol dengan efek *spinner/loading* otomatis saat di-klik (berbasis Alpine.js) guna mencegah multi-submit.
- `x-confirm-modal`: Modal dialog konfirmasi *pop-up* (contoh: "Yakin ingin menghapus?") menggunakan Alpine.js.
- `x-empty-state`: Tampilan *placeholder* ketika sebuah tabel atau daftar data sedang kosong/tidak ada.
- `x-form-error`: Menampilkan pesan validasi error merah di bawah input form.
- `x-password-input`: Input form khusus password dengan tombol "Show/Hide (Mata)" terintegrasi.
- `x-star-rating`: Menampilkan komponen bintang (1-5) untuk keperluan Review/Ulasan.
- `x-status-badge`: Badge khusus berwarna secara spesifik berdasarkan status tertentu (misal: Hijau untuk "Paid", Kuning untuk "Pending", Merah untuk "Cancelled").
- `x-toast`: Notifikasi *pop-up* melayang (Toast/Snackbar) di sudut layar.
- `x-whatsapp-link`: Link *shortcut* untuk membuka chat WhatsApp langsung ke nomor pengelola/admin.

Terdapat juga layout/struktur dasar yang berada di `resources/views/layouts/`:
- `public.blade.php`: Layout utama situs publik (termasuk Navbar responsif & Footer di dalamnya).
- `member.blade.php`: Layout area member (dilengkapi sidebar akun).
- `admin.blade.php` & `partials/admin-nav.blade.php`: Layout dasbor Admin lengkap dengan Sidebar menu manajemen.
