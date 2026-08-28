# Enums — Penginapan Kelapa Sawit

> **Sumber:** `app/Enums/` — diverifikasi dari source code aktual.

Semua enum adalah PHP 8.1+ backed string enum. Tidak ada magic string di codebase.

---

## BookingStatus

**File:** `app/Enums/BookingStatus.php`

| Case | Value | Label | Deskripsi |
|---|---|---|---|
| `PendingPayment` | `pending_payment` | Menunggu Pembayaran | Status awal booking website |
| `Confirmed` | `confirmed` | Dikonfirmasi | Pembayaran diterima atau OTA/walk-in |
| `CheckedIn` | `checked_in` | Check-in | Tamu sudah check-in |
| `CheckedOut` | `checked_out` | Check-out | Tamu sudah check-out |
| `Completed` | `completed` | Selesai | Proses selesai, poin diberikan |
| `Cancelled` | `cancelled` | Dibatalkan | Dibatalkan manual |
| `Expired` | `expired` | Kedaluwarsa | Pembayaran tidak diterima tepat waktu |
| `NoShow` | `no_show` | Tidak Datang | Tamu tidak datang |

**Transition yang diizinkan** (`allowedTransitions()`):

| Dari | Ke |
|---|---|
| `PendingPayment` | `Confirmed`, `Expired`, `Cancelled` |
| `Confirmed` | `CheckedIn`, `Cancelled`, `NoShow` |
| `CheckedIn` | `CheckedOut` |
| `CheckedOut` | `Completed` |
| `Completed` | *(terminal — tidak ada)* |
| `Cancelled` | *(terminal — tidak ada)* |
| `Expired` | *(terminal — tidak ada)* |
| `NoShow` | *(terminal — tidak ada)* |

**Helper methods:**
- `label()` → string label Bahasa Indonesia
- `color()` → Tailwind CSS class untuk badge
- `canTransitionTo(self $target)` → boolean
- `transitionTo(self $target)` → throws `InvalidStatusTransitionException` jika tidak valid
- `isTerminal()` → boolean (terminal = tidak ada allowed transitions)
- `isBlocking()` → boolean — `PendingPayment`, `Confirmed`, `CheckedIn` memblokir kamar untuk booking baru

---

## BookingSource

**File:** `app/Enums/BookingSource.php`

| Case | Value | Label |
|---|---|---|
| `Website` | `website` | Website |
| `Whatsapp` | `whatsapp` | WhatsApp |
| `BookingCom` | `booking_com` | Booking.com |
| `Agoda` | `agoda` | Agoda |
| `Traveloka` | `traveloka` | Traveloka |
| `WalkIn` | `walk_in` | Walk-in |
| `Phone` | `phone` | Telepon |
| `Other` | `other` | Lainnya |

**Eligible loyalty sources** (dari `config/booking.php`): `website`, `whatsapp`, `walk_in`

OTA (booking_com, agoda, traveloka) **tidak eligible** untuk loyalty poin di V1.

---

## PaymentStatus

**File:** `app/Enums/PaymentStatus.php`

| Case | Value | Keterangan |
|---|---|---|
| `Unpaid` | `unpaid` | Belum ada pembayaran |
| `Pending` | `pending` | Pembayaran diproses |
| `Paid` | `paid` | Pembayaran berhasil |
| `Failed` | `failed` | Pembayaran gagal |
| `Expired` | `expired` | Sesi pembayaran expired |
| `Refunded` | `refunded` | Dikembalikan penuh |
| `PartialRefund` | `partial_refund` | Dikembalikan sebagian |

---

## LoyaltyTransactionType

**File:** `app/Enums/LoyaltyTransactionType.php`

| Case | Value | Poin | Keterangan |
|---|---|---|---|
| `Earn` | `earn` | Positif | Booking selesai |
| `Redeem` | `redeem` | Negatif | Digunakan saat booking |
| `Expire` | `expire` | Negatif | Lot poin kedaluwarsa |
| `Adjustment` | `adjustment` | Positif/Negatif | Manual oleh admin |
| `Reversal` | `reversal` | Positif | Pembalikan redeem (saat cancel) |

---

## PromotionType

**File:** `app/Enums/PromotionType.php`

| Case | Value | Keterangan |
|---|---|---|
| `Percentage` | `percentage` | Diskon persen dari subtotal |
| `Fixed` | `fixed` | Diskon nominal tetap (IDR) |

---

## PromotionUsageStatus

**File:** `app/Enums/PromotionUsageStatus.php`

Lifecycle quota promo per booking:

| Case | Value | Keterangan |
|---|---|---|
| `Reserved` | `reserved` | Quota dikunci saat checkout, belum paid |
| `Consumed` | `consumed` | Promo dikonfirmasi setelah pembayaran |
| `Released` | `released` | Quota dibebaskan kembali saat cancel/expired |

---

## RefundStatus

**File:** `app/Enums/RefundStatus.php`

| Case | Value | Keterangan |
|---|---|---|
| `Requested` | `requested` | Admin submit permintaan refund |
| `Processing` | `processing` | Sedang diproses ke Midtrans |
| `Succeeded` | `succeeded` | Refund berhasil |
| `Failed` | `failed` | Refund gagal |
| `Cancelled` | `cancelled` | Permintaan dibatalkan |

---

## RoomStatus

**File:** `app/Enums/RoomStatus.php`

| Case | Value | Keterangan |
|---|---|---|
| `Active` | `active` | Kamar aktif dan bisa dijual |
| `Inactive` | `inactive` | Nonaktif |
| `Maintenance` | `maintenance` | Sedang perbaikan |

Room dengan `status = active` DAN `is_active = true` tersedia untuk dijual (`sellable()` scope).

---

## Penggunaan di Source Code

```php
// Perbandingan status (bukan string)
if ($booking->status === BookingStatus::Completed) { ... }

// Transition dengan validasi
$newStatus = $booking->status->transitionTo(BookingStatus::CheckedIn);

// Label display
$booking->status->label() // "Check-in"

// Cek blocking
BookingStatus::PendingPayment->isBlocking() // true

// Cek terminal
BookingStatus::Completed->isTerminal() // true
```

**Referensi:** `app/Exceptions/InvalidStatusTransitionException.php` — exception yang dilempar saat transition tidak valid.
