# Business Rules — Penginapan Kelapa Sawit

## Booking

- Guest tidak wajib login untuk membuat booking.
- Satu booking = satu kamar fisik untuk satu interval menginap.
- Interval menginap menggunakan `[check_in, check_out)` — half-open interval.
- Overlap terjadi jika: `existing.check_in < new.check_out AND existing.check_out > new.check_in`.
- Booking blocking: status `confirmed`, `checked_in`, dan `pending_payment` yang belum expired.
- Room block juga memblokir kamar dengan rumus overlap yang sama.
- Harga disimpan sebagai snapshot pada booking; perubahan harga tipe kamar tidak mengubah booking lama.
- Promo dan poin tidak dapat digabung pada satu booking (V1).
- `total_amount = max(0, subtotal - promotion_discount - points_discount)`.
- Frontend tidak pernah menjadi sumber kebenaran harga atau total.

## Hold & Expiry

- Booking website dimulai dengan status `pending_payment`.
- `payment_expires_at = waktu server + 30 menit`.
- Scheduler mengexpire booking yang melewati batas waktu dan belum paid.
- Countdown di frontend hanya display; kebenaran waktu ada di server.

## Payment

- Status booking dan status payment adalah dua hal berbeda.
- Webhook Midtrans terverifikasi = sumber kebenaran pembayaran.
- Callback JavaScript BUKAN bukti pembayaran.
- Pembayaran terlambat setelah booking expired: simpan payment paid, set `needs_attention = true`, admin memutuskan resolusi.
- `gross_amount` payment harus sama dengan `total_amount` booking.

## Loyalty

- Poin diberikan hanya setelah booking berstatus `completed`.
- Earn: `floor(eligible_amount / 1000)` = jumlah poin.
- Eligible amount: total yang benar-benar dibayar setelah diskon, tidak termasuk potongan poin.
- Sumber eligible default: website, whatsapp, walk_in. OTA tidak eligible V1.
- Redeem: minimum 100 poin, 1 poin = Rp50, maksimum 20% dari subtotal.
- Poin expired setelah 18 bulan dari tanggal earn.
- Redemption menggunakan FIFO (expiry terdekat duluan).
- Pembatalan/reversal: buat transaksi reversal, jangan hapus histori.
- Ledger loyalty adalah sumber kebenaran saldo, bukan cache.
- Idempotency key wajib untuk setiap mutasi loyalty.

## Promotion

- Validasi promo sepenuhnya backend (kode, waktu, minimum, quota, per-user limit).
- Quota dilindungi transaction + row lock.
- Status usage: reserved → consumed (saat paid) atau released (saat expired/cancel).
- Jangan percaya nominal diskon dari frontend.

## Claim Guest Booking

- Klaim berdasarkan email terverifikasi yang cocok dengan email snapshot booking.
- Klaim TIDAK boleh berdasarkan nama atau nomor WhatsApp saja.
- Token claim sekali pakai, disimpan sebagai hash, ada expiry.
- Admin dapat melakukan manual claim dengan audit.

## Status Transition

- Booking status: pending_payment → confirmed → checked_in → checked_out → completed.
- Terminal: completed, cancelled, expired, no_show.
- Jangan membuat endpoint `update status` generik. Gunakan aksi spesifik.
- Setiap transisi harus dalam transaction yang sama dengan penulisan status history.

## Invoice

- Invoice menggunakan data snapshot booking, bukan harga terbaru room type.
- Invoice number unique, format `INV-YYYYMM-0001`.
- Authorization: member hanya miliknya, guest perlu token/verifikasi, admin boleh semua.
