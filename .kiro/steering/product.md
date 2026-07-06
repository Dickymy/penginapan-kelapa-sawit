# Product Context — Penginapan Kelapa Sawit

## Produk

- **Nama:** Penginapan Kelapa Sawit
- **Lokasi:** Kota Bangun, Kalimantan Timur, Indonesia
- **Timezone bisnis:** `Asia/Makassar` (WITA, UTC+8)
- **Mata uang:** IDR (Rupiah Indonesia)
- **Bahasa aplikasi:** Bahasa Indonesia

## Jenis Sistem

Website Publik + Booking Engine + Payment (Midtrans Snap) + Member Area + Loyalty Point + Admin Management System.

## Pengguna

| Peran | Deskripsi |
|---|---|
| Guest (Pengunjung) | Melihat info, mencari ketersediaan, booking tanpa login, bayar |
| Member | Guest + profil tersimpan, histori, poin loyalitas, claim booking |
| Admin | Mengelola kamar, reservasi, kalender, pembayaran, promo, poin, laporan |

## Prinsip Utama

1. Guest tidak wajib login untuk memesan kamar.
2. Satu booking = satu kamar fisik untuk satu interval menginap.
3. Member mendapat manfaat nyata: profil, histori, poin, invoice, claim.
4. Admin memiliki area dan guard terpisah dari member.
5. Integritas data dan pencegahan double booking adalah prioritas tertinggi.
6. Frontend tidak pernah menjadi sumber kebenaran untuk harga, status pembayaran, atau availability.

## Value Utama Member

- Profil dan data tersimpan (autofill checkout)
- Histori booking dan invoice
- Loyalty point (earn saat completed, redeem saat booking)
- Claim guest booking yang sudah dibuat sebelum register

## Data Bisnis

Data yang belum dikonfirmasi (harga, fasilitas, alamat lengkap, kebijakan, jam operasional) menggunakan placeholder/admin setting. Tidak boleh dikarang.

Data yang diketahui pasti:
- Tipe kamar: Twin
- Kamar fisik: Twin 01, Twin 02
