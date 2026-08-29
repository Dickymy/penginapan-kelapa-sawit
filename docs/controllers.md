# Controllers — Penginapan Kelapa Sawit

> **Sumber:** `app/Http/Controllers/` — diverifikasi dari source code aktual.

Prinsip: controller tipis — terima request, validasi via Form Request, authorization via guard/check, panggil Service, return response.

---

## Public Controllers

**Folder:** `app/Http/Controllers/Public/`

| Controller | Method | Keterangan |
|---|---|---|
| `HomeController` | `index()` | Homepage |
| `RoomController` | `index()`, `show($slug)` | Daftar & detail tipe kamar |
| `AvailabilityController` | `search(Request)` | Hasil pencarian ketersediaan |
| `BookingController` | `showCheckout()`, `store()`, `confirmation()`, `verifyForm()`, `verifyAccess()`, `myBooking()`, `guestDetail()` | Alur booking guest/member |
| `PaymentController` | `pay($bookingCode)`, `finish($bookingCode)` | Halaman bayar Midtrans & callback |
| `InvoiceController` | `download($bookingCode)` | Download PDF invoice |
| `PageController` | `about()`, `location()`, `policy()` | Halaman statis |
| `ContactController` | `create()`, `store()` | Form kontak (throttle 3/10mnt) |
| `GalleryController` | `index()` | Galeri foto publik |
| `FaqController` | `index()` | Halaman FAQ |
| `NearbyPlaceController` | `index()` | Tempat sekitar |

---

## Auth Controllers

**Folder:** `app/Http/Controllers/Auth/`

| Controller | Method | Keterangan |
|---|---|---|
| `GoogleController` | `redirect()`, `callback()` | Google OAuth via Socialite |

> Login/register/verify/reset dihandle oleh Laravel Fortify (`app/Actions/Fortify/`, `app/Providers/FortifyServiceProvider.php`). Custom response login: `app/Http/Responses/LoginResponse.php`.

---

## Member Controllers

**Folder:** `app/Http/Controllers/Member/`

Middleware: `auth`, `verified`

| Controller | Method | Keterangan |
|---|---|---|
| `DashboardController` | `index()` | Dashboard member |
| `BookingController` | `index()`, `show()`, `cancel()` | Daftar & detail booking member, cancel |
| `ProfileController` | `edit()`, `update()`, `updateWhatsapp()` | Edit profil & WhatsApp |
| `ClaimController` | `index()`, `claim(Booking)` | Klaim guest booking |
| `PointController` | `index()` | Riwayat & saldo poin |
| `ReviewController` | `create(Booking)`, `store()` | Buat ulasan (throttle 5/60mnt) |
| `BookingChangeRequestController` | `create(Booking)`, `store()` | Request perubahan booking |

---

## Admin Auth Controller

**Folder:** `app/Http/Controllers/Admin/Auth/`

| Controller | Method | Keterangan |
|---|---|---|
| `LoginController` | `showLoginForm()`, `login()`, `logout()` | Auth admin terpisah (guard: admin) |

---

## Admin Controllers

**Folder:** `app/Http/Controllers/Admin/`

Middleware: `auth:admin`

| Controller | Methods Utama | Keterangan |
|---|---|---|
| `DashboardController` | `index()` | Dashboard admin |
| `RoomTypeController` | index, create, store, edit, update, `toggleActive()` | Manajemen tipe kamar |
| `RoomController` | index, create, store, edit, update, `toggleActive()` | Manajemen kamar fisik |
| `RoomImageController` | `store()`, `setCover()`, `destroy()` | Upload & kelola gambar kamar |
| `FacilityController` | index, create, store, edit, update, destroy | CRUD fasilitas |
| `BookingController` | index, `export()`, create, store, show, `cancel()`, `checkIn()`, `checkOut()`, `complete()`, `noShow()` | Full booking management |
| `BookingChangeRequestController` | index, show, `approve()`, `reject()` | Persetujuan perubahan booking |
| `RoomBlockController` | index, create, store, destroy | Blokir kamar manual |
| `CalendarController` | `index()`, `data()` | Kalender visual (data() return JSON) |
| `LoyaltyController` | `index()`, `show($user)`, `adjust($user)` | Manajemen poin member |
| `PromotionController` | index, create, store, edit, update, destroy | CRUD promo |
| `AddonController` | index, create, store, edit, update, destroy | CRUD addon |
| `RefundController` | `create($booking)`, `store($booking)` | Submit refund |
| `ExpenseController` | index, create, store, edit, update, destroy | CRUD pengeluaran |
| `ReportController` | `revenue()`, `exportRevenue()`, `occupancy()`, `exportOccupancy()`, `profit()`, `sources()` | Laporan (CSV export tersedia) |
| `ReviewController` | `index()`, `publish()`, `reply()` | Moderasi ulasan |
| `FaqController` | index, create, store, edit, update, destroy | CRUD FAQ |
| `ContactMessageController` | index, show, `markRead()`, destroy | Pesan kontak |
| `NearbyPlaceController` | index, create, store, show, edit, update, destroy | CRUD tempat sekitar |
| `GalleryController` | index, store, update, `toggleActive()`, `reorder()`, destroy | Kelola galeri foto |
| `SettingsController` | `edit($group)`, `update($group)` | Edit pengaturan per group |
| `PolicyVersionController` | index, create, store, show, `publish()` | Versi kebijakan |
| `RateOverrideController` | index, store, destroy | Override harga per tanggal |

---

## Webhook Controller

**Folder:** `app/Http/Controllers/Webhook/`

| Controller | Method | Keterangan |
|---|---|---|
| `MidtransWebhookController` | `handle(Request)` | Terima & proses webhook Midtrans (tanpa CSRF, tanpa session) |
