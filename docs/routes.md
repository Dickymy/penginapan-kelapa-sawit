# Routes — Penginapan Kelapa Sawit

> **Sumber:** `routes/web.php` — diverifikasi dari source code aktual.
> Tidak ada `routes/api.php`. Semua endpoint dalam satu file `routes/web.php`.

---

## Public Routes (tanpa auth)

| Method | URI | Name | Controller | Keterangan |
|---|---|---|---|---|
| GET | `/` | `home` | `Public\HomeController@index` | Homepage |
| GET | `/kamar` | `rooms.index` | `Public\RoomController@index` | Daftar tipe kamar |
| GET | `/kamar/{slug}` | `rooms.show` | `Public\RoomController@show` | Detail tipe kamar |
| GET | `/tentang` | `about` | `Public\PageController@about` | Halaman tentang |
| GET | `/lokasi` | `location` | `Public\PageController@location` | Halaman lokasi |
| GET | `/kebijakan` | `policy` | `Public\PageController@policy` | Halaman kebijakan |
| GET | `/hubungi` | `contact.create` | `Public\ContactController@create` | Form kontak |
| POST | `/hubungi` | `contact.store` | `Public\ContactController@store` | Kirim pesan (throttle: 3/10min) |
| GET | `/galeri` | `gallery` | `Public\GalleryController@index` | Galeri foto |
| GET | `/faq` | `faq` | `Public\FaqController@index` | FAQ |
| GET | `/sekitar` | `nearby-places` | `Public\NearbyPlaceController@index` | Tempat sekitar |

---

## Availability & Booking Routes (tanpa auth)

| Method | URI | Name | Controller | Keterangan |
|---|---|---|---|---|
| GET | `/ketersediaan` | `availability.search` | `Public\AvailabilityController@search` | Hasil pencarian kamar |
| GET | `/checkout` | `booking.checkout` | `Public\BookingController@showCheckout` | Form checkout |
| POST | `/booking` | `booking.store` | `Public\BookingController@store` | Buat booking (throttle: 5/mnt) |
| GET | `/booking/{bookingCode}/konfirmasi` | `booking.confirmation` | `Public\BookingController@confirmation` | Halaman konfirmasi |
| GET | `/cek-booking` | `booking.verify.form` | `Public\BookingController@verifyForm` | Form cek booking |
| POST | `/cek-booking` | `booking.verify` | `Public\BookingController@verifyAccess` | Verifikasi akses (throttle: 10/mnt) |
| GET | `/booking-saya` | `booking.my` | `Public\BookingController@myBooking` | Redirect cek booking (guest) |
| GET | `/booking/{bookingCode}/detail` | `booking.guest.detail` | `Public\BookingController@guestDetail` | Detail booking (guest) |

---

## Payment Routes (tanpa auth)

| Method | URI | Name | Controller | Keterangan |
|---|---|---|---|---|
| GET | `/booking/{bookingCode}/bayar` | `booking.pay` | `Public\PaymentController@pay` | Halaman bayar (Snap) — throttle: 10/mnt |
| GET | `/booking/{bookingCode}/selesai` | `booking.finish` | `Public\PaymentController@finish` | Callback selesai bayar |
| GET | `/booking/{bookingCode}/invoice` | `booking.invoice` | `Public\InvoiceController@download` | Download PDF invoice |

---

## Auth Routes

| Method | URI | Name | Controller | Keterangan |
|---|---|---|---|---|
| GET | `/auth/google` | `auth.google` | `Auth\GoogleController@redirect` | Redirect ke Google OAuth |
| GET | `/auth/google/callback` | `auth.google.callback` | `Auth\GoogleController@callback` | Callback Google OAuth |

> Fortify mengelola: `/login`, `/register`, `/forgot-password`, `/reset-password/{token}`, `/email/verify`

---

## Webhook Routes (tanpa CSRF)

