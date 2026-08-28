# Feature: Loyalty Points — Penginapan Kelapa Sawit

> **Sumber:** `app/Services/LoyaltyPointService.php`, `config/loyalty.php`, `app/Models/LoyaltyTransaction.php`

---

## Konfigurasi

**File:** `config/loyalty.php`

| Parameter | Env Key | Default | Keterangan |
|---|---|---|---|
| earn_divisor | `LOYALTY_EARN_DIVISOR` | 1000 | Rp1.000 = 1 poin |
| point_value | `LOYALTY_POINT_VALUE` | 50 | 1 poin = Rp50 |
| min_redeem | `LOYALTY_MIN_REDEEM` | 100 | Minimum 100 poin untuk redeem |
| max_redemption_percent | `LOYALTY_MAX_REDEMPTION_PERCENT` | 20 | Max 20% dari subtotal |
| expiry_months | `LOYALTY_EXPIRY_MONTHS` | 18 | Poin expired setelah 18 bulan |

---

## Earn (Mendapat Poin)

**File:** `app/Services/LoyaltyPointService.php::awardForCompletedBooking()`

**Kondisi:**
- Booking harus berstatus `Completed`
- Booking harus memiliki `user_id` (tidak bisa guest)
- Source booking harus eligible: `website`, `whatsapp`, `walk_in` (OTA tidak eligible V1)
- Idempotency key: `"earn:booking:{$booking->id}"` — tidak bisa dobel

**Formula:**
```
poin = floor(eligible_loyalty_amount / earn_divisor)
```

`eligible_loyalty_amount` adalah total setelah diskon (promo atau poin) — disimpan sebagai snapshot di booking.

---

## Redeem (Menggunakan Poin)

**File:** `app/Services/LoyaltyPointService.php::redeemForBooking()`

**Kondisi:**
- Minimum 100 poin
- Maksimum 20% dari subtotal booking
- Saldo harus mencukupi
- Idempotency key: `"redeem:booking:{$booking->id}"`

**Formula:**
```
max_discount = floor(subtotal * max_redemption_percent / 100)
requested_discount = requested_points * point_value
actual_discount = min(requested_discount, max_discount)
actual_points = ceil(actual_discount / point_value)
```

**FIFO (First In First Out):**
Poin dari lot dengan `expires_at` terdekat digunakan duluan. Implementasi via `LoyaltyPointAllocation`:
- Setiap redeem/expire membuat row di `loyalty_point_allocations`
- Lot `remaining_points` dikurangi sesuai alokasi

**Promo tidak dapat digabung dengan poin** dalam satu booking (V1).

---

## Reversal (Pembatalan Redeem)

**File:** `app/Services/LoyaltyPointService.php::reverseRedemptionForBooking()`

Saat booking dibatalkan/expired setelah poin diredeem:
- Cari transaksi `Redeem` untuk booking tersebut
- Buat transaksi `Reversal` (poin positif)
- Kembalikan `remaining_points` ke lot-lot asal via `LoyaltyPointAllocation`
- Idempotency key: `"reversal:redeem:booking:{$booking->id}"`

**Histori tidak dihapus.** Hanya buat transaksi baru untuk reversal.

---

## Expire

**File:** `app/Services/LoyaltyPointService.php::expirePointsForUser()`, `app/Console/Commands/ExpireLoyaltyPointsCommand.php`

- Jalankan harian via scheduler
- Temukan lot dengan `remaining_points > 0` dan `expires_at <= now()`
- Buat transaksi `Expire` (poin negatif) + alokasi
- Set `remaining_points = 0` pada lot yang expired
- Idempotency key: `"expire:loyalty_transaction:{$lot->id}"`

---

## Adjustment Manual

**File:** `app/Services/LoyaltyPointService.php::adjustPoints()`, `app/Http/Controllers/Admin/LoyaltyController.php`

Admin dapat menyesuaikan saldo poin secara manual:
- Poin positif: poin baru dengan expiry 18 bulan
- Poin negatif: pengurangan (tanpa expiry)
- Idempotency key: SHA-256 dari kombinasi user+points+reason+admin+date

**Route:** `POST /admin/loyalty/{user}/adjust`

---

## Saldo

**File:** `app/Services/LoyaltyPointService.php::getBalance()`

```php
$balance = LoyaltyTransaction::where('user_id', $user->id)->sum('points');
```

**Ledger adalah sumber kebenaran** — bukan `users.loyalty_balance_cache`. Cache hanya untuk display cepat dan di-update setelah setiap mutasi.

---

## Tabel Terkait

| Tabel | Kegunaan |
|---|---|
| `loyalty_transactions` | Ledger semua transaksi poin |
| `loyalty_point_allocations` | Penghubung FIFO debit-kredit |
| `users.loyalty_balance_cache` | Cache display (bukan sumber kebenaran) |
