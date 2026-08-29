# Views — Penginapan Kelapa Sawit

> **Sumber:** `resources/views/` — diverifikasi dari struktur folder aktual.

---

## Layouts (3 layout)

**Folder:** `resources/views/layouts/`

| File | Kegunaan |
|---|---|
| `public.blade.php` | Layout halaman publik — navbar, footer |
| `member.blade.php` | Layout area member — navbar + menu member |
| `admin.blade.php` | Layout admin — sidebar + topbar |
| `partials/admin-nav.blade.php` | Partial navigasi sidebar admin |

---

## Halaman Publik

**Folder:** `resources/views/public/`

| View | Route | Controller |
|---|---|---|
| `home.blade.php` | `/` | `HomeController@index` |
| `about.blade.php` | `/tentang` | `PageController@about` |
| `location.blade.php` | `/lokasi` | `PageController@location` |
| `policy.blade.php` | `/kebijakan` | `PageController@policy` |
| `contact.blade.php` | `/hubungi` | `ContactController@create` |
| `gallery.blade.php` | `/galeri` | `GalleryController@index` |
| `faq.blade.php` | `/faq` | `FaqController@index` |
| `nearby-places.blade.php` | `/sekitar` | `NearbyPlaceController@index` |

### Rooms

| View | Route |
|---|---|
| `rooms/index.blade.php` | `/kamar` |
| `rooms/show.blade.php` | `/kamar/{slug}` |

### Availability

| View | Route |
|---|---|
| `availability/results.blade.php` | `/ketersediaan` |

### Booking

| View | Route |
|---|---|
| `booking/checkout.blade.php` | `/checkout` |
| `booking/confirmation.blade.php` | `/booking/{code}/konfirmasi` |
| `booking/detail.blade.php` | `/booking/{code}/detail` |
| `booking/my-booking.blade.php` | `/booking-saya` |
| `booking/verify.blade.php` | `/cek-booking` |
| `booking/pay.blade.php` | `/booking/{code}/bayar` |
| `booking/finish.blade.php` | `/booking/{code}/selesai` |
| `booking/pay-error.blade.php` | — |
| `booking/status.blade.php` | — |

---

## Halaman Auth

**Folder:** `resources/views/auth/`

| File | Kegunaan |
|---|---|
| `login.blade.php` | Login member |
| `register.blade.php` | Register member |
| `verify-email.blade.php` | Verifikasi email |
| `forgot-password.blade.php` | Form lupa password |
| `reset-password.blade.php` | Form reset password |

---

## Halaman Member

**Folder:** `resources/views/member/`

| View | Route |
|---|---|
| `dashboard.blade.php` | `/member/dashboard` |
| `bookings/index.blade.php` | `/member/bookings` |
| `bookings/show.blade.php` | `/member/bookings/{booking}` |
| `bookings/change.blade.php` | `/member/bookings/{booking}/change` |
| `profile/edit.blade.php` | `/member/profile` |
| `claim/index.blade.php` | `/member/claim` |
| `points/index.blade.php` | `/member/points` |
| `reviews/create.blade.php` | `/member/reviews/create/{booking}` |

---

## Halaman Admin

**Folder:** `resources/views/admin/`

### Auth
- `auth/login.blade.php`

### Dashboard
- `dashboard.blade.php`

### Room Management
- `room-types/index.blade.php`, `room-types/create.blade.php`, `room-types/edit.blade.php`
- `rooms/index.blade.php`, `rooms/create.blade.php`, `rooms/edit.blade.php`
- `facilities/index.blade.php`, `facilities/create.blade.php`, `facilities/edit.blade.php`
- `galleries/index.blade.php`
- `calendar/index.blade.php`
- `room-blocks/index.blade.php`, `room-blocks/create.blade.php`
- `rate-overrides/index.blade.php`

### Booking Management
- `bookings/index.blade.php`, `bookings/create.blade.php`, `bookings/show.blade.php`
- `booking-changes/index.blade.php`, `booking-changes/show.blade.php`