| Method | URI | Name | Controller | Keterangan |
|---|---|---|---|---|
| POST | `/webhook/midtrans` | `webhook.midtrans` | `Webhook\MidtransWebhookController@handle` | Webhook Midtrans — throttle: 60/mnt, tanpa CSRF |

---

## Member Routes (prefix `/member`, middleware: `auth`, `verified`)

| Method | URI | Name | Controller |
|---|---|---|---|
| GET | `/member/dashboard` | `member.dashboard` | `Member\DashboardController@index` |
| GET | `/member/bookings` | `member.bookings.index` | `Member\BookingController@index` |
| GET | `/member/bookings/{booking}` | `member.bookings.show` | `Member\BookingController@show` |
| PATCH | `/member/bookings/{booking}/cancel` | `member.bookings.cancel` | `Member\BookingController@cancel` |
| GET | `/member/profile` | `member.profile.edit` | `Member\ProfileController@edit` |
| PUT | `/member/profile` | `member.profile.update` | `Member\ProfileController@update` |
| PUT | `/member/profile/whatsapp` | `member.profile.update-whatsapp` | `Member\ProfileController@updateWhatsapp` |
| GET | `/member/claim` | `member.claim.index` | `Member\ClaimController@index` |
| POST | `/member/claim/{booking}` | `member.claim.claim` | `Member\ClaimController@claim` |
| GET | `/member/points` | `member.points.index` | `Member\PointController@index` |
| GET | `/member/reviews/create/{booking}` | `member.reviews.create` | `Member\ReviewController@create` |
| POST | `/member/reviews` | `member.reviews.store` | `Member\ReviewController@store` (throttle: 5/60mnt) |
| GET | `/member/bookings/{booking}/change` | `member.booking-changes.create` | `Member\BookingChangeRequestController@create` |
| POST | `/member/bookings/{booking}/change` | `member.booking-changes.store` | `Member\BookingChangeRequestController@store` |

---

## Admin Routes (prefix `/admin`, middleware: `auth:admin`)

### Auth Admin

| Method | URI | Name | Keterangan |
|---|---|---|---|
| GET | `/admin/login` | `admin.login` | Form login (middleware: `guest:admin`) |
| POST | `/admin/login` | — | Proses login (throttle: 5/mnt) |
| POST | `/admin/logout` | `admin.logout` | Logout |

### Dashboard & Core

| Method | URI | Name | Controller |
|---|---|---|---|
| GET | `/admin/dashboard` | `admin.dashboard` | `Admin\DashboardController@index` |

### Room Types & Rooms

| Method | URI | Name | Controller |
|---|---|---|---|
| GET | `/admin/room-types` | `admin.room-types.index` | `Admin\RoomTypeController@index` |
| GET | `/admin/room-types/create` | `admin.room-types.create` | `Admin\RoomTypeController@create` |
| POST | `/admin/room-types` | `admin.room-types.store` | `Admin\RoomTypeController@store` |
| GET | `/admin/room-types/{room_type}/edit` | `admin.room-types.edit` | `Admin\RoomTypeController@edit` |
| PUT/PATCH | `/admin/room-types/{room_type}` | `admin.room-types.update` | `Admin\RoomTypeController@update` |
| PATCH | `/admin/room-types/{room_type}/toggle` | `admin.room-types.toggle` | `Admin\RoomTypeController@toggleActive` |
| GET/POST | `/admin/rooms` (resource) | `admin.rooms.*` | `Admin\RoomController` (index, create, store, edit, update) |
| PATCH | `/admin/rooms/{room}/toggle` | `admin.rooms.toggle` | `Admin\RoomController@toggleActive` |

### Room Images

| Method | URI | Name | Controller |
|---|---|---|---|
| POST | `/admin/room-types/{room_type}/images` | `admin.room-images.store` | `Admin\RoomImageController@store` |
| PATCH | `/admin/room-images/{image}/cover` | `admin.room-images.cover` | `Admin\RoomImageController@setCover` |
| DELETE | `/admin/room-images/{image}` | `admin.room-images.destroy` | `Admin\RoomImageController@destroy` |

### Facilities & Settings

