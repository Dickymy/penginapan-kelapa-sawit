# Events, Listeners & Mail — Penginapan Kelapa Sawit

> **Sumber:** `app/Events/`, `app/Listeners/`, `app/Mail/` — diverifikasi dari source code aktual.

---

## Events

**Folder:** `app/Events/`

Semua event menggunakan trait `Dispatchable`, `InteractsWithSockets`, `SerializesModels`.

| Event | File | Properties | Di-dispatch dari |
|---|---|---|---|
| `BookingCreated` | `app/Events/BookingCreated.php` | `public Booking $booking` | `BookingService::createBooking()` — setelah transaction, cek `wasRecentlyCreated` |
| `BookingCancelled` | `app/Events/BookingCancelled.php` | `public Booking $booking` | `BookingService::expirePendingBooking()` |
| `PaymentConfirmed` | `app/Events/PaymentConfirmed.php` | `public Booking $booking`, `public Payment $payment` | `MidtransPaymentService::handleWebhook()` — setelah status booking jadi `Confirmed` |

**Catatan:** Event di-dispatch **di luar** `DB::transaction()` untuk mencegah email terkirim jika transaction rollback.

---

## Listeners

**Folder:** `app/Listeners/`

Semua listener mengimplementasikan `ShouldQueue` — dijalankan secara async di queue.

### SendBookingConfirmationListener

**File:** `app/Listeners/SendBookingConfirmationListener.php`  
**Mendengarkan:** `BookingCreated`  
**Mengirim:** `BookingConfirmationMail`

**Logika:**
1. Skip jika `$booking->guest_email` kosong
2. Idempotency: skip jika `confirmation_email_sent_at !== null`
3. Kirim email → update `confirmation_email_sent_at = now()`
4. Re-throw exception agar queue bisa retry

### SendPaymentSuccessListener

**File:** `app/Listeners/SendPaymentSuccessListener.php`  
**Mendengarkan:** `PaymentConfirmed`  
**Mengirim:** `PaymentSuccessMail`

**Logika:**
1. Skip jika `$booking->guest_email` kosong
2. Idempotency: skip jika `payment_email_sent_at !== null`
3. Kirim email → update `payment_email_sent_at = now()`

### SendBookingCancelledListener

**File:** `app/Listeners/SendBookingCancelledListener.php`  
**Mendengarkan:** `BookingCancelled`  
**Mengirim:** `BookingCancelledMail`

**Logika:**
1. Skip jika `$booking->guest_email` kosong
2. Idempotency: skip jika `cancellation_email_sent_at !== null`
3. Kirim email → update `cancellation_email_sent_at = now()`

---

## Mailable Classes

**Folder:** `app/Mail/`

Semua Mailable mengimplementasikan `ShouldQueue`.

| Mailable | File | Trigger | Template View |
|---|---|---|---|
| `BookingConfirmationMail` | `BookingConfirmationMail.php` | `SendBookingConfirmationListener` | `resources/views/mail/booking-confirmation.blade.php` |
| `PaymentSuccessMail` | `PaymentSuccessMail.php` | `SendPaymentSuccessListener` | `resources/views/mail/payment-success.blade.php` |
| `BookingCancelledMail` | `BookingCancelledMail.php` | `SendBookingCancelledListener` | `resources/views/mail/booking-cancelled.blade.php` |
| `CheckinReminderMail` | `CheckinReminderMail.php` | Command `SendCheckinReminders` | `resources/views/mail/checkin-reminder.blade.php` |
| `PostCheckoutMail` | `PostCheckoutMail.php` | Command `SendPostCheckoutEmails` | `resources/views/mail/post-checkout.blade.php` |
| `ContactAutoReplyMail` | `ContactAutoReplyMail.php` | `ContactController@store` | `resources/views/mail/contact-auto-reply.blade.php` |
| `NewReviewNotificationMail` | `NewReviewNotificationMail.php` | `Member\ReviewController@store` | `resources/views/mail/admin/new_review.blade.php` |
| `BookingChangeRequestMail` | `BookingChangeRequestMail.php` | `BookingChangeService` | `resources/views/emails/booking_change_request.blade.php` |
| `BookingChangeResultMail` | `BookingChangeResultMail.php` | `BookingChangeService` | `resources/views/emails/booking_change_result.blade.php` |

**Layout email:** `resources/views/mail/layout.blade.php` — digunakan bersama oleh template email.

---

## Scheduled Commands untuk Email

**File:** `app/Console/Commands/SendCheckinReminders.php` & `SendPostCheckoutEmails.php`

### SendCheckinReminders

| Aspek | Detail |
|---|---|
| Artisan signature | `booking:send-checkin-reminders` |
| Jadwal | Daily 09:00 WITA |
| Target | Booking `status = confirmed`, `check_in = tomorrow`, `reminder_email_sent_at IS NULL` |
| Idempotency | Set `reminder_email_sent_at = now()` setelah kirim |

### SendPostCheckoutEmails

| Aspek | Detail |
|---|---|
| Artisan signature | `booking:send-post-checkout-emails` |
| Jadwal | Daily 10:00 WITA |
| Target | Booking `status = checked_out`, `checked_out_at = yesterday`, `checkout_email_sent_at IS NULL` |
| Idempotency | Set `checkout_email_sent_at = now()` setelah kirim |

---

## Idempotency Email

Setiap pengiriman email dicatat di kolom timestamp booking (`*_email_sent_at`). Ini memastikan:
- Email tidak terkirim dobel jika listener/command dijalankan lebih dari sekali
- Data ada di kolom `bookings` — tidak perlu tabel terpisah

**Kolom tracking** (dari `2026_08_19_125640_add_email_tracking_to_bookings_table.php`):
- `confirmation_email_sent_at`
- `payment_email_sent_at`
- `reminder_email_sent_at`
- `checkout_email_sent_at`
- `cancellation_email_sent_at`
