# SPEC 02 — Room Management & Public Website: Requirements

> **Referensi master:** Fase 2 (Task 2.1–2.3) + Fase 3 (Task 3.1–3.3)  
> **Dependency:** SPEC 01 selesai (foundation, auth, enum, layouts)  
> **Scope:** Inventory kamar, admin CRUD, website publik informasional

---

## REQ-1: Room Inventory Schema

### REQ-1.1
**Sebagai** sistem,  
**saya ingin** data tipe kamar, kamar fisik, fasilitas, dan foto tersimpan dengan relasi yang benar,  
**sehingga** inventory dapat dikelola dan ditampilkan publik secara akurat.

**Acceptance Criteria:**
- Tabel `room_types` menyimpan: nama, slug (unique), deskripsi, kapasitas, bed count/type, base_price (integer Rupiah), is_active, sort_order.
- Tabel `rooms` menyimpan: room_type_id (FK), code (unique), name (unique), floor, notes, status (enum RoomStatus), is_active, sort_order.
- Tabel `facilities` menyimpan: nama, slug (unique), icon, deskripsi, is_active, sort_order.
- Tabel `room_type_facility` (pivot) menghubungkan room_type ke facilities.
- Tabel `room_images` menyimpan: room_type_id (FK), path, alt_text, is_cover, sort_order.
- Foreign keys: room → room_type RESTRICT, room_images → room_type CASCADE, pivot → RESTRICT/CASCADE sesuai master.
- Harga disimpan sebagai BIGINT UNSIGNED (integer Rupiah).

### REQ-1.2
**Sebagai** sistem,  
**saya ingin** kamar yang sudah memiliki booking tidak dapat dihapus secara hard-delete,  
**sehingga** integritas data histori terjaga.

**Acceptance Criteria:**
- Room dengan booking existing tidak dapat di-delete (RESTRICT atau application-level check).
- Room type dengan rooms existing tidak dapat di-delete.
- Solusi: nonaktifkan (is_active = false) sebagai alternatif.

---

## REQ-2: Admin CRUD Tipe Kamar

### REQ-2.1
**Sebagai** admin,  
**saya ingin** membuat, mengedit, dan mengelola tipe kamar,  
**sehingga** produk kamar tersedia untuk dijual.

**Acceptance Criteria:**
- Admin dapat create room type: nama, slug (auto-generate dari nama, editable), short description, description, kapasitas (≥1), bed count (≥1), bed type, base price (≥0), is_active, sort_order.
- Admin dapat edit seluruh field room type.
- Admin dapat mengaktifkan/menonaktifkan room type.
- Room type inactive tidak tampil di publik.
- Perubahan harga tidak mengubah booking historis (snapshot di booking).

### REQ-2.2
**Sebagai** admin,  
**saya ingin** mengelola fasilitas per tipe kamar,  
**sehingga** pengunjung melihat informasi fasilitas yang akurat.

**Acceptance Criteria:**
- Admin dapat assign multiple facilities ke room type saat create/edit.
- Admin dapat CRUD fasilitas master (nama, icon, deskripsi).
- Fasilitas inactive tidak ditampilkan publik.
- Fasilitas yang di-RESTRICT tidak dapat dihapus jika masih dipakai pivot.

### REQ-2.3
**Sebagai** admin,  
**saya ingin** mengunggah dan mengelola foto tipe kamar,  
**sehingga** pengunjung melihat visual kamar yang menarik.

**Acceptance Criteria:**
- Admin dapat upload multiple images per room type.
- Validasi: JPEG/PNG/WebP, ukuran maks 2MB per file.
- File disimpan dengan nama acak, bukan original filename.
- Admin dapat set satu foto sebagai cover.
- Admin dapat reorder foto (sort_order).
- Admin dapat hapus foto (file dan record dihapus setelah DB commit berhasil).
- Alt text opsional untuk accessibility/SEO.

---

## REQ-3: Admin CRUD Kamar Fisik

### REQ-3.1
**Sebagai** admin,  
**saya ingin** membuat dan mengelola kamar fisik,  
**sehingga** unit inventory nyata tersedia untuk booking.

**Acceptance Criteria:**
- Admin dapat create room: pilih room_type, code (unique), name (unique), floor (optional), notes (optional), status (enum), is_active, sort_order.
- Admin dapat edit seluruh field room.
- Admin dapat mengaktifkan/menonaktifkan room.
- Room inactive tidak dapat dipesan.
- Room dengan booking existing tidak dapat di-hard-delete.

### REQ-3.2
**Sebagai** sistem,  
**saya ingin** data awal Twin / Twin 01 / Twin 02 tersedia,  
**sehingga** inventory minimal siap digunakan.