| Method | URI | Name |
|---|---|---|
| Resource | `/admin/facilities` | `admin.facilities.*` (index, create, store, edit, update, destroy) |
| GET/PUT | `/admin/settings/{group}` | `admin.settings.edit`, `admin.settings.update` |

### Policy Versions

| Method | URI | Name |
|---|---|---|
| Resource (except edit, update, destroy) | `/admin/policies` | `admin.policies.*` (index, create, store, show) |
| PATCH | `/admin/policies/{policy}/publish` | `admin.policies.publish` |

### Gallery & Rate Overrides

| Method | URI | Name |
|---|---|---|
| GET | `/admin/galleries` | `admin.galleries.index` |
| POST | `/admin/galleries` | `admin.galleries.store` |
| PATCH | `/admin/galleries/{gallery}` | `admin.galleries.update` |
| PATCH | `/admin/galleries/{gallery}/toggle` | `admin.galleries.toggle` |
| POST | `/admin/galleries/reorder` | `admin.galleries.reorder` |
| DELETE | `/admin/galleries/{gallery}` | `admin.galleries.destroy` |
| Resource (index, store, destroy) | `/admin/rate-overrides` | `admin.rate-overrides.*` |

### Calendar

| Method | URI | Name |
|---|---|---|
| GET | `/admin/calendar` | `admin.calendar.index` |
| GET | `/admin/calendar/data` | `admin.calendar.data` (JSON response) |

### Bookings

| Method | URI | Name |
|---|---|---|
| GET | `/admin/bookings/export` | `admin.bookings.export` (CSV) |
| GET | `/admin/bookings` | `admin.bookings.index` |
| GET | `/admin/bookings/create` | `admin.bookings.create` |
| POST | `/admin/bookings` | `admin.bookings.store` |
| GET | `/admin/bookings/{booking}` | `admin.bookings.show` |
| PATCH | `/admin/bookings/{booking}/cancel` | `admin.bookings.cancel` |
| PATCH | `/admin/bookings/{booking}/check-in` | `admin.bookings.check-in` |
| PATCH | `/admin/bookings/{booking}/check-out` | `admin.bookings.check-out` |
| PATCH | `/admin/bookings/{booking}/complete` | `admin.bookings.complete` |
| PATCH | `/admin/bookings/{booking}/no-show` | `admin.bookings.no-show` |

### Booking Changes, Room Blocks, Loyalty

| Group | Routes |
|---|---|
| Booking Changes | GET index, GET show, POST approve, POST reject |
| Room Blocks | GET index, GET create, POST store, DELETE destroy |
| Loyalty | GET index, GET show/{user}, POST adjust/{user} |

### Promotions, Addons, Refunds

| Group | Routes |
|---|---|
| Promotions | Resource (index, create, store, edit, update, destroy) — tanpa show |
| Addons | Resource (index, create, store, edit, update, destroy) — tanpa show |
| Refunds | GET create/{booking}, POST store/{booking} |

### Expenses & Reports

| Group | Routes |
|---|---|
| Expenses | Resource (index, create, store, edit, update, destroy) — tanpa show |
| Reports | GET revenue, GET revenue/export, GET occupancy, GET occupancy/export, GET profit, GET sources |

### Reviews, FAQs, Contact, Nearby Places

| Group | Routes |
|---|---|
| Reviews | GET index, PATCH publish/{review}, POST reply/{review} |
| FAQs | Resource (index, create, store, edit, update, destroy) — tanpa show |
| Contact Messages | GET index, GET show, PATCH mark-read, DELETE destroy |
| Nearby Places | Full resource (index, create, store, show, edit, update, destroy) |

---

## Rate Limiters Kustom

Didefinisikan di `app/Providers/AppServiceProvider.php`:

| Name | Limit | By |
|---|---|---|
| `booking-verify` | 10/menit | IP |
| `booking-store` | 5/menit | user ID atau IP |
| `payment-initiate` | 10/menit | IP |
| `admin-login` | 5/menit | IP |
