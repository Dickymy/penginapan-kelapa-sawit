# Feature: Promotion — Penginapan Kelapa Sawit

> **Sumber:** `app/Services/PromotionService.php`, `app/Models/Promotion.php`, `app/Models/PromotionUsage.php`

---

## Tipe Promo

| Tipe | Enum | Keterangan |
|---|---|---|
| Persentase | `PromotionType::Percentage` | Diskon % dari subtotal, bisa ada cap (`maximum_discount`) |
| Nominal | `PromotionType::Fixed` | Diskon nominal tetap (IDR) |

---

## Aturan Validasi (Backend Only)

Referensi: `app/Services/PromotionService.php::validateForQuote()`

Semua validasi dilakukan backend. Frontend tidak pernah dipercaya untuk menentukan nominal diskon.

1. Kode promo valid dan ada
2. `is_active = true`
3. `starts_at <= now() <= ends_at` (atau null = tidak ada batas waktu)
4. `subtotal >= minimum_booking_amount`
5. Quota belum habis (`usage_quota` - count consumed < 0 adalah habis)
6. `max_usage_per_user` belum terlampaui (untuk user yang login)

---

## Lifecycle Usage

Referensi: `app/Services/PromotionService.php`, `app/Enums/PromotionUsageStatus.php`

```
reserved → consumed  (saat webhook payment confirmed)
reserved → released  (saat booking expired/cancelled)
```

| Status | Kapan |
|---|---|
| `reserved` | Saat booking dibuat — quota dikunci |
| `consumed` | Saat `PaymentConfirmed` — promo dikonfirmasi |
| `released` | Saat `BookingCancelled`/`Expired` — quota dibebaskan |

**Row lock saat reserve:** `promotions` di-lock dengan `lockForUpdate` dalam transaction untuk mencegah race condition quota.

---

## Promo + Poin

**Tidak dapat digabung dalam satu booking (V1).**

Referensi: `app/Services/PricingService.php`

`calculateQuoteWithPromo()` dan `calculateQuoteWithPoints()` adalah dua path terpisah — tidak ada method `calculateQuoteWithPromoAndPoints()`.

---

## Admin Management

**Routes:** `/admin/promotions` (resource — index, create, store, edit, update, destroy)

**Form Request:** `app/Http/Requests/Admin/StorePromotionRequest.php`

**View:** `resources/views/admin/promotions/` (index, create, edit, _form)