**Acceptance Criteria:**
- Seeder membuat: room type "Twin" (is_active = false/placeholder karena harga belum pasti).
- Seeder membuat: room "Twin 01" dan "Twin 02" yang terhubung ke room type Twin.
- Seeder idempotent (aman dijalankan ulang tanpa duplikasi).
- Tidak mengarang harga, kapasitas, atau fasilitas yang belum dikonfirmasi.

---

## REQ-4: Settings & Public Content

### REQ-4.1
**Sebagai** admin,  
**saya ingin** mengelola informasi bisnis publik melalui settings,  
**sehingga** perubahan informasi tidak memerlukan edit source code.

**Acceptance Criteria:**
- Tabel `settings` tersedia dengan group, key, value, type, is_public.
- Admin dapat edit settings melalui UI grouped (general, contact, booking, whatsapp, seo).
- Perubahan settings penting diaudit (updated_by_admin_id).
- Secret tidak dapat disimpan melalui UI settings.
- Cache setting dengan invalidasi saat update.

### REQ-4.2
**Sebagai** admin,  
**saya ingin** mengelola kebijakan penginapan dengan versioning,  
**sehingga** kebijakan lama yang sudah disetujui booking tidak berubah.

**Acceptance Criteria:**
- Tabel `policy_versions` tersedia dengan policy_key, version, title, content, is_current.
- Admin dapat create versi baru dan publish sebagai current.
- Versi lama yang sudah dipakai booking tidak dapat diedit.
- Hanya satu current per policy_key.
- Halaman publik kebijakan menampilkan current version.

---

## REQ-5: Website Publik

### REQ-5.1
**Sebagai** pengunjung,  
**saya ingin** melihat beranda penginapan yang informatif,  
**sehingga** saya mendapat gambaran tentang penginapan.

**Acceptance Criteria:**
- Beranda menampilkan: nama penginapan, deskripsi singkat, harga mulai dari (termurah room type aktif), lokasi, CTA booking, CTA WhatsApp.
- Preview tipe kamar aktif (cover photo, nama, kapasitas, harga).
- Form availability (check-in, check-out, jumlah tamu) — mengarah ke route search yang akan aktif di SPEC 03.
- Jika tidak ada room type aktif, tampilkan pesan informatif (bukan error).

### REQ-5.2
**Sebagai** pengunjung,  
**saya ingin** melihat daftar dan detail tipe kamar,  
**sehingga** saya dapat memilih kamar yang sesuai.

**Acceptance Criteria:**
- Halaman daftar kamar: hanya room type active, sorted by sort_order.
- Per room type: cover photo, nama, kapasitas, bed info, fasilitas, harga, link detail.
- Halaman detail kamar (by slug): galeri foto, deskripsi lengkap, fasilitas, kapasitas, harga, CTA booking.
- Room type inactive menghasilkan 404.
- Jika foto belum ada, tampilkan placeholder visual.

### REQ-5.3
**Sebagai** pengunjung,  
**saya ingin** melihat halaman tentang, lokasi, dan kebijakan,  
**sehingga** saya memiliki informasi lengkap sebelum memesan.

**Acceptance Criteria:**
- Halaman Tentang: konten dari settings.
- Halaman Lokasi & Kontak: alamat, peta (link/embed dari settings), WhatsApp.
- Halaman Kebijakan: current policy version.
- Jika data belum diisi admin, tampilkan pesan "belum tersedia" yang graceful.
- WhatsApp link menggunakan format internasional dari settings.

### REQ-5.4
**Sebagai** pengunjung,  
**saya ingin** website memiliki SEO dasar yang benar,  
**sehingga** penginapan mudah ditemukan di mesin pencari.

**Acceptance Criteria:**
- Meta title per halaman.
- Meta description.
- Open Graph tags.
- Favicon.
- Alt text pada gambar.
- Heading hierarchy semantic.
- Halaman sensitif (booking guest) diberi noindex (untuk SPEC selanjutnya).

---

## REQ-6: Galeri Publik

### REQ-6.1
**Sebagai** admin,  
**saya ingin** mengelola galeri foto umum penginapan,  
**sehingga** pengunjung melihat suasana penginapan secara keseluruhan.

**Acceptance Criteria:**
- Tabel `galleries` tersedia.
- Admin dapat upload, sort, activate/deactivate galeri.
- Validasi upload sama ketatnya dengan room images.
- Publik menampilkan galeri aktif yang tersorted.

---

## Constraints

- **C-1:** Harga integer Rupiah, tidak ada floating point.
- **C-2:** Tidak ada data bisnis yang dikarang (fasilitas, harga, kapasitas yang belum dikonfirmasi).
- **C-3:** Upload harus aman: random filename, validasi MIME, batas ukuran, tidak executable.
- **C-4:** Admin-only untuk seluruh operasi CRUD.
- **C-5:** Perubahan harga room type tidak mempengaruhi booking historis.
- **C-6:** Halaman publik mobile-first responsive.