### Business
- `loyalty/index.blade.php`, `loyalty/show.blade.php`
- `promotions/index.blade.php`, `promotions/create.blade.php`, `promotions/edit.blade.php`, `promotions/_form.blade.php`
- `addons/index.blade.php`, `addons/create.blade.php`, `addons/edit.blade.php`
- `refunds/create.blade.php`
- `expenses/index.blade.php`, `expenses/create.blade.php`, `expenses/edit.blade.php`

### Reports
- `reports/revenue.blade.php`
- `reports/occupancy.blade.php`
- `reports/profit.blade.php`
- `reports/sources.blade.php`

### Content Management
- `reviews/index.blade.php`
- `faqs/index.blade.php`, `faqs/create.blade.php`, `faqs/edit.blade.php`
- `contact-messages/index.blade.php`, `contact-messages/show.blade.php`
- `nearby-places/index.blade.php`, `nearby-places/create.blade.php`, `nearby-places/edit.blade.php`, `nearby-places/show.blade.php`
- `policies/index.blade.php`, `policies/create.blade.php`, `policies/show.blade.php`
- `settings/edit.blade.php`

---

## Blade Components

**Folder:** `resources/views/components/`

| Component | Tag | Kegunaan |
|---|---|---|
| `alert.blade.php` | `<x-alert>` | Alert/flash message |
| `badge.blade.php` | `<x-badge>` | Badge label |
| `button.blade.php` | `<x-button>` | Tombol standar |
| `confirm-modal.blade.php` | `<x-confirm-modal>` | Modal konfirmasi aksi (Alpine.js) |
| `empty-state.blade.php` | `<x-empty-state>` | Tampilan data kosong |
| `form-error.blade.php` | `<x-form-error>` | Error pesan validasi form |
| `loading-button.blade.php` | `<x-loading-button>` | Tombol dengan loading state |
| `password-input.blade.php` | `<x-password-input>` | Input password dengan toggle show/hide |
| `star-rating.blade.php` | `<x-star-rating>` | Bintang rating (SVG, parameter `$rating`) |
| `status-badge.blade.php` | `<x-status-badge>` | Badge status booking dengan warna dari Enum |
| `toast.blade.php` | `<x-toast>` | Toast notification |
| `whatsapp-link.blade.php` | `<x-whatsapp-link>` | Link WhatsApp dengan URL wa.me |

---

## Email Templates

**Folder:** `resources/views/mail/` dan `resources/views/emails/`

| Template | Mailable | Keterangan |
|---|---|---|
| `mail/layout.blade.php` | — | Layout dasar semua email |
| `mail/booking-confirmation.blade.php` | `BookingConfirmationMail` | Email booking baru |
| `mail/payment-success.blade.php` | `PaymentSuccessMail` | Email pembayaran berhasil |
| `mail/booking-cancelled.blade.php` | `BookingCancelledMail` | Email booking dibatalkan/expired |
| `mail/checkin-reminder.blade.php` | `CheckinReminderMail` | Pengingat H-1 check-in |
| `mail/post-checkout.blade.php` | `PostCheckoutMail` | Email post-checkout |
| `mail/contact-auto-reply.blade.php` | `ContactAutoReplyMail` | Auto-reply pesan kontak |
| `mail/admin/new_review.blade.php` | `NewReviewNotificationMail` | Notifikasi review baru ke admin |
| `emails/booking_change_request.blade.php` | `BookingChangeRequestMail` | Email permintaan perubahan |
| `emails/booking_change_result.blade.php` | `BookingChangeResultMail` | Email hasil perubahan |

---

## Invoice PDF

**File:** `resources/views/invoices/booking.blade.php`

- Render via `barryvdh/laravel-dompdf`
- Menggunakan data snapshot booking, bukan harga terbaru
- Diakses via `GET /booking/{code}/invoice`

---

## Error Pages

**Folder:** `resources/views/errors/`

| File | HTTP Status |
|---|---|
| `403.blade.php` | Forbidden |
| `404.blade.php` | Not Found |
| `419.blade.php` | CSRF Token Mismatch |
| `429.blade.php` | Too Many Requests |
| `500.blade.php` | Server Error |
| `503.blade.php` | Service Unavailable |
