# Critical Safety Rules — Penginapan Kelapa Sawit

## Jangan Pernah

1. **Percaya harga dari frontend.** Harga dikalkulasi backend sepenuhnya.
2. **Percaya payment status dari frontend.** Status dari webhook terverifikasi atau server-to-server check.
3. **Percaya callback JavaScript Midtrans sebagai bukti pembayaran.** Hanya webhook yang authoritative.
4. **Hardcode secret** (Midtrans key, Google secret, DB password, APP_KEY) di source code.
5. **Jalankan migration destructive** (`migrate:fresh`, `migrate:rollback` production) tanpa instruksi eksplisit.
6. **Izinkan double booking.** Gunakan transaction + pessimistic lock pada row kamar.
7. **Buat booking tanpa authoritative overlap recheck** di dalam transaction.
8. **Award poin dua kali** untuk booking yang sama. Gunakan idempotency key.
9. **Hapus histori ledger loyalty.** Gunakan reversal transaction.
10. **Claim guest booking berdasarkan nama saja.** Wajib verifikasi email/token.
11. **Buka data booking dengan identifier yang mudah ditebak** (sequential ID). Gunakan token/code.
12. **Ubah histori booking lama mengikuti harga baru.** Booking menggunakan snapshot.
13. **Hapus booking, payment, atau loyalty transaction.** Data finansial tidak boleh hard-delete.

## Transaction & Locking

- Operasi kritis (create booking, payment confirmation, loyalty mutasi, promo reserve) WAJIB menggunakan database transaction.
- Lock row kamar target dengan `lockForUpdate` sebelum overlap check saat create booking.
- Lock user/lot saat mutasi loyalty.
- Lock promotion row saat reserve quota.
- Jangan menahan DB lock sambil menunggu external API call (Midtrans).

## Webhook

- Webhook HARUS verified (signature check sesuai dokumentasi resmi).
- Webhook HARUS idempotent (duplicate notification menghasilkan response sukses tanpa side effect ganda).
- Webhook HARUS amount-checked (nominal payload cocok dengan booking total).
- Webhook tidak memerlukan session/CSRF — punya mekanisme verifikasi sendiri.
- Jangan tolak duplicate webhook dengan HTTP 500; return 2xx.
- Jangan log Server Key atau data kartu sensitif.

## Double Booking Protection (5 Lapisan)

1. **Search** — filter kamar tersedia.
2. **Checkout** — recheck sebelum ringkasan.
3. **Create Booking** — transaction + lock + overlap recheck (authoritative).
4. **Idempotency** — key unik mencegah submit ganda.
5. **Unique Constraint** — booking code, provider order ID sebagai last defense.

## Secret Management

- APP_KEY, DB credentials, Midtrans keys, Google secret, mail credentials → `.env` only.
- Tidak boleh commit `.env`.
- Tidak boleh simpan secret di tabel `settings`.
- Tidak boleh kirim Server Key ke browser.
- Tidak boleh log secret, raw token, atau password.

## Kiro Workflow Safety

- Jangan otomatis jalankan `migrate:fresh`.
- Jangan otomatis hapus file.
- Jangan otomatis push Git atau deploy.
- Jangan otomatis refund tanpa persetujuan admin.
- Jangan membuat Hook yang mengeksekusi atau mencetak secret.
