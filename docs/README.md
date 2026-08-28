# Dokumentasi — Penginapan Kelapa Sawit

> Dokumentasi teknis project. Semua dokumen diverifikasi terhadap source code aktual.
> **Terakhir diperbarui:** Agustus 2026

---

## Daftar Dokumen

| File | Isi |
|---|---|
| [overview.md](overview.md) | Gambaran umum produk, pengguna, stack, konfigurasi bisnis |
| [architecture.md](architecture.md) | Stack lengkap, struktur folder, auth, service architecture, konvensi |
| [models.md](models.md) | Semua Eloquent model, relasi, fillable, casts |
| [controllers.md](controllers.md) | Semua controller dan method-nya |
| [services.md](services.md) | Semua service dengan method dan tanggung jawab |
| [routes.md](routes.md) | Semua route (public, auth, member, admin, webhook) |
| [database.md](database.md) | Skema tabel, kolom, dan relasi |
| [enums.md](enums.md) | Semua enum, cases, transition rules |
| [events-listeners-mail.md](events-listeners-mail.md) | Events, listeners, mailable classes, email tracking |
| [views.md](views.md) | Semua views, layouts, components, email templates |
| [commands.md](commands.md) | Artisan commands dan scheduler |
| [dependencies.md](dependencies.md) | Backend dan frontend dependencies |

### Feature Docs

| File | Isi |
|---|---|
| [features/booking-flow.md](features/booking-flow.md) | Alur booking, double-booking protection, akses guest |
| [features/payment.md](features/payment.md) | Integrasi Midtrans Snap, webhook, refund |
| [features/loyalty.md](features/loyalty.md) | Sistem poin: earn, redeem, expire, FIFO |
| [features/promotion.md](features/promotion.md) | Promo kode, validasi backend, lifecycle quota |

---

## Quick Reference

### Booking Status Flow
```
pending_payment → confirmed → checked_in → checked_out → completed
               ↘ expired
               ↘ cancelled
    confirmed  → no_show
```

### Harga & Diskon
```
subtotal = Σ harga_per_malam (base_price atau rate_override)
total = max(0, subtotal - promotion_discount - points_discount)
eligible_loyalty_amount = total setelah diskon
```

### Loyalty Earn
```
poin = floor(eligible_loyalty_amount / 1000)  # default config
```

### Loyalty Redeem
```
max_discount = floor(subtotal × 20%)
actual_discount = min(requested_points × 50, max_discount)
```

### Key Files untuk Perubahan Umum

| Task | File Utama |
|---|---|
| Tambah halaman publik | `routes/web.php`, `app/Http/Controllers/Public/`, `resources/views/public/` |
| Ubah logika booking | `app/Services/BookingService.php` |
| Ubah kalkulasi harga | `app/Services/PricingService.php` |
| Ubah logika poin | `app/Services/LoyaltyPointService.php` |
| Tambah admin page | `routes/web.php`, `app/Http/Controllers/Admin/`, `resources/views/admin/` |
| Ubah layout publik | `resources/views/layouts/public.blade.php` |
| Tambah email | `app/Mail/`, `app/Listeners/`, `resources/views/mail/` |
| Ubah config bisnis | `config/booking.php`, `config/loyalty.php`, `.env` |

---

## Dokumen Legacy (Tidak Digunakan untuk Implementasi)

| File | Keterangan |
|---|---|
| `implementation_plan.md` | Audit UI/UX — gunakan untuk referensi perbaikan visual |
| `implementation_plan_v3_final.md` | Rencana fitur fase 0-11 — **sebagian informasi sudah usang** (mis. klaim "Events/Listeners BELUM ADA" sudah tidak berlaku — sudah diimplementasikan) |
| `PROJECT_AUDIT.md` | Audit awal Juli 2026 — project sudah jauh berkembang |
| `SPEC_01_PLAN.md` | Rencana SPEC 01 — sudah diimplementasikan |
