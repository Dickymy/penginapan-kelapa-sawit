# MASTER REQUIREMENTS & KIRO IMPLEMENTATION ROADMAP — PENGINAPAN KELAPA SAWIT

> **Nama file:** `MASTER_REQUIREMENTS_KIRO_PENGINAPAN_KELAPA_SAWIT.md`  
> **Target eksekutor:** Kiro AI / Kiro IDE  
> **Bahasa aplikasi:** Bahasa Indonesia  
> **Lokasi bisnis:** Kota Bangun, Kalimantan Timur, Indonesia  
> **Zona waktu bisnis:** `Asia/Makassar`  
> **Mata uang:** `IDR` / Rupiah Indonesia  
> **Jenis sistem:** Website Publik + Booking Engine + Midtrans + Member + Loyalty Point + Sistem Manajemen Penginapan
> **Mode kerja:** Spec-Driven Development melalui Kiro Steering + Feature Specs + task-by-task verification  
> **Peran dokumen:** Source of truth kebutuhan lengkap; bukan prompt sekali jalan  

---

# 0. PERINTAH UTAMA UNTUK KIRO

Anda bertindak sebagai **Senior Software Architect, Senior Laravel Developer, Database Designer, Payment Integration Engineer, QA Engineer, Security-Minded Maintainer, dan Kiro Spec-Driven Engineer** untuk membangun sistem produksi bernama **Penginapan Kelapa Sawit**.

Dokumen ini adalah **master requirements, architectural source of truth, dan implementation roadmap**. Jangan memperlakukannya sebagai daftar ide atau sebagai satu prompt sekali jalan. Kiro wajib menggunakan dokumen ini sebagai sumber kebutuhan utama, lalu menjalankan pekerjaan melalui kombinasi **Project Audit → Steering → Feature Spec → Task-by-Task Implementation → Verification**.

## 0.1 Tujuan Utama

Bangun sistem yang memungkinkan:

1. pengunjung melihat profil dan informasi penginapan;
2. pengunjung mencari kamar fisik yang tersedia berdasarkan tanggal;
3. pengunjung memesan **tanpa wajib login**;
4. booking sementara mengunci kamar secara aman selama masa pembayaran;
5. tamu membayar melalui **Midtrans Snap**;
6. webhook Midtrans terverifikasi menjadi sumber kebenaran pembayaran;
7. member login dengan email/password atau Google;
8. member mendapat manfaat nyata berupa profil tersimpan, histori booking, invoice, loyalty point, dan klaim guest booking yang aman;
9. admin mengelola kamar, reservasi, kalender, pembayaran, check-in, check-out, promo, poin, room block, laporan, pengeluaran, galeri, kebijakan, dan pengaturan;
10. sistem mencegah double booking, double payment processing, double point award, dan klaim booking tanpa verifikasi.

## 0.2 Aturan Paling Penting

Urutan prioritas ketika terjadi konflik keputusan:

1. **Integritas data dan keamanan.**
2. **Pencegahan double booking dan kehilangan uang.**
3. **Kebenaran status pembayaran.**
4. **Keterlacakan audit dan histori.**
5. **Kebutuhan bisnis dalam dokumen ini.**
6. **Kesederhanaan dan kemudahan pemeliharaan.**
7. **Keindahan UI.**

Jangan mengorbankan integritas data hanya agar alur terlihat lebih sederhana di frontend.

## 0.3 Cara Menjalankan Dokumen Ini

Jangan membangun seluruh sistem dalam satu perubahan besar.

Untuk setiap fase:

1. baca seluruh bagian fase;
2. baca file project yang relevan;
3. tulis ringkasan kondisi awal;
4. identifikasi konflik dengan kode yang sudah ada;
5. buat rencana perubahan kecil;
6. implementasikan satu task;
7. jalankan test dan pemeriksaan;
8. perbaiki kegagalan;
9. baru lanjut ke task berikutnya;
10. jangan melanjutkan ke fase berikutnya sebelum checklist fase selesai.

Setiap task dalam dokumen ini menggunakan format:

- Tujuan
- Kondisi Sebelum Perubahan
- File yang Harus Dibaca
- File yang Dibuat
- File yang Diubah
- Database yang Terlibat
- Detail Implementasi
- Business Rules
- Validation Rules
- Security Considerations
- Edge Cases
- Testing
- Acceptance Criteria
- Checklist

## 0.4 Kondisi Project

### Jika Project Masih Kosong

Bangun fondasi dari awal menggunakan Laravel yang kompatibel dengan environment. Pada tanggal penyusunan dokumen ini, dokumentasi resmi Laravel 13 menyatakan kebutuhan minimum PHP 8.3. Karena environment pengguna dapat berbeda, lakukan pemeriksaan nyata sebelum membuat project.

Urutan:

1. cek versi PHP;
2. cek Composer;
3. cek Node.js dan npm;
4. cek MySQL;
5. pilih Laravel 13 jika seluruh requirement kompatibel;
6. jika tidak kompatibel, gunakan versi Laravel terbaru yang **resmi mendukung environment** dan catat alasannya;
7. jangan menurunkan versi keamanan hanya untuk memaksa dependency lama.

### Jika Project Sudah Berisi Kode

Wajib:

1. baca `composer.json`;
2. baca `package.json`;
3. baca `.env.example`;
4. baca struktur `app/`;
5. baca seluruh migration;
6. baca model;
7. baca route;
8. baca middleware;
9. baca controller;
10. baca service/action class;
11. baca test;
12. baca layout dan component frontend;
13. cari implementasi auth yang sudah ada;
14. cari konvensi enum/status yang sudah ada;
15. cari integrasi pembayaran yang mungkin sudah ada;
16. jangan menduplikasi solusi yang sudah benar;
17. jangan menghapus file yang masih direferensikan route, service container, view, test, config, atau scheduler.

Buat catatan awal bernama misalnya `docs/PROJECT_AUDIT.md` jika project sudah ada dan dokumentasi internal diizinkan. Catatan tersebut bukan pengganti implementasi.


## 0.5 MODEL KERJA KHUSUS KIRO — WAJIB

Kiro memiliki **Steering**, **Specs**, **Hooks**, dan kemampuan agent/subagent. Gunakan fitur tersebut secara terkontrol. Jangan menganggap dokumen master ini sebagai pengganti semua fitur Kiro.

Urutan kerja baku:

```text
MASTER REQUIREMENTS
        ↓
PROJECT AUDIT
        ↓
KIRO STEERING
        ↓
FEATURE SPEC TERBATAS
        ↓
requirements.md
        ↓
design.md
        ↓
tasks.md
        ↓
IMPLEMENTASI SATU TASK
        ↓
TEST + REVIEW + UPDATE PROGRESS
        ↓
TASK BERIKUTNYA
```

Aturan wajib:

1. Dokumen master ini tetap menjadi sumber kebenaran kebutuhan bisnis paling lengkap.
2. Steering hanya menyimpan konteks yang perlu selalu diketahui Kiro; jangan menyalin seluruh dokumen master ke Steering.
3. Fitur kompleks harus dikerjakan melalui **Feature Spec**, bukan langsung dibuat dari satu prompt besar.
4. Jangan membuat satu Spec raksasa untuk seluruh website.
5. Satu Spec harus memiliki scope yang jelas dan dependency yang masuk akal.
6. Sebelum implementasi sebuah Spec, Kiro wajib memastikan requirement, design, dan task plan konsisten dengan project nyata.
7. Implementasikan satu task kecil pada satu waktu.
8. Jangan otomatis melanjutkan ke task atau Spec berikutnya hanya karena source code selesai ditulis.
9. Jalankan test dan checklist task sebelum melanjutkan.
10. Jika kebutuhan berubah, perbarui artifact Spec terkait terlebih dahulu agar dokumentasi dan implementasi tetap sinkron.
11. Jika project existing memiliki pola yang sudah benar, sesuaikan Spec dengan pola tersebut; jangan memaksakan struktur dokumen ini secara buta.
12. Gunakan sesi non-Spec/Vibe hanya untuk audit kecil, eksplorasi, pertanyaan, atau perubahan sangat terfokus. Untuk fitur kompleks, gunakan Spec.
13. Untuk bug kompleks yang melibatkan root cause dan regression risk, gunakan workflow bug-fix yang terstruktur bila tersedia.
14. Jangan menggunakan kemampuan paralel untuk membuat perubahan bersamaan pada area kritis yang sama.

### Sumber Kebenaran antara Master, Steering, Spec, dan Kode

| Artefak | Fungsi | Boleh Berisi |
|---|---|---|
| Master Requirements ini | Kebutuhan bisnis dan teknis lengkap | Seluruh aturan sistem, roadmap, acceptance criteria |
| `.kiro/steering/` | Pengetahuan persistent yang selalu relevan | Product, tech, structure, business rules, critical safety rules |
| Feature Spec | Scope fitur yang sedang dikerjakan | Requirements, design, tasks, traceability |
| Source code | Implementasi nyata | Hanya hasil yang konsisten dengan requirement dan test |
| Test | Bukti perilaku | Business rule dan regression protection |

Jika terjadi konflik:

1. keamanan dan integritas data;
2. requirement bisnis terbaru yang telah disetujui;
3. keputusan arsitektur yang tercatat;
4. Spec aktif;
5. implementasi existing.

Kiro tidak boleh diam-diam mengubah aturan bisnis hanya agar kode lebih mudah dibuat.


## 0.6 STRATEGI KIRO STEERING

Gunakan folder workspace:

```text
.kiro/
└── steering/
    ├── product.md
    ├── tech.md
    ├── structure.md
    ├── business-rules.md
    ├── critical-safety-rules.md
    └── workflow.md
```

### `product.md`

Berisi konteks stabil:

- nama produk: Penginapan Kelapa Sawit;
- lokasi bisnis: Kota Bangun, Kalimantan Timur;
- timezone bisnis: `Asia/Makassar`;
- mata uang: IDR;
- jenis pengguna: guest, member, admin;
- tujuan sistem;
- value utama member;
- prinsip guest booking tanpa login.

Jangan memasukkan detail migration atau daftar file implementasi ke `product.md`.

### `tech.md`

Berisi:

- versi Laravel/PHP aktual setelah audit;
- MySQL;
- Blade;
- Tailwind CSS;
- Alpine.js;
- auth strategy;
- Midtrans Sandbox-first;
- Google OAuth melalui Socialite;
- PDF strategy;
- timezone strategy;
- testing strategy;
- aturan dependency minimal.

Jangan mencatat versi yang hanya diasumsikan. Gunakan versi hasil audit project.

### `structure.md`

Berisi:

- struktur folder aktual;
- naming convention;
- pola controller tipis;
- lokasi service;
- enum/status convention;
- Form Request;
- Policy/Gate;
- test structure;
- view/component convention;
- aturan perubahan project existing.

### `business-rules.md`

Minimal memuat invariant yang perlu selalu diingat:

- guest tidak wajib login;
- satu booking V1 mewakili satu kamar fisik;
- interval menginap `[check_in, check_out)`;
- overlap menggunakan `existing.check_in < new.check_out` dan `existing.check_out > new.check_in`;
- pending payment aktif, confirmed, dan checked-in memblokir kamar;
- room block memblokir kamar;
- harga lama menggunakan snapshot;
- promo dan poin tidak dapat digabung pada V1;
- poin hanya diberikan setelah booking completed;
- OTA tidak eligible poin pada V1 kecuali konfigurasi diubah;
- loyalty ledger adalah sumber kebenaran;
- booking dan payment status terpisah.

### `critical-safety-rules.md`

Minimal memuat:

- jangan percaya harga dari frontend;
- jangan percaya payment status dari frontend;
- jangan percaya callback JavaScript sebagai bukti pembayaran;
- jangan hardcode secret;
- jangan menjalankan migration destructive tanpa instruksi eksplisit;
- jangan izinkan double booking;
- jangan membuat booking tanpa authoritative overlap recheck;
- gunakan transaction dan locking untuk operasi kritis;
- webhook harus verified, idempotent, dan amount-checked;
- jangan award poin dua kali;
- jangan hapus histori ledger;
- jangan claim guest booking berdasarkan nama;
- jangan membuka data booking dengan identifier yang mudah ditebak;
- jangan mengubah histori booking lama mengikuti harga baru.

### `workflow.md`

Berisi cara kerja Kiro untuk project ini:

1. baca master requirements;
2. audit file relevan;
3. identifikasi Spec aktif;
4. baca requirement/design/tasks Spec;
5. implementasikan satu task;
6. jalankan test;
7. update progress;
8. berhenti jika exit gate belum terpenuhi.

### Aturan Steering

- Steering harus ringkas dan tidak menjadi duplikasi 10.000+ baris dari dokumen master.
- Jangan menyalin secret ke Steering.
- Jangan menyalin data pribadi tamu ke Steering.
- Jika keputusan arsitektur berubah, perbarui Steering yang relevan.
- Jika perubahan hanya sementara atau eksperimen, jangan masukkan ke Steering.
- Foundation Steering harus mencerminkan project aktual setelah audit, bukan rencana imajiner.


## 0.7 PEMBAGIAN FEATURE SPEC KIRO

Dokumen ini tetap menggunakan fase dan task terperinci sebagai roadmap. Kiro harus mengeksekusinya melalui Feature Spec berikut.

| Kiro Spec | Scope Utama | Fase Dokumen |
|---|---|---|
| SPEC 01 — Project Foundation | Audit, environment, Steering, fondasi, auth dasar, enum, layout | Fase 0–1 |
| SPEC 02 — Room Management & Public Website | Tipe kamar, kamar fisik, fasilitas, foto, website publik | Fase 2–3 |
| SPEC 03 — Availability & Guest Booking Engine | Availability, overlap, locking, guest checkout, pending hold | Fase 4–5 |
| SPEC 04 — Midtrans Payment | Payment attempt, Snap, webhook, reconciliation, resume payment | Fase 6 |
| SPEC 05 — Admin Reservation & Member | Reservasi admin, kalender, room block, Google login, dashboard member, claim | Fase 7–8 |
| SPEC 06 — Loyalty & Promotion | Ledger poin, earn/redeem/expire/reversal, promo lifecycle | Fase 9–10 |
| SPEC 07 — Property Operations | Check-in, check-out, complete, cancel, refund, invoice, WhatsApp | Fase 11–12 |
| SPEC 08 — Reports, Security & Release | Pengeluaran, laporan, hardening, regression, production readiness | Fase 13–15 |

Aturan:

1. Jangan membuat seluruh delapan Spec sekaligus pada awal project.
2. Buat hanya Spec yang segera akan dikerjakan.
3. Sebelum membuat Spec baru, baca kondisi source code terbaru.
4. Setiap Spec harus mereferensikan bagian master requirements yang relevan.
5. Jangan menyalin seluruh master requirements ke satu Spec.
6. Requirements harus fokus pada user/business outcome.
7. Design harus fokus pada arsitektur, data flow, schema, concurrency, security, dan integration boundary.
8. Tasks harus kecil, berurutan, dapat diuji, dan memiliki dependency yang jelas.
9. Jangan memasukkan task implementasi fitur yang bergantung pada fondasi yang belum selesai.
10. Setelah Spec selesai, lakukan exit-gate review sebelum Spec berikutnya.

### Struktur Artifact Spec yang Diharapkan

Untuk setiap Spec, Kiro harus memiliki artifact yang secara konseptual mencakup:

```text
SPEC XX
├── requirements.md
├── design.md
└── tasks.md
```

Nama folder/file aktual mengikuti mekanisme Kiro yang tersedia. Jangan memaksa path manual jika Kiro mengelola artifact Spec melalui UI/workspace sendiri.

### Requirements

Requirements harus:

- menggunakan bahasa yang tidak ambigu;
- dapat ditelusuri ke acceptance criteria;
- menyebut actor;
- menyebut kondisi dan hasil;
- mencakup error/edge case kritis;
- tidak mencampur detail implementasi secara berlebihan.

### Design

Design harus menjelaskan:

- arsitektur komponen;
- model dan relasi;
- perubahan database;
- service boundary;
- state transition;
- transaction boundary;
- locking/idempotency bila relevan;
- authorization;
- error handling;
- observability;
- test strategy.

### Tasks

Setiap task harus:

- memiliki tujuan tunggal;
- menyebut dependency;
- menyebut file target secara indikatif setelah audit;
- menyebut test;
- dapat dihentikan dan diverifikasi sebelum task berikutnya;
- tidak menggabungkan banyak modul kritis sekaligus.


## 0.8 KEBIJAKAN KIRO HOOKS

Jangan membuat banyak Hook pada awal project.

Tahap awal:

- audit;
- Steering;
- Spec;
- implementasi manual terkontrol.

Hook baru dipertimbangkan setelah struktur project stabil.

Hook yang boleh dipertimbangkan:

- setelah task Spec selesai, ingatkan untuk menjalankan test terkait;
- setelah file PHP tertentu berubah, jalankan formatter/linter yang memang sudah digunakan project;
- setelah perubahan migration, ingatkan migration status dan test schema;
- setelah perubahan frontend, jalankan build check bila tidak terlalu berat.

Larangan Hook:

- jangan otomatis menjalankan `migrate:fresh`;
- jangan otomatis menghapus file;
- jangan otomatis melakukan deployment;
- jangan otomatis push Git;
- jangan otomatis melakukan refund;
- jangan otomatis mengubah production database;
- jangan menjalankan seluruh test suite pada setiap save jika menghambat workflow;
- jangan membuat Hook yang mengeksekusi secret atau mencetak credential.


## 0.9 KEBIJAKAN SUBAGENT DAN PEKERJAAN PARALEL

Kiro dapat menggunakan subagent untuk eksplorasi atau pekerjaan terpisah, tetapi area kritis harus dikendalikan.

Boleh diparalelkan secara hati-hati:

- membaca dokumentasi;
- inventarisasi file;
- audit accessibility;
- review UI;
- menulis test tambahan yang tidak menyentuh schema yang sama;
- dokumentasi.

Jangan diparalelkan pada resource yang sama tanpa pembagian yang eksplisit:

- migration inti;
- tabel booking;
- AvailabilityService;
- BookingService;
- room locking;
- Midtrans webhook;
- payment status mapping;
- loyalty ledger;
- promo quota;
- state transition booking.

Satu jalur perubahan harus memiliki ownership yang jelas agar tidak terjadi konflik patch, duplikasi logic, atau invariant yang berbeda.


## 0.10 CARA MEMULAI PROJECT INI DI KIRO

Pada penggunaan pertama:

1. letakkan file ini di root project;
2. buka project sebagai workspace Kiro;
3. jangan meminta “buat seluruh website”;
4. minta Kiro membaca file ini seluruhnya;
5. kerjakan hanya **Fase 0**;
6. audit project;
7. buat atau sesuaikan Steering;
8. siapkan scope **SPEC 01 — Project Foundation**;
9. jangan implementasikan Spec 01 sebelum audit, Steering, requirements, design, dan tasks telah direview;
10. berhenti setelah output tahap awal selesai.

Prompt pertama siap pakai tersedia pada bagian paling akhir dokumen ini.

---

# 1. SUMBER KEBENARAN DAN BATAS DESAIN

## 1.1 Sumber Kebenaran Data

Gunakan sumber kebenaran berikut:

| Domain | Sumber kebenaran |
|---|---|
| Ketersediaan kamar | Database booking blocking + room block + waktu server |
| Harga booking lama | Snapshot pada booking |
| Harga saat checkout baru | Pricing Service dari data aktif |
| Status pembayaran | Webhook Midtrans terverifikasi atau server-to-server status check |
| Status booking | State transition terkontrol di backend |
| Saldo loyalty | Ledger transaksi loyalty dan lot/alokasi terkait |
| Promo | Promotion Service dan data promo backend |
| Guest access | Token acak aman atau verifikasi identitas tambahan |
| Klaim guest booking | Email terverifikasi/signed claim token/admin audited linking |
| Waktu kadaluarsa | Waktu server, bukan timer browser |
| Invoice lama | Snapshot booking dan payment terkait |

## 1.2 Keputusan Desain V1

Agar sistem realistis dan tidak over-engineering:

1. **Satu booking mewakili satu kamar fisik.**
2. Jika satu tamu memesan dua kamar, buat dua booking terpisah. Pengelompokan booking multi-room dapat menjadi pengembangan berikutnya.
3. Satu kamar fisik memiliki satu `room_type`.
4. Satu booking dapat memiliki beberapa **payment attempt**, tetapi hanya satu status pembayaran efektif.
5. Satu booking dapat memiliki nol atau satu promo.
6. Satu booking dapat menggunakan poin atau promo, tidak keduanya pada V1.
7. Guest booking harus tetap valid tanpa `user_id`.
8. Admin menggunakan area autentikasi terpisah dari member.
9. Nominal uang disimpan sebagai **integer Rupiah**, bukan floating point.
10. Tanggal menginap menggunakan `DATE` dan interval `[check_in, check_out)`.
11. Timestamp operasional memakai waktu server yang konsisten; seluruh tampilan bisnis menggunakan zona `Asia/Makassar`.
12. Informasi kamar dan harga yang dibutuhkan histori disimpan sebagai snapshot pada booking.
13. Frontend tidak pernah menjadi sumber kebenaran untuk harga, payment status, point balance, atau availability final.

## 1.3 Keputusan yang Tidak Boleh Ditebak

Untuk data bisnis yang belum diketahui, jangan mengarang. Gunakan placeholder/admin setting untuk:

- alamat lengkap;
- titik koordinat peta;
- nomor WhatsApp resmi;
- email resmi;
- logo;
- foto;
- fasilitas aktual;
- harga aktual;
- jam check-in;
- jam check-out;
- kebijakan pembatalan;
- kebijakan syariah/aturan tamu;
- refund policy;
- kapasitas kamar yang benar;
- ketersediaan AC, Wi-Fi, parkir, sarapan, dan fasilitas lain.

Seeder hanya boleh memasukkan data yang diketahui pasti:

- tipe kamar `Twin`;
- dua kamar fisik `Twin 01` dan `Twin 02`;
- fasilitas yang belum dipastikan harus berupa placeholder non-published atau tidak dimasukkan sama sekali.

---

# 2. TEKNOLOGI DAN KEBIJAKAN DEPENDENCY

## 2.1 Backend

Target utama:

- Laravel 13 jika environment kompatibel;
- PHP minimum mengikuti versi Laravel yang dipakai;
- MySQL;
- Blade;
- Eloquent ORM;
- Form Request untuk validasi;
- Policy/Gate untuk authorization;
- Service class untuk business logic;
- database transaction untuk operasi kritis;
- Laravel Scheduler untuk expiry dan maintenance task;
- queue hanya untuk proses yang benar-benar cocok dijalankan async dan tidak menentukan hasil transaksi sinkron.

## 2.2 Frontend

Gunakan:

- Blade;
- Tailwind CSS;
- Alpine.js untuk interaksi ringan;
- Vite;
- komponen Blade reusable.

Jangan menggunakan React, Vue, Inertia, atau SPA kecuali project existing sudah menggunakan teknologi tersebut dan migrasi balik justru merusak sistem. Jika project kosong, tetap gunakan Blade + Tailwind + Alpine.

## 2.3 Authentication

Rekomendasi:

- member: guard `web`, tabel `users`;
- admin: guard `admin`, tabel `admins`;
- email/password member: gunakan mekanisme auth Laravel yang resmi dan sesuai versi;
- bila menggunakan Laravel Fortify untuk backend auth, buat UI Blade sendiri;
- Google OAuth: Laravel Socialite;
- email verification: mekanisme resmi Laravel;
- password reset: mekanisme resmi Laravel.

Jangan membuat autentikasi custom yang menyimpan password secara manual.

## 2.4 Integrasi Midtrans

Gunakan:

- Midtrans Snap;
- package resmi `midtrans/midtrans-php` jika kompatibel;
- backend memperoleh Snap token;
- frontend hanya membuka Snap menggunakan token;
- webhook server menjadi sumber kebenaran;
- server-to-server `GET Status` sebagai fallback/reconciliation.

Jangan menggunakan package Laravel lama yang tidak terpelihara hanya karena namanya mengandung “Laravel”.

## 2.5 PDF

Gunakan package PDF yang:

- kompatibel dengan versi Laravel/PHP;
- dapat merender Blade;
- stabil untuk invoice sederhana.

Jika project existing sudah menggunakan DomPDF secara benar, pertahankan. Jika kosong, pilih package yang kompatibel dan dokumentasikan keputusan. Jangan menambah beberapa engine PDF sekaligus.

## 2.6 Referensi Teknis Resmi yang Wajib Dicek Saat Implementasi

Periksa versi terbaru dokumentasi resmi sebelum implementasi dependency kritis:

- Laravel release: `https://laravel.com/docs/13.x/releases`
- Laravel installation: `https://laravel.com/docs/13.x/installation`
- Laravel Socialite: `https://laravel.com/docs/13.x/socialite`
- Laravel Fortify: `https://laravel.com/docs/13.x/fortify`
- Laravel password reset: `https://laravel.com/docs/13.x/passwords`
- Laravel pessimistic locking: `https://laravel.com/docs/13.x/queries#pessimistic-locking`
- Midtrans Snap guide: `https://docs.midtrans.com/docs/snap-snap-integration-guide`
- Midtrans webhook: `https://docs.midtrans.com/docs/https-notification-webhooks`
- Midtrans notification signature: `https://docs.midtrans.com/reference/handle-notifications`
- Midtrans notification best practices: `https://docs.midtrans.com/reference/best-practices-to-handle-notification`
- Midtrans status API: `https://docs.midtrans.com/docs/get-status-api-requests`
- Midtrans Sandbox testing: `https://docs.midtrans.com/docs/testing-payment-on-sandbox`
- Midtrans PHP SDK: `https://github.com/Midtrans/midtrans-php`
- Kiro Specs: `https://kiro.dev/docs/specs/`
- Kiro Feature Specs: `https://kiro.dev/docs/specs/feature-specs/`
- Kiro Steering: `https://kiro.dev/docs/steering/`
- Kiro Hooks: `https://kiro.dev/docs/hooks/`
- Kiro Subagents: `https://kiro.dev/docs/chat/subagents/`

Jangan menyalin contoh kode lama tanpa menyesuaikan API dan versi dependency yang benar-benar terpasang. Untuk workflow Kiro, cek dokumentasi Kiro terbaru sebelum mengubah strategi Specs, Steering, Hooks, atau agent execution.

---

# 3. PERAN PENGGUNA DAN OTORISASI

## 3.1 Pengunjung/Guest

Boleh:

- melihat website publik;
- mencari availability;
- melihat detail kamar;
- melakukan guest booking;
- membuka pembayaran;
- melanjutkan pembayaran booking yang masih valid;
- mengecek booking dengan akses aman;
- mengunduh invoice jika verifikasi akses berhasil dan booking memenuhi syarat;
- menghubungi WhatsApp.

Tidak boleh:

- melihat booking guest lain;
- menebak booking melalui ID database;
- mengubah nominal;
- memaksa room ID yang tidak tersedia;
- mengubah status payment;
- mengklaim booking hanya berdasarkan nama.

## 3.2 Member

Mendapat seluruh hak guest serta:

- profil tersimpan;
- autofill checkout;
- booking aktif;
- riwayat;
- invoice;
- saldo dan histori poin;
- redeem poin;
- claim guest booking yang valid.

Member hanya boleh melihat data miliknya sendiri.

## 3.3 Admin

Admin memiliki area terpisah dan wajib authenticated.

Admin dapat:

- melihat dashboard operasional;
- melihat kalender;
- membuat booking manual;
- mengelola reservasi;
- mengelola kamar dan tipe kamar;
- mengelola foto dan fasilitas;
- mengelola promo;
- melihat dan mengoreksi loyalty melalui transaksi adjustment/reversal;
- check-in/check-out/complete;
- room block;
- mengelola galeri;
- mengelola kebijakan;
- mengelola expense;
- melihat laporan;
- mengubah setting yang diizinkan.

Aksi sensitif wajib diaudit:

- booking manual;
- perubahan harga manual;
- cancel booking;
- check-in dengan warning pembayaran;
- check-out;
- complete;
- refund;
- point adjustment;
- point reversal;
- manual claim;
- perubahan kebijakan;
- perubahan setting pembayaran;
- perubahan kamar pada booking.

## 3.4 Admin Guard

Gunakan tabel `admins` dan guard terpisah.

Jangan memakai boolean `is_admin` pada `users` sebagai satu-satunya pemisah jika project kosong. Pemisahan ini mengurangi risiko route member tidak sengaja memberikan akses admin.

---

# 4. GLOSARIUM DOMAIN

| Istilah | Definisi |
|---|---|
| Room Type / Tipe Kamar | Produk umum, contoh `Twin` |
| Room / Kamar Fisik | Unit yang benar-benar ditempati, contoh `Twin 01` |
| Availability | Apakah kamar fisik bebas dari booking blocking dan room block |
| Hold | Penguncian kamar sementara melalui booking `pending_payment` |
| Booking | Reservasi satu kamar fisik untuk satu interval menginap |
| Payment Attempt | Satu percobaan transaksi Midtrans atau pembayaran manual |
| Booking Source | Kanal asal reservasi |
| Member | User terautentikasi |
| Guest Booking | Booking dengan `user_id = NULL` |
| Claim | Menghubungkan guest booking ke member setelah verifikasi |
| Loyalty Ledger | Histori transaksi poin yang tidak dihapus |
| Loyalty Lot | Poin positif yang memiliki sisa dan tanggal expired |
| Redemption | Penggunaan poin menjadi potongan |
| Reversal | Transaksi pembalik, bukan penghapusan histori |
| Room Block | Interval kamar tidak dapat dijual |
| Policy Version | Versi kebijakan yang disetujui saat booking |
| Snapshot | Salinan data saat transaksi agar histori tidak berubah |

---

# 5. STRUKTUR APLIKASI YANG DIREKOMENDASIKAN

Sesuaikan nama namespace dengan project existing. Jangan memindahkan seluruh project hanya demi mengikuti struktur ini jika project sudah memiliki pola konsisten.

```text
app/
├── Enums/
│   ├── BookingStatus.php
│   ├── PaymentStatus.php
│   ├── BookingSource.php
│   ├── LoyaltyTransactionType.php
│   ├── PromotionType.php
│   ├── PromotionUsageStatus.php
│   ├── RefundStatus.php
│   └── RoomStatus.php
├── Http/
│   ├── Controllers/
│   │   ├── Public/
│   │   ├── Auth/
│   │   ├── Member/
│   │   ├── Admin/
│   │   └── Webhook/
│   ├── Requests/
│   │   ├── Booking/
│   │   ├── Member/
│   │   └── Admin/
│   └── Middleware/
├── Models/
├── Policies/
├── Services/
│   ├── AvailabilityService.php
│   ├── BookingService.php
│   ├── PricingService.php
│   ├── MidtransPaymentService.php
│   ├── LoyaltyPointService.php
│   ├── PromotionService.php
│   ├── InvoiceService.php
│   └── BookingClaimService.php
├── Actions/
│   └── (opsional untuk workflow kecil dan spesifik)
├── Jobs/
├── Console/
│   └── Commands/
└── Support/
    ├── Money/
    ├── Phone/
    └── Security/

config/
├── booking.php
├── loyalty.php
├── midtrans.php
└── services.php

resources/views/
├── public/
├── auth/
├── member/
├── admin/
├── invoices/
├── components/
└── layouts/

routes/
├── web.php
└── (pisahkan admin jika project convention mendukung)

tests/
├── Unit/
└── Feature/
```

## 5.1 Controller Harus Tipis

Controller bertugas:

1. menerima request;
2. memanggil Form Request;
3. melakukan authorization;
4. memanggil service;
5. mengubah hasil menjadi response/view/redirect.

Controller tidak boleh berisi:

- query overlap kompleks yang diduplikasi;
- kalkulasi harga;
- perhitungan loyalty;
- mapping status Midtrans;
- logika claim;
- generator invoice;
- transaksi booking panjang.

## 5.2 Service Boundary

Gunakan service terpisah berdasarkan domain. Jangan membuat `HotelService` atau `BookingManagerService` raksasa.

## 5.3 Enum

Semua status dan sumber booking terpusat.

Jangan menyebar string seperti `'confirmed'`, `'paid'`, `'website'` di puluhan file tanpa enum/constant.

---

# 6. STATUS, ENUM, DAN TRANSISI

## 6.1 BookingStatus

Nilai:

- `pending_payment`
- `confirmed`
- `checked_in`
- `checked_out`
- `completed`
- `cancelled`
- `expired`
- `no_show`

### Transisi Valid

| Dari | Ke | Kondisi |
|---|---|---|
| pending_payment | confirmed | payment terverifikasi paid sebelum expiry dan tidak ada conflict |
| pending_payment | expired | melewati `payment_expires_at` dan belum paid |
| pending_payment | cancelled | pembatalan valid |
| confirmed | checked_in | admin menjalankan check-in |
| confirmed | cancelled | pembatalan admin sesuai kebijakan |
| confirmed | no_show | tamu tidak datang dan admin mengonfirmasi |
| checked_in | checked_out | admin mencatat checkout aktual |
| checked_out | completed | proses selesai dan siap award point |
| completed | — | terminal |
| cancelled | — | terminal |
| expired | — | terminal |
| no_show | — | terminal |

Jangan membuat endpoint generik `update status` yang menerima status sembarang. Buat action spesifik:

- confirm dari payment service;
- expire dari scheduler/service;
- cancel;
- check-in;
- check-out;
- complete;
- no-show.

### Pembayaran Terlambat Setelah Booking Expired

Jika webhook `paid/settlement` datang setelah booking telah `expired`:

1. jangan otomatis mengubah expired menjadi confirmed;
2. lock booking dan kamar;
3. simpan payment sebagai `paid`;
4. set `needs_attention = true`;
5. isi `attention_reason = late_payment_after_booking_expired`;
6. buat audit/event log;
7. tampilkan alert admin;
8. admin harus memilih penyelesaian: kamar alternatif jika aman, manual remediation, atau proses refund.

Ini mencegah pembayaran terlambat menimpa booking lain yang sah.

## 6.2 PaymentStatus

Nilai normalisasi aplikasi:

- `unpaid`
- `pending`
- `paid`
- `failed`
- `expired`
- `refunded`
- `partial_refund`

Simpan juga status mentah provider pada payment agar debugging tidak kehilangan informasi.

### Prinsip

- `booking.status` dan `payment.status` bukan hal yang sama.
- Booking dapat `expired` tetapi payment terlambat menjadi `paid`; kondisi ini harus masuk review admin.
- Booking `confirmed` berarti pembayaran website telah terverifikasi atau booking manual/OTA secara eksplisit dikonfirmasi oleh admin sesuai sumber.

## 6.3 BookingSource

Nilai:

- `website`
- `whatsapp`
- `booking_com`
- `agoda`
- `traveloka`
- `walk_in`
- `phone`
- `other`

Gunakan enum dan label Bahasa Indonesia di presentation layer.

## 6.4 LoyaltyTransactionType

- `earn`
- `redeem`
- `expire`
- `adjustment`
- `reversal`

## 6.5 PromotionType

- `percentage`
- `fixed`

## 6.6 PromotionUsageStatus

- `reserved`
- `consumed`
- `released`

## 6.7 RefundStatus

- `requested`
- `processing`
- `succeeded`
- `failed`
- `cancelled`

---

# 7. RANCANGAN DATABASE LENGKAP

## 7.1 Prinsip Database

1. Gunakan InnoDB.
2. Gunakan foreign key nyata.
3. Gunakan index untuk query availability, reporting, dan lookup publik.
4. Uang disimpan sebagai `BIGINT UNSIGNED` dalam Rupiah.
5. Poin disimpan sebagai integer.
6. Jangan menggunakan `FLOAT` untuk uang.
7. Gunakan `DECIMAL` hanya jika provider benar-benar memerlukan pecahan; normalisasi kembali ke integer Rupiah untuk domain.
8. Public code harus unik dan bukan ID mentah.
9. Token rahasia disimpan dalam bentuk hash jika raw token tidak diperlukan setelah dikirim.
10. Data transaksi historis tidak dihapus melalui cascade sembarangan.
11. Tabel master boleh menggunakan soft delete hanya jika ada kebutuhan jelas; jangan menerapkan soft delete ke semua tabel secara otomatis.
12. Untuk booking/payment/loyalty/audit, utamakan histori dan status, bukan hard delete.

## 7.2 Aturan Foreign Key

- `CASCADE` hanya untuk data anak yang tidak memiliki arti tanpa parent dan bukan histori keuangan.
- `RESTRICT` untuk mencegah penghapusan master yang masih dipakai.
- `SET NULL` untuk referensi aktor/admin yang boleh hilang tanpa menghilangkan histori.
- booking, payment, loyalty, refund, audit tidak boleh hilang hanya karena user/admin dihapus.



# 7.3 DETAIL TABEL

## 7.3.1 `users`

Akun member. Guest booking tidak mewajibkan record pada tabel ini.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| name | VARCHAR(150) | Tidak | — | Nama member |
| email | VARCHAR(191) | Tidak | — | Disimpan normalized/lowercase untuk login |
| email_verified_at | TIMESTAMP | Ya | NULL | Waktu verifikasi email |
| whatsapp | VARCHAR(32) | Ya | NULL | Format ter-normalisasi |
| avatar_path | VARCHAR(255) | Ya | NULL | Foto lokal opsional |
| avatar_url | VARCHAR(500) | Ya | NULL | Avatar provider eksternal opsional |
| password | VARCHAR(255) | Ya | NULL | Boleh NULL untuk akun Google-only |
| remember_token | VARCHAR(100) | Ya | NULL | Mekanisme auth Laravel |
| loyalty_balance_cache | BIGINT | Tidak | 0 | Cache performa; bukan sumber kebenaran |
| is_active | BOOLEAN | Tidak | TRUE | Akun dapat dinonaktifkan tanpa menghapus histori |
| last_login_at | TIMESTAMP | Ya | NULL | Audit ringan |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(email)` setelah normalisasi.
- `INDEX(is_active)` bila admin sering memfilter akun.
- Validasi aplikasi memastikan `loyalty_balance_cache >= 0`.

### Foreign Key

- Tidak ada.

### Relasi / Penggunaan

`User hasMany Booking`; `User hasMany SocialAccount`; `User hasMany LoyaltyTransaction`.

### Catatan Implementasi

Jangan melakukan merge user otomatis hanya karena nomor WhatsApp sama. Perubahan email harus melalui proses keamanan dan verifikasi ulang.

## 7.3.2 `admins`

Akun admin terpisah dari member.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| name | VARCHAR(150) | Tidak | — | Nama admin |
| email | VARCHAR(191) | Tidak | — | Email login admin |
| password | VARCHAR(255) | Tidak | — | Hash password |
| role | VARCHAR(50) | Tidak | super_admin | Role sederhana |
| is_active | BOOLEAN | Tidak | TRUE | Menonaktifkan akses tanpa menghapus histori |
| last_login_at | TIMESTAMP | Ya | NULL | Waktu login terakhir |
| password_changed_at | TIMESTAMP | Ya | NULL | Mendukung force-change |
| remember_token | VARCHAR(100) | Ya | NULL | Auth Laravel |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(email)`.
- `INDEX(is_active, role)`.

### Foreign Key

- Tidak ada.

### Catatan Implementasi

Seeder development tidak boleh memiliki password production default. Gunakan environment lokal dan gagal aman pada production.

## 7.3.3 `social_accounts`

Memisahkan identitas OAuth dari tabel user agar linking akun aman dan tidak menduplikasi kolom provider.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| user_id | BIGINT UNSIGNED | Tidak | — | Pemilik akun |
| provider | VARCHAR(50) | Tidak | — | Contoh `google` |
| provider_user_id | VARCHAR(191) | Tidak | — | ID unik provider |
| provider_email | VARCHAR(191) | Ya | NULL | Snapshot email provider |
| provider_email_verified | BOOLEAN | Tidak | FALSE | Status verifikasi provider |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(provider, provider_user_id)`.
- `UNIQUE(user_id, provider)` pada V1.
- `INDEX(provider_email)` untuk troubleshooting/linking.

### Foreign Key

- `user_id -> users.id ON DELETE CASCADE`.

### Relasi / Penggunaan

`SocialAccount belongsTo User`.

### Catatan Implementasi

Jangan simpan access token/refresh token jika tidak diperlukan. Jika suatu hari diperlukan, enkripsi at-rest dan batasi scope.

## 7.3.4 `room_types`

Master tipe kamar, contoh `Twin`.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| name | VARCHAR(120) | Tidak | — | Nama tipe |
| slug | VARCHAR(150) | Tidak | — | URL publik |
| short_description | VARCHAR(255) | Ya | NULL | Ringkasan kartu |
| description | TEXT | Ya | NULL | Deskripsi detail |
| capacity | SMALLINT UNSIGNED | Tidak | 1 | Kapasitas maksimal |
| bed_count | SMALLINT UNSIGNED | Tidak | 1 | Jumlah tempat tidur |
| bed_type | VARCHAR(100) | Ya | NULL | Jenis tempat tidur |
| base_price | BIGINT UNSIGNED | Tidak | 0 | Harga dasar/malam dalam Rupiah |
| is_active | BOOLEAN | Tidak | TRUE | Boleh ditampilkan/dijual |
| sort_order | INT UNSIGNED | Tidak | 0 | Urutan |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(slug)`.
- `INDEX(is_active, sort_order)`.
- Validasi `capacity >= 1`, `bed_count >= 1`, `base_price >= 0`.

### Foreign Key

- Tidak ada.

### Relasi / Penggunaan

`RoomType hasMany Room`; `RoomType belongsToMany Facility`; `RoomType hasMany RoomImage`.

### Catatan Implementasi

Perubahan nama atau harga tipe kamar tidak boleh mengubah histori booking karena booking menyimpan snapshot.

## 7.3.5 `rooms`

Kamar fisik yang menjadi unit inventory sebenarnya.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| room_type_id | BIGINT UNSIGNED | Tidak | — | Tipe kamar |
| code | VARCHAR(50) | Tidak | — | Contoh TWIN-01 |
| name | VARCHAR(120) | Tidak | — | Contoh Twin 01 |
| floor | VARCHAR(50) | Ya | NULL | Opsional |
| notes | TEXT | Ya | NULL | Catatan internal |
| status | VARCHAR(30) | Tidak | active | Enum RoomStatus |
| is_active | BOOLEAN | Tidak | TRUE | Kontrol penjualan |
| sort_order | INT UNSIGNED | Tidak | 0 | Urutan kalender |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(code)`.
- `UNIQUE(name)` jika nama kamar harus unik pada properti.
- `INDEX(room_type_id, is_active, status)`.

### Foreign Key

- `room_type_id -> room_types.id ON DELETE RESTRICT`.

### Relasi / Penggunaan

`Room belongsTo RoomType`; `Room hasMany Booking`; `Room hasMany RoomBlock`.

### Catatan Implementasi

Jangan hard delete kamar yang sudah memiliki booking. Nonaktifkan sebagai gantinya.

## 7.3.6 `facilities`

Master fasilitas reusable.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| name | VARCHAR(120) | Tidak | — | Nama fasilitas |
| slug | VARCHAR(150) | Tidak | — | Identifier |
| icon | VARCHAR(100) | Ya | NULL | Nama ikon, bukan HTML bebas |
| description | VARCHAR(255) | Ya | NULL | Opsional |
| is_active | BOOLEAN | Tidak | TRUE | Status |
| sort_order | INT UNSIGNED | Tidak | 0 | Urutan |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(slug)`.
- `INDEX(is_active, sort_order)`.

### Foreign Key

- Tidak ada.

### Relasi / Penggunaan

`Facility belongsToMany RoomType`.

### Catatan Implementasi

Jangan seeder fasilitas yang belum dipastikan benar-benar tersedia.

## 7.3.7 `room_type_facility`

Pivot fasilitas per tipe kamar.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| room_type_id | BIGINT UNSIGNED | Tidak | — | FK |
| facility_id | BIGINT UNSIGNED | Tidak | — | FK |
| created_at / updated_at | TIMESTAMP | Tidak | — | Opsional mengikuti convention project |

### Index dan Constraint

- `UNIQUE(room_type_id, facility_id)`.

### Foreign Key

- `room_type_id -> room_types.id ON DELETE CASCADE`.
- `facility_id -> facilities.id ON DELETE RESTRICT`.

### Relasi / Penggunaan

`RoomType belongsToMany Facility`.

## 7.3.8 `room_images`

Foto tipe kamar dengan urutan dan cover.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| room_type_id | BIGINT UNSIGNED | Tidak | — | Tipe kamar |
| path | VARCHAR(255) | Tidak | — | Path storage |
| alt_text | VARCHAR(255) | Ya | NULL | SEO/accessibility |
| is_cover | BOOLEAN | Tidak | FALSE | Foto utama |
| sort_order | INT UNSIGNED | Tidak | 0 | Urutan |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `INDEX(room_type_id, sort_order)`.
- Service memastikan hanya satu cover efektif per room type.

### Foreign Key

- `room_type_id -> room_types.id ON DELETE CASCADE`.

### Relasi / Penggunaan

`RoomImage belongsTo RoomType`.

### Catatan Implementasi

Validasi MIME, ukuran, ekstensi; nama file acak. Hapus file orphan secara aman setelah transaksi database berhasil.

## 7.3.9 `bookings`

Tabel inti reservasi. Satu row mewakili satu kamar fisik untuk satu interval menginap.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key internal |
| booking_code | VARCHAR(40) | Tidak | — | Kode publik, contoh BKG-202607-0001 |
| invoice_number | VARCHAR(40) | Ya | NULL | Nomor invoice |
| idempotency_key | VARCHAR(100) | Ya | NULL | Mencegah submit ganda |
| user_id | BIGINT UNSIGNED | Ya | NULL | NULL untuk guest |
| room_id | BIGINT UNSIGNED | Tidak | — | Kamar fisik |
| created_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Booking manual |
| source | VARCHAR(30) | Tidak | website | BookingSource |
| status | VARCHAR(30) | Tidak | pending_payment | BookingStatus |
| payment_status | VARCHAR(30) | Tidak | unpaid | Status pembayaran efektif |
| check_in | DATE | Tidak | — | Tanggal masuk |
| check_out | DATE | Tidak | — | Tanggal keluar; exclusive end |
| nights | SMALLINT UNSIGNED | Tidak | — | Snapshot jumlah malam |
| guest_count | SMALLINT UNSIGNED | Tidak | 1 | Jumlah tamu |
| guest_name | VARCHAR(150) | Tidak | — | Snapshot |
| guest_email | VARCHAR(191) | Ya | NULL | Snapshot |
| guest_whatsapp | VARCHAR(32) | Tidak | — | Nomor normalized |
| arrival_estimate | VARCHAR(100) | Ya | NULL | Perkiraan kedatangan |
| special_request | TEXT | Ya | NULL | Permintaan khusus |
| room_type_name_snapshot | VARCHAR(120) | Tidak | — | Histori |
| room_name_snapshot | VARCHAR(120) | Tidak | — | Histori |
| price_per_night_snapshot | BIGINT UNSIGNED | Tidak | — | Rupiah |
| subtotal | BIGINT UNSIGNED | Tidak | — | Sebelum diskon |
| promotion_id | BIGINT UNSIGNED | Ya | NULL | Promo |
| promotion_code_snapshot | VARCHAR(100) | Ya | NULL | Histori |
| promotion_discount | BIGINT UNSIGNED | Tidak | 0 | Rupiah |
| points_redeemed | BIGINT UNSIGNED | Tidak | 0 | Jumlah poin |
| points_discount | BIGINT UNSIGNED | Tidak | 0 | Rupiah |
| total_amount | BIGINT UNSIGNED | Tidak | — | Nominal akhir |
| currency | CHAR(3) | Tidak | IDR | Mata uang |
| eligible_loyalty_amount | BIGINT UNSIGNED | Tidak | 0 | Basis earn point |
| payment_expires_at | TIMESTAMP | Ya | NULL | Hold expiration |
| policy_version_id | BIGINT UNSIGNED | Ya | NULL | Versi kebijakan |
| policy_accepted_at | TIMESTAMP | Ya | NULL | Waktu persetujuan |
| guest_access_token_hash | CHAR(64) | Ya | NULL | Hash token akses |
| claimed_at | TIMESTAMP | Ya | NULL | Waktu claim |
| claim_method | VARCHAR(50) | Ya | NULL | Metode claim |
| checked_in_at | TIMESTAMP | Ya | NULL | Aktual |
| checked_out_at | TIMESTAMP | Ya | NULL | Aktual |
| completed_at | TIMESTAMP | Ya | NULL | Aktual |
| cancelled_at | TIMESTAMP | Ya | NULL | Waktu cancel |
| cancellation_reason | VARCHAR(255) | Ya | NULL | Alasan |
| cancellation_notes | TEXT | Ya | NULL | Catatan |
| cancelled_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Aktor |
| needs_attention | BOOLEAN | Tidak | FALSE | Kasus anomali |
| attention_reason | VARCHAR(191) | Ya | NULL | Kode alasan |
| internal_notes | TEXT | Ya | NULL | Admin only |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(booking_code)`.
- `UNIQUE(invoice_number)` dengan NULL diperbolehkan.
- `UNIQUE(idempotency_key)` untuk request yang menggunakan key.
- `INDEX(room_id, check_in, check_out, status)` sangat penting.
- `INDEX(status, payment_expires_at)` untuk scheduler.
- `INDEX(user_id, status, check_in)` untuk member.
- `INDEX(source, created_at)` untuk laporan.
- `INDEX(payment_status, created_at)`.
- `INDEX(guest_email)` dan `INDEX(guest_whatsapp)` untuk lookup internal; bukan otorisasi tunggal.
- `INDEX(needs_attention, created_at)`.

### Foreign Key

- `user_id -> users.id ON DELETE SET NULL`.
- `room_id -> rooms.id ON DELETE RESTRICT`.
- `created_by_admin_id -> admins.id ON DELETE SET NULL`.
- `promotion_id -> promotions.id ON DELETE SET NULL`.
- `policy_version_id -> policy_versions.id ON DELETE SET NULL`.
- `cancelled_by_admin_id -> admins.id ON DELETE SET NULL`.

### Relasi / Penggunaan

`Booking belongsTo User nullable`; `belongsTo Room`; `hasMany Payment`; `hasMany LoyaltyTransaction`; `hasMany PromotionUsage`; `hasMany Refund`.

### Catatan Implementasi

Nilai harga wajib dihitung backend. Semua snapshot dan status awal ditulis melalui BookingService di dalam transaction. Jangan menerima `total_amount` dari frontend.

## 7.3.10 `payments`

Riwayat payment attempt. Satu booking dapat mempunyai beberapa attempt bila percobaan sebelumnya gagal selama hold booking masih valid.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| booking_id | BIGINT UNSIGNED | Tidak | — | Booking |
| provider | VARCHAR(30) | Tidak | midtrans | Provider |
| provider_order_id | VARCHAR(100) | Tidak | — | Unik per attempt |
| transaction_id | VARCHAR(191) | Ya | NULL | ID provider |
| attempt_no | SMALLINT UNSIGNED | Tidak | 1 | Urutan |
| snap_token | TEXT | Ya | NULL | Token Snap |
| payment_type | VARCHAR(100) | Ya | NULL | Metode aktual |
| gross_amount | BIGINT UNSIGNED | Tidak | — | Harus sama dengan booking total |
| status | VARCHAR(30) | Tidak | unpaid | PaymentStatus |
| provider_transaction_status | VARCHAR(50) | Ya | NULL | Status mentah |
| fraud_status | VARCHAR(50) | Ya | NULL | Fraud status |
| provider_transaction_time | TIMESTAMP | Ya | NULL | Waktu provider |
| paid_at | TIMESTAMP | Ya | NULL | Waktu paid |
| expired_at | TIMESTAMP | Ya | NULL | Waktu expired |
| refunded_at | TIMESTAMP | Ya | NULL | Full refund |
| raw_response | JSON | Ya | NULL | Payload ter-redact |
| last_status_checked_at | TIMESTAMP | Ya | NULL | Reconciliation |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(provider, provider_order_id)`.
- `UNIQUE(booking_id, attempt_no)`.
- `INDEX(booking_id, status)`.
- `INDEX(transaction_id)`.
- `INDEX(status, created_at)`.

### Foreign Key

- `booking_id -> bookings.id ON DELETE RESTRICT`.

### Relasi / Penggunaan

`Payment belongsTo Booking`.

### Catatan Implementasi

Jangan menghapus payment gagal/expired. Jangan log Snap token atau Server Key.

## 7.3.11 `payment_webhook_events`

Inbox/audit webhook untuk idempotency, observability, dan debugging.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| provider | VARCHAR(30) | Tidak | midtrans | Provider |
| deduplication_key | CHAR(64) | Tidak | — | Hash event stabil |
| provider_order_id | VARCHAR(100) | Ya | NULL | Lookup |
| transaction_id | VARCHAR(191) | Ya | NULL | Lookup |
| event_status | VARCHAR(50) | Ya | NULL | Status provider |
| signature_valid | BOOLEAN | Tidak | FALSE | Hasil verifikasi |
| amount_valid | BOOLEAN | Tidak | FALSE | Hasil nominal |
| processing_status | VARCHAR(30) | Tidak | received | received/processed/ignored/failed |
| payload | JSON | Tidak | — | Payload ter-redact |
| error_message | TEXT | Ya | NULL | Error aman |
| processed_at | TIMESTAMP | Ya | NULL | Waktu proses |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(provider, deduplication_key)`.
- `INDEX(provider_order_id, created_at)`.
- `INDEX(processing_status, created_at)`.

### Foreign Key

- Tidak ada.

### Catatan Implementasi

Tabel ini bukan sumber status payment. Status final tetap diproses service setelah verifikasi.

## 7.3.12 `room_blocks`

Interval kamar tidak dapat dijual karena maintenance, perbaikan AC, renovasi, pembersihan khusus, penggunaan internal, atau alasan lainnya.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| room_id | BIGINT UNSIGNED | Tidak | — | Kamar |
| start_date | DATE | Tidak | — | Inclusive |
| end_date | DATE | Tidak | — | Exclusive |
| reason_type | VARCHAR(50) | Tidak | other | Kategori |
| reason | VARCHAR(255) | Tidak | — | Alasan |
| notes | TEXT | Ya | NULL | Catatan internal |
| created_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Aktor |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `INDEX(room_id, start_date, end_date)`.
- Validasi `end_date > start_date`.

### Foreign Key

- `room_id -> rooms.id ON DELETE RESTRICT`.
- `created_by_admin_id -> admins.id ON DELETE SET NULL`.

### Relasi / Penggunaan

`RoomBlock belongsTo Room`; `belongsTo Admin nullable`.

### Catatan Implementasi

Pembuatan room block wajib mengecek booking blocking. Jika bentrok, tampilkan daftar conflict dan jangan simpan diam-diam.

## 7.3.13 `promotions`

Master promo kode yang seluruh validasinya dilakukan backend.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| code | VARCHAR(100) | Tidak | — | Uppercase normalized |
| name | VARCHAR(150) | Tidak | — | Nama |
| description | TEXT | Ya | NULL | Deskripsi |
| type | VARCHAR(30) | Tidak | — | percentage/fixed |
| value | BIGINT UNSIGNED | Tidak | — | Nilai sesuai type |
| starts_at | TIMESTAMP | Tidak | — | Mulai |
| ends_at | TIMESTAMP | Tidak | — | Selesai |
| minimum_booking_amount | BIGINT UNSIGNED | Tidak | 0 | Minimum subtotal |
| maximum_discount | BIGINT UNSIGNED | Ya | NULL | Cap persen |
| usage_quota | INT UNSIGNED | Ya | NULL | NULL tidak terbatas |
| max_usage_per_user | INT UNSIGNED | Ya | NULL | Opsional |
| is_active | BOOLEAN | Tidak | TRUE | Status |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(code)`.
- `INDEX(is_active, starts_at, ends_at)`.
- Validasi `ends_at > starts_at` dan percentage dalam rentang sah.

### Foreign Key

- Tidak ada.

### Relasi / Penggunaan

`Promotion hasMany PromotionUsage`; `Promotion hasMany Booking`.

### Catatan Implementasi

Jangan percaya kode atau nominal diskon dari frontend. Quota harus dilindungi transaction dan row lock.

## 7.3.14 `promotion_usages`

Reservasi dan konsumsi kuota promo agar checkout bersamaan tidak melebihi kuota.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| promotion_id | BIGINT UNSIGNED | Tidak | — | Promo |
| booking_id | BIGINT UNSIGNED | Tidak | — | Booking |
| user_id | BIGINT UNSIGNED | Ya | NULL | Member bila ada |
| status | VARCHAR(30) | Tidak | reserved | reserved/consumed/released |
| discount_amount | BIGINT UNSIGNED | Tidak | 0 | Snapshot |
| reserved_at | TIMESTAMP | Tidak | — | Reserve |
| consumed_at | TIMESTAMP | Ya | NULL | Consumed |
| released_at | TIMESTAMP | Ya | NULL | Released |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(promotion_id, booking_id)`.
- `INDEX(promotion_id, status)`.
- `INDEX(user_id, promotion_id, status)`.

### Foreign Key

- `promotion_id -> promotions.id ON DELETE RESTRICT`.
- `booking_id -> bookings.id ON DELETE RESTRICT`.
- `user_id -> users.id ON DELETE SET NULL`.

### Relasi / Penggunaan

`PromotionUsage belongsTo Promotion/Booking/User`.

### Catatan Implementasi

Quota menghitung `reserved + consumed`; release saat booking expired/cancel. Status transition harus idempotent.

## 7.3.15 `loyalty_transactions`

Ledger immutable untuk seluruh perubahan poin.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| user_id | BIGINT UNSIGNED | Tidak | — | Pemilik |
| booking_id | BIGINT UNSIGNED | Ya | NULL | Booking terkait |
| type | VARCHAR(30) | Tidak | — | earn/redeem/expire/adjustment/reversal |
| points | BIGINT | Tidak | — | Positif masuk, negatif keluar |
| balance_after | BIGINT | Tidak | — | Saldo sesudah |
| remaining_points | BIGINT UNSIGNED | Tidak | 0 | Sisa lot positif |
| description | VARCHAR(255) | Tidak | — | Deskripsi |
| expires_at | TIMESTAMP | Ya | NULL | Expiry lot positif |
| source_transaction_id | BIGINT UNSIGNED | Ya | NULL | Transaksi rujukan |
| idempotency_key | VARCHAR(150) | Tidak | — | Unique event key |
| created_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Adjustment manual |
| metadata | JSON | Ya | NULL | Konteks aman |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(idempotency_key)`.
- `INDEX(user_id, created_at)`.
- `INDEX(user_id, expires_at, remaining_points)`.
- `INDEX(booking_id)`.

### Foreign Key

- `user_id -> users.id ON DELETE RESTRICT`.
- `booking_id -> bookings.id ON DELETE SET NULL`.
- `source_transaction_id -> loyalty_transactions.id ON DELETE SET NULL`.
- `created_by_admin_id -> admins.id ON DELETE SET NULL`.

### Relasi / Penggunaan

`LoyaltyTransaction belongsTo User`; `belongsTo Booking nullable`; self-reference source transaction.

### Catatan Implementasi

Jangan update/hapus histori untuk membatalkan poin. Buat reversal. `remaining_points` boleh berubah sebagai state lot, tetapi perubahan saldo harus selalu mempunyai event ledger.

## 7.3.16 `loyalty_point_allocations`

Mencatat debit poin menggunakan lot positif mana. Ini diperlukan untuk FIFO, expiry, dan reversal yang akurat.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| debit_transaction_id | BIGINT UNSIGNED | Tidak | — | Redeem/expire debit |
| credit_transaction_id | BIGINT UNSIGNED | Tidak | — | Earn/positive lot |
| points | BIGINT UNSIGNED | Tidak | — | Jumlah alokasi |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(debit_transaction_id, credit_transaction_id)`.
- `INDEX(credit_transaction_id)`.

### Foreign Key

- `debit_transaction_id -> loyalty_transactions.id ON DELETE RESTRICT`.
- `credit_transaction_id -> loyalty_transactions.id ON DELETE RESTRICT`.

### Catatan Implementasi

Redemption mengambil lot dengan expiry terdekat lebih dahulu. Reversal mengembalikan poin ke lot asal sejauh kebijakan masih mengizinkan.

## 7.3.17 `booking_claim_tokens`

Token sekali pakai untuk klaim guest booking.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| booking_id | BIGINT UNSIGNED | Tidak | — | Guest booking |
| token_hash | CHAR(64) | Tidak | — | Hash token |
| target_email | VARCHAR(191) | Tidak | — | Email yang harus cocok |
| expires_at | TIMESTAMP | Tidak | — | Masa berlaku |
| used_at | TIMESTAMP | Ya | NULL | Sekali pakai |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(token_hash)`.
- `INDEX(booking_id, used_at, expires_at)`.

### Foreign Key

- `booking_id -> bookings.id ON DELETE RESTRICT`.

### Relasi / Penggunaan

`BookingClaimToken belongsTo Booking`.

### Catatan Implementasi

Raw token hanya dikirim ke user; database menyimpan hash. Token tidak boleh muncul di log.

## 7.3.18 `refunds`

Struktur refund penuh/partial yang diproses admin.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| booking_id | BIGINT UNSIGNED | Tidak | — | Booking |
| payment_id | BIGINT UNSIGNED | Tidak | — | Payment asal |
| requested_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Peminta |
| processed_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Pemroses |
| amount | BIGINT UNSIGNED | Tidak | — | Nominal |
| reason | VARCHAR(255) | Tidak | — | Alasan |
| notes | TEXT | Ya | NULL | Catatan |
| status | VARCHAR(30) | Tidak | requested | RefundStatus |
| requested_at | TIMESTAMP | Tidak | — | Waktu request |
| processed_at | TIMESTAMP | Ya | NULL | Waktu selesai |
| provider_refund_id | VARCHAR(191) | Ya | NULL | ID provider |
| provider_response | JSON | Ya | NULL | Response ter-redact |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `INDEX(booking_id, status)`.
- `INDEX(payment_id)`.
- Total refund sukses per payment tidak boleh melebihi nominal yang benar-benar dibayar.

### Foreign Key

- `booking_id -> bookings.id ON DELETE RESTRICT`.
- `payment_id -> payments.id ON DELETE RESTRICT`.
- `requested_by_admin_id -> admins.id ON DELETE SET NULL`.
- `processed_by_admin_id -> admins.id ON DELETE SET NULL`.

### Relasi / Penggunaan

`Refund belongsTo Booking/Payment/Admin`.

### Catatan Implementasi

Guest tidak memiliki endpoint refund otomatis. Refund sukses harus memicu evaluasi point reversal bila poin sudah pernah diberikan.

## 7.3.19 `policy_versions`

Versi kebijakan yang dipublikasikan dan ditautkan ke booking.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| policy_key | VARCHAR(100) | Tidak | guest_policy | Jenis dokumen |
| version | VARCHAR(50) | Tidak | — | Contoh 2026-07-v1 |
| title | VARCHAR(191) | Tidak | — | Judul |
| content | LONGTEXT | Tidak | — | Isi sanitized |
| is_current | BOOLEAN | Tidak | FALSE | Versi aktif |
| published_at | TIMESTAMP | Ya | NULL | Waktu publish |
| created_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Aktor |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(policy_key, version)`.
- `INDEX(policy_key, is_current)`.
- Service memastikan satu current per `policy_key`.

### Foreign Key

- `created_by_admin_id -> admins.id ON DELETE SET NULL`.

### Relasi / Penggunaan

`PolicyVersion hasMany Booking`.

### Catatan Implementasi

Jangan mengubah isi versi lama yang sudah disetujui booking. Buat versi baru.

## 7.3.20 `expenses`

Pengeluaran operasional sederhana untuk estimasi laba, bukan akuntansi resmi.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| expense_date | DATE | Tidak | — | Tanggal |
| category | VARCHAR(50) | Tidak | other | Kategori |
| amount | BIGINT UNSIGNED | Tidak | — | Rupiah |
| description | TEXT | Tidak | — | Keterangan |
| receipt_path | VARCHAR(255) | Ya | NULL | Bukti opsional |
| created_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Aktor |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `INDEX(expense_date, category)`.

### Foreign Key

- `created_by_admin_id -> admins.id ON DELETE SET NULL`.

### Relasi / Penggunaan

`Expense belongsTo Admin nullable`.

### Catatan Implementasi

Kategori: listrik, air, internet, laundry, perlengkapan_kamar, perbaikan, gaji, other. Laporan harus diberi label Estimasi Laba Bersih.

## 7.3.21 `settings`

Konfigurasi bisnis terstruktur.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| group | VARCHAR(100) | Tidak | general | Kelompok |
| key | VARCHAR(150) | Tidak | — | Kunci |
| value | LONGTEXT | Ya | NULL | Nilai serialized sesuai type |
| type | VARCHAR(30) | Tidak | string | string/integer/boolean/json/time/url |
| is_public | BOOLEAN | Tidak | FALSE | Boleh dibaca publik |
| updated_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Aktor |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(group, key)`.
- `INDEX(is_public)`.

### Foreign Key

- `updated_by_admin_id -> admins.id ON DELETE SET NULL`.

### Catatan Implementasi

Jangan menyimpan Midtrans key, Google Client Secret, password, atau secret lain di tabel ini. Secret tetap di environment/secret manager.

## 7.3.22 `galleries`

Galeri publik umum di luar foto tipe kamar.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| title | VARCHAR(191) | Ya | NULL | Judul |
| path | VARCHAR(255) | Tidak | — | File |
| alt_text | VARCHAR(255) | Ya | NULL | Accessibility/SEO |
| is_active | BOOLEAN | Tidak | TRUE | Tampil |
| sort_order | INT UNSIGNED | Tidak | 0 | Urutan |
| created_by_admin_id | BIGINT UNSIGNED | Ya | NULL | Aktor |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `INDEX(is_active, sort_order)`.

### Foreign Key

- `created_by_admin_id -> admins.id ON DELETE SET NULL`.

### Catatan Implementasi

Gunakan validasi upload yang sama ketatnya dengan room image.

## 7.3.23 `audit_logs`

Audit trail untuk aksi penting.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| actor_type | VARCHAR(50) | Ya | NULL | admin/user/system |
| actor_id | BIGINT UNSIGNED | Ya | NULL | ID aktor |
| action | VARCHAR(150) | Tidak | — | Kode aksi |
| subject_type | VARCHAR(150) | Ya | NULL | Domain/model |
| subject_id | BIGINT UNSIGNED | Ya | NULL | ID |
| before | JSON | Ya | NULL | Nilai sebelum yang aman |
| after | JSON | Ya | NULL | Nilai sesudah yang aman |
| ip_address | VARCHAR(45) | Ya | NULL | IP |
| user_agent | VARCHAR(500) | Ya | NULL | Dipangkas |
| metadata | JSON | Ya | NULL | Konteks |
| created_at | TIMESTAMP | Tidak | — | Immutable |

### Index dan Constraint

- `INDEX(actor_type, actor_id, created_at)`.
- `INDEX(subject_type, subject_id, created_at)`.
- `INDEX(action, created_at)`.

### Foreign Key

- Tidak ada.

### Catatan Implementasi

Jangan log password, raw token, secret, full card data, atau data pembayaran sensitif.

## 7.3.24 `document_sequences`

Generator kode booking/invoice bulanan yang aman dari race condition.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| document_type | VARCHAR(30) | Tidak | — | booking/invoice |
| period | CHAR(6) | Tidak | — | YYYYMM |
| last_number | BIGINT UNSIGNED | Tidak | 0 | Nomor terakhir |
| created_at / updated_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `UNIQUE(document_type, period)`.

### Foreign Key

- Tidak ada.

### Catatan Implementasi

Ambil row type+period di dalam transaction dengan `lockForUpdate`, increment, lalu format. Tetap pasang unique constraint pada kode akhir.

## 7.3.25 `booking_status_histories`

Histori eksplisit transisi booking untuk operasional dan audit.

### Kolom

| Kolom | Tipe | Null | Default | Constraint/Keterangan |
|---|---|---:|---|---|
| id | BIGINT UNSIGNED | Tidak | auto | Primary key |
| booking_id | BIGINT UNSIGNED | Tidak | — | Booking |
| from_status | VARCHAR(30) | Ya | NULL | Status awal |
| to_status | VARCHAR(30) | Tidak | — | Status baru |
| reason | VARCHAR(255) | Ya | NULL | Alasan |
| actor_type | VARCHAR(50) | Tidak | system | system/admin/user |
| actor_id | BIGINT UNSIGNED | Ya | NULL | ID aktor |
| metadata | JSON | Ya | NULL | Konteks |
| created_at | TIMESTAMP | Tidak | — | Timestamp |

### Index dan Constraint

- `INDEX(booking_id, created_at)`.
- `INDEX(to_status, created_at)`.

### Foreign Key

- `booking_id -> bookings.id ON DELETE RESTRICT`.

### Relasi / Penggunaan

`Booking hasMany BookingStatusHistory`.

### Catatan Implementasi

Insert history harus berada dalam transaction yang sama dengan perubahan status booking.



# 7.4 INVARIANT DATABASE DAN KONSISTENSI DATA

Kiro harus memperlakukan aturan berikut sebagai invariant:

1. `check_out > check_in`.
2. `nights = jumlah hari kalender antara check_in dan check_out`.
3. booking hanya menunjuk kamar aktif saat dibuat.
4. `guest_count >= 1`.
5. `guest_count <= room_type.capacity`.
6. `subtotal = nights × price_per_night_snapshot` pada model harga V1.
7. `promotion_discount >= 0`.
8. `points_discount >= 0`.
9. promo dan poin tidak boleh aktif bersamaan.
10. `total_amount = max(0, subtotal - promotion_discount - points_discount)`.
11. `payment.gross_amount = booking.total_amount` untuk payment attempt yang dibuat.
12. `pending_payment` yang memblokir hanya berlaku selama `payment_expires_at > now`.
13. loyalty earn per booking hanya satu kali.
14. loyalty redeem per booking hanya satu transaksi debit efektif; pembatalan menggunakan reversal.
15. total alokasi debit loyalty harus sama dengan nilai absolut debit.
16. saldo cache user harus sama dengan saldo ledger setelah workflow selesai.
17. satu provider order ID hanya untuk satu payment attempt.
18. satu claim token hanya dipakai sekali.
19. satu policy version lama tidak boleh diedit setelah ada booking yang mengacu.
20. room block dan booking menggunakan interval setengah terbuka `[start, end)`.

## 7.4.1 Urutan Migration

Karena foreign key saling terkait, buat migration dalam urutan yang aman:

1. users;
2. admins;
3. social_accounts;
4. room_types;
5. rooms;
6. facilities;
7. room_type_facility;
8. room_images;
9. promotions;
10. policy_versions;
11. bookings;
12. payments;
13. payment_webhook_events;
14. room_blocks;
15. promotion_usages;
16. loyalty_transactions;
17. loyalty_point_allocations;
18. booking_claim_tokens;
19. refunds;
20. expenses;
21. settings;
22. galleries;
23. audit_logs;
24. document_sequences;
25. booking_status_histories.

Jika project existing mempunyai tabel dengan nama sama, jangan membuat migration duplikat. Audit struktur existing dan buat migration alter yang aman.

## 7.4.2 Kebijakan Delete

### Jangan Hard Delete

- bookings;
- payments;
- payment_webhook_events;
- loyalty_transactions;
- loyalty_point_allocations;
- refunds;
- booking_status_histories;
- audit_logs;
- policy version yang pernah dipakai.

### Boleh Dihapus dengan Syarat

- room image yang tidak dipakai;
- gallery image;
- social account jika user memutus link dengan aman.

### Lebih Baik Dinonaktifkan

- room type;
- room;
- facility;
- promotion;
- user;
- admin.

---

# 8. RELASI ELOQUENT

## 8.1 User

Wajib:

- `hasMany(Booking::class)`;
- `hasMany(SocialAccount::class)`;
- `hasMany(LoyaltyTransaction::class)`.

Tambahkan query/helper:

- booking aktif;
- booking selesai;
- current loyalty balance dari service, bukan sekadar akses kolom cache;
- unexpired loyalty lots.

## 8.2 Admin

Relasi opsional:

- booking manual yang dibuat;
- room block;
- expense;
- policy version;
- point adjustment;
- cancellation.

Jangan menjadikan relasi audit polymorphic sebagai alasan untuk melewatkan authorization.

## 8.3 RoomType

- `hasMany(Room::class)`;
- `belongsToMany(Facility::class)`;
- `hasMany(RoomImage::class)`.

Tambahkan scope:

- `active()`;
- urutan display.

## 8.4 Room

- `belongsTo(RoomType::class)`;
- `hasMany(Booking::class)`;
- `hasMany(RoomBlock::class)`.

Scope:

- `active()`;
- `sellable()` jika status enum mendukung.

Jangan menaruh seluruh query availability di model Room. Availability tetap milik `AvailabilityService`.

## 8.5 Booking

- `belongsTo(User::class)->nullable`;
- `belongsTo(Room::class)`;
- `belongsTo(Admin::class, 'created_by_admin_id')->nullable`;
- `belongsTo(Promotion::class)->nullable`;
- `belongsTo(PolicyVersion::class)->nullable`;
- `hasMany(Payment::class)`;
- `hasMany(PromotionUsage::class)`;
- `hasMany(LoyaltyTransaction::class)`;
- `hasMany(Refund::class)`;
- `hasMany(BookingStatusHistory::class)`.

Accessor/presentation boleh dibuat untuk:

- label status;
- label sumber;
- formatted Rupiah;
- duration text.

Business logic transisi status jangan disembunyikan sebagai setter model bebas.

## 8.6 Payment

- `belongsTo(Booking::class)`;
- `hasMany(Refund::class)`.

Tambahkan method semantik ringan seperti `isPaid()` boleh dilakukan, tetapi mapping status Midtrans tetap di `MidtransPaymentService`.

## 8.7 LoyaltyTransaction

- `belongsTo(User::class)`;
- `belongsTo(Booking::class)->nullable`;
- self-reference `sourceTransaction`;
- hasMany allocation sebagai debit;
- hasMany allocation sebagai credit.

## 8.8 Promotion

- `hasMany(PromotionUsage::class)`;
- `hasMany(Booking::class)`.

Promo validation tetap di `PromotionService`.

---

# 9. SERVICE CLASS WAJIB

# 9.1 `AvailabilityService`

## Tanggung Jawab

Menentukan ketersediaan kamar fisik berdasarkan:

- tanggal;
- jumlah tamu;
- status kamar;
- status tipe kamar;
- booking blocking;
- hold pending yang belum expired;
- room block.

## Method Penting

### `searchAvailableRoomTypes(checkIn, checkOut, guestCount)`

Input:

- tanggal check-in;
- tanggal check-out;
- jumlah tamu.

Output:

- daftar tipe kamar aktif;
- jumlah kamar fisik tersedia;
- kandidat kamar fisik;
- harga display yang dihitung PricingService;
- metadata yang aman untuk frontend.

Tidak boleh menganggap hasil ini sebagai reservation final.

### `findAvailableRooms(roomTypeId, checkIn, checkOut, guestCount)`

Mengembalikan kamar fisik kandidat.

### `isRoomAvailable(roomId, checkIn, checkOut, excludeBookingId = null)`

Dipakai saat recheck.

### `assertRoomAvailableForBooking(...)`

Melempar domain exception yang dapat diterjemahkan menjadi pesan user-friendly.

## Query Booking Blocking

Kondisi overlap:

`existing.check_in < new.check_out`

DAN

`existing.check_out > new.check_in`

Booking blocking:

- `confirmed`;
- `checked_in`;
- `pending_payment` hanya jika `payment_expires_at > now`.

Sesuai kebutuhan V1, `checked_out`, `completed`, `expired`, `cancelled`, `no_show` tidak memblokir interval.

Room block overlap menggunakan rumus yang sama.

## Error Handling

Gunakan domain exception seperti:

- invalid date range;
- room not active;
- capacity exceeded;
- room no longer available.

Controller mengubah exception menjadi redirect/422/409 sesuai context.

# 9.2 `PricingService`

## Tanggung Jawab

Menghitung harga server-side dan membuat snapshot.

## Method Penting

### `calculateQuote(roomType, checkIn, checkOut, promoCode = null, redeemPoints = null, user = null)`

Output object/DTO terstruktur:

- nights;
- price per night;
- subtotal;
- promotion;
- promotion discount;
- points redeemed;
- points discount;
- total amount;
- eligible loyalty amount;
- explanation.

### `calculateNights(checkIn, checkOut)`

Harus pure dan memiliki unit test.

## Aturan V1

1. harga per malam dari `room_type.base_price`;
2. `nights = diff date`;
3. `subtotal = nights × rate`;
4. promo atau point;
5. total tidak negatif;
6. loyalty earning basis default = total final yang benar-benar dibayar.

Jangan menerima subtotal/discount/total dari request.

# 9.3 `PromotionService`

## Tanggung Jawab

- normalisasi kode;
- validasi masa aktif;
- minimum booking;
- tipe diskon;
- maximum discount;
- quota;
- per-user limit bila dipakai;
- reserve quota;
- consume quota;
- release quota.

## Method Penting

- `validateForQuote(code, subtotal, user?)`;
- `calculateDiscount(promotion, subtotal)`;
- `reserveForBooking(promotion, booking, user?)`;
- `consumeForBooking(booking)`;
- `releaseForBooking(booking)`.

## Concurrency

Saat reserve:

1. buka database transaction;
2. lock row promotion `FOR UPDATE`;
3. recheck aktif dan waktu;
4. hitung usages status `reserved` + `consumed`;
5. recheck quota;
6. insert usage reserved;
7. commit.

# 9.4 `BookingService`

## Tanggung Jawab

Workflow booking, bukan semua domain.

## Method Penting

- `createGuestBooking(command)`;
- `createMemberBooking(command, user)`;
- `createManualBooking(command, admin)`;
- `cancelBooking(booking, reason, admin)`;
- `expirePendingBooking(booking)`;
- `transitionStatus(...)` internal terkontrol;
- `markNeedsAttention(...)`.

## Workflow Create Booking

1. validasi request;
2. ambil idempotency key;
3. jika key sudah ada, return booking existing yang sah;
4. mulai DB transaction;
5. lock row kamar target dengan `lockForUpdate`;
6. cek kamar aktif;
7. cek kapasitas;
8. recheck overlap booking;
9. recheck room block;
10. lock promo/user loyalty bila digunakan;
11. hitung quote server-side;
12. generate booking code melalui sequence lock;
13. generate raw guest access token;
14. simpan hash token;
15. simpan booking snapshot;
16. reserve promo atau debit poin sesuai pilihan;
17. tulis status history;
18. commit;
19. buat payment attempt melalui payment service;
20. jika pembuatan payment gagal, jangan diam-diam membuat booking baru;
21. tampilkan status dan retry yang aman.

Perhatikan pemisahan transaksi database dan external API. Jangan menahan DB lock sambil menunggu request jaringan Midtrans terlalu lama.

Rekomendasi:

- transaction pertama mengamankan booking/hold;
- setelah commit, panggil Midtrans;
- jika Midtrans gagal, booking tetap pending dengan mekanisme retry selama hold valid;
- jangan melepas kamar hanya karena satu call jaringan gagal.

# 9.5 `MidtransPaymentService`

## Tanggung Jawab

- konfigurasi SDK;
- membuat payment attempt;
- memperoleh Snap token;
- membentuk provider order ID unik;
- memverifikasi notification;
- mapping status;
- update payment;
- update booking;
- server-to-server status check;
- reconciliation.

## Method Penting

- `createOrResumeSnapPayment(booking)`;
- `createPaymentAttempt(booking)`;
- `handleWebhook(payload, headers?)`;
- `verifySignature(payload)`;
- `fetchStatus(providerOrderId)`;
- `reconcilePayment(payment)`;
- `mapProviderStatus(payload)`.

## Aturan

- Server Key hanya backend.
- Client Key boleh diekspos hanya sesuai mekanisme Snap resmi.
- `gross_amount` harus dari booking.
- provider order ID unik.
- callback JS tidak mengubah payment status.
- webhook idempotent.
- notification duplicate aman.
- out-of-order event tidak boleh menurunkan status final secara sembarangan.

# 9.6 `LoyaltyPointService`

## Tanggung Jawab

- saldo;
- earn;
- redeem;
- reversal;
- expiry;
- adjustment;
- FIFO lot allocation;
- idempotency.

## Method Penting

- `getBalance(user)`;
- `previewRedemption(user, eligibleAmount, requestedPoints)`;
- `redeemForBooking(user, booking, points)`;
- `reverseRedemptionForBooking(booking, reason)`;
- `awardForCompletedBooking(booking)`;
- `reverseEarnForBooking(booking, reason)`;
- `expirePointsForUser(user, now)`;
- `adjustPoints(user, points, reason, admin)`.

Semua method mutasi wajib transaction + lock user/lot yang relevan.

# 9.7 `InvoiceService`

## Tanggung Jawab

- menentukan eligibility invoice;
- generate invoice number;
- mengambil snapshot booking/payment;
- render PDF;
- authorization guest/member/admin;
- cache file bila dipilih.

Invoice lama tidak boleh membaca harga terbaru dari room type.

# 9.8 `BookingClaimService`

## Tanggung Jawab

- membuat claim token;
- memvalidasi token;
- mencocokkan email terverifikasi;
- linking guest booking;
- audit manual claim.

Method:

- `issueClaimToken(booking)`;
- `claimWithVerifiedEmail(user, booking, rawToken?)`;
- `claimAutomaticallyAfterLogin(user, booking)` hanya bila aturan aman terpenuhi;
- `manualClaim(booking, user, admin, reason)`.

---

# 10. ALGORITMA KRITIS

# 10.1 Interval Menginap

Gunakan interval:

`[check_in, check_out)`

Contoh:

- booking A: 10 Juli–12 Juli;
- kamar terpakai malam 10 dan 11;
- booking B boleh check-in 12 Juli.

Overlap hanya jika:

`existing.check_in < new_check_out`

dan:

`existing.check_out > new_check_in`

Jangan menggunakan perbandingan `<=` pada kedua sisi yang membuat checkout dan check-in pada tanggal sama dianggap bentrok.

# 10.2 Pencarian Availability

Pseudoflow:

1. validate dates;
2. validate guest count;
3. ambil room types aktif dengan capacity cukup;
4. ambil rooms aktif;
5. exclude room yang mempunyai booking blocking overlap;
6. exclude room yang mempunyai room block overlap;
7. kelompokkan berdasarkan room type;
8. tampilkan hanya room type dengan setidaknya satu room fisik tersedia.

Pencarian ini bersifat informasional. Booking final tetap recheck.

# 10.3 Double Booking Protection

Ini adalah desain wajib.

## Lapisan 1 — Search

Filter kamar yang tampak tersedia.

## Lapisan 2 — Checkout

Sebelum menampilkan ringkasan final, recheck.

## Lapisan 3 — Create Booking

Authoritative transaction:

1. `DB::transaction`;
2. select `rooms.id = target` dengan `lockForUpdate`;
3. setelah lock diperoleh, jalankan ulang overlap query;
4. jalankan ulang room block query;
5. bila conflict, rollback;
6. hitung harga ulang;
7. insert booking.

Mengapa lock row kamar?

Query overlap saja tidak cukup untuk mencegah dua request yang sama-sama melihat “tidak ada conflict” sebelum salah satunya insert. Lock row kamar membuat request untuk kamar yang sama antre secara serial.

## Lapisan 4 — Idempotency

Frontend checkout mendapatkan `idempotency_key` server-generated/session-bound.

Submit ulang dengan key sama:

- jangan membuat booking kedua;
- return booking existing yang cocok;
- bila payload berbeda untuk key sama, reject.

## Lapisan 5 — Unique Constraint

- booking code unique;
- provider order ID unique;
- loyalty idempotency unique.

## Test Concurrency

Test wajib menggunakan dua proses/request yang mencoba:

- room sama;
- check-in/out sama;
- hampir bersamaan.

Hasil:

- tepat satu booking sukses;
- yang lain menerima conflict/unavailable;
- tidak ada dua row blocking.

# 10.4 Hold 30 Menit

Saat booking website dibuat:

- status `pending_payment`;
- `payment_expires_at = server_now + 30 minutes`.

Availability menganggap booking pending memblokir hanya saat expiry masih di masa depan.

Scheduler:

1. setiap menit atau frekuensi masuk akal;
2. query pending melewati expiry;
3. proses batch;
4. lock tiap booking;
5. recheck status;
6. recheck payment status;
7. jika belum paid, expire booking;
8. release promo reservation;
9. reverse point redemption;
10. tulis status history dan audit.

Frontend countdown hanya display:

`remaining = payment_expires_at - server_now`

Jangan menggunakan local browser timer sebagai kebenaran.

# 10.5 Race Scheduler vs Webhook

Keduanya harus lock booking/payment yang sama.

Jika webhook paid masuk lebih dulu:

- payment paid;
- booking confirmed;
- scheduler melihat bukan pending lalu skip.

Jika scheduler expire lebih dulu:

- booking expired;
- webhook paid datang kemudian;
- payment tetap dicatat paid;
- booking tidak otomatis dihidupkan;
- `needs_attention = true`.

# 10.6 Pricing

Rumus V1:

`nights = check_out - check_in`

`subtotal = nights × price_per_night`

Promo:

`promotion_discount = min(calculated_discount, allowed_cap)`

Point:

`requested_points_value = requested_points × Rp50`

`max_point_discount = floor(eligible_booking_value × 20%)`

`points_discount = min(requested_points_value, max_point_discount)`

Namun sistem harus menolak request points yang menghasilkan penggunaan di atas maksimum, bukan diam-diam mengambil semua poin tanpa penjelasan.

`total = subtotal - selected_discount`

Pastikan:

- total >= 0;
- promo dan point mutually exclusive;
- harga dikalkulasi ulang saat create.

# 10.7 Loyalty Earn

Aturan awal:

- Rp1.000 eligible amount = 1 poin;
- `floor(eligible_amount / 1000)`;
- award hanya ketika booking `completed`;
- booking paid;
- sumber eligible;
- booking terhubung user;
- belum pernah earn.

Sumber eligible default:

- website;
- whatsapp;
- walk_in.

Tidak eligible:

- booking_com;
- agoda;
- traveloka;
- other OTA.

`phone` dan `other` harus configurable, bukan ditebak.

Basis eligible default:

- jumlah final yang benar-benar dibayar setelah diskon;
- tidak termasuk nilai potongan poin;
- sesuaikan refund sukses.

Award flow:

1. lock booking;
2. verify completed;
3. verify payment eligible/paid sesuai source;
4. verify user exists;
5. verify source allowed;
6. compute points;
7. idempotency key `earn:booking:{id}`;
8. lock user;
9. compute current ledger balance;
10. create positive earn;
11. `remaining_points = points`;
12. `expires_at = earned_at + 18 months`;
13. update balance cache;
14. commit.

# 10.8 Loyalty Redemption

Aturan:

- hanya member login;
- minimum 100 poin;
- 1 poin = Rp50;
- maksimum discount 20%;
- promo tidak boleh bersamaan;
- poin cukup;
- hanya poin belum expired.

Flow:

1. quote preview tanpa mutasi;
2. saat booking create, lock user;
3. query positive lots `remaining_points > 0` dan `expires_at > now`, order expiry ascending lalu created_at;
4. recheck balance;
5. recheck max allowed;
6. create debit `redeem` dengan idempotency key;
7. create allocation ke lot;
8. decrement remaining lot;
9. update cache;
10. commit bersama booking.

Jika booking expired/cancel:

- create reversal transaction;
- restore allocation ke lot asal sesuai policy;
- jangan delete redeem.

Jika lot asal sudah melewati expiry ketika reversal dilakukan, dokumentasikan policy. Rekomendasi V1: reversal mengembalikan poin dengan masa berlaku yang tersisa; jika sudah expired, jangan membuat poin hidup kembali tanpa keputusan bisnis. Tandai sebagai expired/reversal adjustment yang eksplisit.

# 10.9 Loyalty Expiry

Scheduler harian:

1. cari lot positif `remaining_points > 0` dan `expires_at <= now`;
2. proses per user;
3. lock user dan lot;
4. buat transaksi `expire` negatif;
5. alokasikan ke lot;
6. set remaining menjadi 0;
7. update balance cache.

Idempotency key berdasarkan lot:

`expire:loyalty_transaction:{credit_id}`

# 10.10 Guest Claim

Default aman:

1. guest booking memiliki email;
2. booking belum terhubung user;
3. member login;
4. email member telah terverifikasi;
5. email normalized member sama dengan email snapshot booking;
6. claim token valid atau user masuk dari flow claim yang sah;
7. lock booking;
8. recheck `user_id IS NULL`;
9. set user_id;
10. set claimed_at;
11. set claim_method;
12. mark token used;
13. audit.

Jangan claim berdasarkan:

- nama sama;
- nomor WhatsApp tanpa verifikasi;
- tebakan booking code.

Manual admin claim:

- admin memilih user;
- sistem menampilkan identitas booking dan user;
- admin memasukkan alasan;
- audit before/after;
- tidak menghapus histori.

# 10.11 Kode Booking dan Invoice

Format:

- booking: `BKG-YYYYMM-0001`;
- invoice: `INV-YYYYMM-0001`.

Generator:

1. tentukan period dari waktu bisnis;
2. transaction;
3. ambil/buat sequence row;
4. lock row;
5. increment;
6. format padded;
7. insert entity;
8. unique constraint menjadi last defense.

Jangan `count() + 1`.

# 10.12 Midtrans Payment Creation

Flow:

1. pastikan booking masih `pending_payment`;
2. pastikan `payment_expires_at > now`;
3. pastikan total > 0;
4. cari active payment attempt yang masih dapat dilanjutkan;
5. jika ada Snap token valid, return token tersebut;
6. jika perlu attempt baru, buat provider order ID unik;
7. simpan payment row;
8. call Midtrans backend;
9. simpan Snap token;
10. return token.

Jika network error:

- tandai error secara aman;
- booking tetap ada selama hold;
- user dapat retry;
- jangan membuat booking baru.

# 10.13 Webhook Midtrans

Endpoint publik tetapi aman.

Urutan:

1. terima JSON;
2. jangan membutuhkan session/CSRF;
3. rate limit yang tidak menghalangi provider;
4. ekstrak identifier minimal;
5. verifikasi signature sesuai dokumentasi resmi;
6. cari payment berdasarkan provider order ID;
7. cocokkan booking;
8. cocokkan nominal;
9. buat dedup key;
10. insert webhook event;
11. jika duplicate processed, return 2xx aman;
12. lock payment dan booking;
13. map status provider;
14. terapkan transition idempotent;
15. update payment;
16. update booking jika allowed;
17. consume promo jika paid;
18. jangan award points;
19. commit;
20. return response cepat.

### Jangan

- percaya status dari query string;
- percaya frontend `onSuccess`;
- langsung award points;
- log Server Key;
- menolak duplicate dengan 500;
- mengubah paid kembali menjadi pending karena webhook lama.

# 10.14 Mapping Status Midtrans

Kiro harus mengikuti status resmi yang benar-benar didokumentasikan saat implementasi. Buat mapping terpusat.

Prinsip umum:

- settlement/capture yang sah dan fraud acceptable → paid;
- pending → pending;
- expire → expired;
- deny/cancel/failure → failed atau expired sesuai semantik;
- refund/partial refund → status sesuai refund.

Jangan mengandalkan nama status dari ingatan. Verifikasi dokumentasi resmi.

# 10.15 Reconciliation

Sediakan command/scheduler untuk payment yang:

- pending terlalu lama;
- webhook belum diterima;
- needs attention.

Flow:

1. fetch status server-to-server;
2. verify order/amount;
3. proses melalui mapping yang sama dengan webhook;
4. idempotent.

---

# 11. HALAMAN WEBSITE PUBLIK

# 11.1 Beranda

Urutan mobile-first:

1. header ringan;
2. hero;
3. form cek ketersediaan;
4. CTA Pesan Sekarang;
5. CTA WhatsApp;
6. preview tipe kamar;
7. fasilitas utama yang benar-benar tersedia;
8. alasan memilih penginapan;
9. lokasi/peta;
10. kebijakan utama;
11. footer.

Hero wajib:

- nama Penginapan Kelapa Sawit;
- deskripsi singkat;
- foto utama;
- harga mulai dari berdasarkan room type aktif termurah;
- lokasi Kota Bangun, Kalimantan Timur.

Form:

- check-in;
- check-out;
- jumlah tamu;
- tombol cek.

Server validation tetap wajib.

# 11.2 Daftar Kamar

Per room type:

- cover photo;
- nama;
- kapasitas;
- bed count/type;
- fasilitas;
- harga mulai;
- detail;
- cek ketersediaan.

Jangan tampilkan jumlah kamar kosong tanpa konteks tanggal bila user belum melakukan pencarian.

# 11.3 Detail Kamar

Tampilkan:

- galeri;
- deskripsi;
- fasilitas;
- kapasitas;
- tempat tidur;
- harga;
- kebijakan;
- booking form.

Jika user datang dari search, pertahankan query tanggal dan jumlah tamu.

# 11.4 Hasil Ketersediaan

Tampilkan hanya tipe kamar dengan room fisik kandidat.

Untuk setiap hasil:

- tanggal;
- jumlah malam;
- jumlah tamu;
- harga;
- total preview;
- jumlah unit tersedia bila diperlukan;
- tombol pilih.

Saat user klik pilih, jangan menganggap kamar final terkunci. Checkout akan memilih/assign physical room dan recheck.

Strategi assignment V1:

- sistem memilih kamar fisik tersedia pertama berdasarkan `sort_order`, lalu ID;
- user publik memilih tipe kamar, bukan nomor fisik;
- admin dapat melihat kamar fisik.

Sebelum booking insert:

- lock kandidat room satu per satu secara deterministik;
- pilih yang masih available.

Ini lebih baik untuk UX dan inventory daripada menampilkan “Twin 01/Twin 02” kepada guest.

# 11.5 Tentang

Konten admin-manageable melalui setting/content sederhana atau view yang mudah diubah.

# 11.6 Lokasi dan Kontak

Tampilkan:

- alamat;
- map embed/link;
- WhatsApp;
- petunjuk lokasi bila tersedia.

Jangan hardcode data yang belum pasti.

# 11.7 Kebijakan

Ambil `policy_versions` current.

# 11.8 Cek Booking

Dua jalur:

### Jalur Tautan Aman

- booking code + raw access token;
- raw token dibandingkan hash;
- rate limit;
- authorization scoped.

### Jalur Form Manual

- booking code;
- email atau WhatsApp;
- cocokkan normalized;
- rate limit ketat;
- respons generik agar tidak membocorkan keberadaan booking.

Jangan tampilkan data sensitif sebelum verifikasi.

---

# 12. AUTH DAN MEMBER

# 12.1 Register Email

Field:

- nama;
- email;
- WhatsApp;
- password;
- konfirmasi.

Rules:

- name required;
- email valid, normalized, unique;
- WhatsApp normalized;
- password mengikuti rule Laravel yang masuk akal;
- terms/privacy bila disediakan.

Kirim email verification.

# 12.2 Login Email

- rate limit;
- generic error;
- reject inactive account;
- regenerate session.

# 12.3 Forgot Password

Gunakan flow resmi Laravel.

# 12.4 Google OAuth

Flow:

1. click;
2. redirect provider;
3. state protection;
4. callback;
5. ambil provider ID dan email;
6. pastikan data yang diperlukan;
7. cari social account exact;
8. jika ada, login user;
9. jika tidak ada, cari user email normalized;
10. hanya link otomatis bila email provider telah terverifikasi sesuai data provider;
11. jika tidak aman, minta flow linking eksplisit;
12. buat social account;
13. login;
14. regenerate session.

Jangan menyimpan token jika tidak perlu.

# 12.5 Dashboard Member

Cards:

- nama;
- saldo poin;
- estimasi nilai Rupiah;
- booking aktif;
- CTA pesan kamar.

Menu:

- Dashboard;
- Booking Saya;
- Poin Saya;
- Profil.

Booking Saya:

- aktif;
- selesai;
- batal/expired.

Poin Saya:

- saldo;
- nilai;
- transaksi masuk;
- transaksi keluar;
- tanggal expired lot terdekat.

# 12.6 Profil

Boleh mengubah:

- nama;
- WhatsApp;
- avatar.

Perubahan email:

- minta password/re-auth bila akun password;
- kirim verifikasi ke email baru;
- jangan langsung menganggap verified.

---

# 13. ADMIN OPERATIONS

# 13.1 Dashboard

Statistik operasional:

- booking hari ini;
- check-in hari ini;
- check-out hari ini;
- kamar terisi saat ini;
- kamar tersedia saat ini;
- pending payment aktif;
- pendapatan bulan berjalan;
- booking terbaru;
- booking needs attention.

Setiap card harus dapat ditelusuri ke daftar relevan.

# 13.2 Kalender Kamar

Layout:

- baris = kamar fisik;
- kolom = tanggal;
- horizontal scroll;
- sticky room column;
- date range selector;
- status warna konsisten.

Status:

- available;
- pending;
- confirmed;
- checked-in;
- blocked.

Click cell/booking:

- detail;
- add booking;
- block room.

Calendar tidak boleh membuat ketersediaan sendiri; gunakan service/query yang sama.

# 13.3 Reservasi

Filter:

- tanggal;
- status;
- source;
- payment status;
- room;
- guest.

Actions sesuai state:

- detail;
- continue payment info;
- cancel;
- check-in;
- check-out;
- complete;
- no-show;
- invoice;
- WhatsApp.

# 13.4 Booking Manual

Source:

- whatsapp;
- booking_com;
- agoda;
- traveloka;
- walk_in;
- phone;
- other.

Field:

- guest;
- WhatsApp;
- email;
- room;
- dates;
- guest count;
- source;
- price;
- payment status;
- payment method;
- notes.

Rules:

- tetap lock room;
- tetap overlap check;
- room block check;
- admin price override harus dicatat audit;
- OTA booking tidak mendapat point V1;
- source bukan website tidak wajib Midtrans.

# 13.5 Tamu

V1 boleh berupa customer view yang disusun dari:

- users;
- booking guest snapshots.

Jangan merge identitas otomatis hanya dari nama.

Tampilkan:

- nama;
- email;
- WhatsApp;
- jumlah booking;
- booking terakhir;
- status member.

# 13.6 Pembayaran

Tampilkan:

- booking;
- provider;
- order ID;
- status;
- nominal;
- metode;
- waktu;
- reconciliation;
- refund.

Jangan tampilkan secret.

# 13.7 Check-in

Syarat:

- booking confirmed;
- tanggal relevan atau admin override dengan audit;
- warning bila belum lunas;
- jika tetap check-in unpaid untuk booking manual, butuh konfirmasi admin dan audit.

Simpan `checked_in_at`.

# 13.8 Check-out

Syarat:

- checked_in.

Simpan `checked_out_at`.

# 13.9 Complete

Setelah checked_out:

- admin/system menandai completed;
- LoyaltyPointService dipanggil idempotent.

Jangan award pada check-out jika belum completed sesuai rule.

# 13.10 Cancellation

Simpan:

- timestamp;
- reason;
- notes;
- actor.

Jika pending:

- release hold secara status;
- release promo;
- reverse point redemption.

Jika paid:

- jangan otomatis refund;
- tampilkan status `refund required?`/attention sesuai policy.

# 13.11 Refund

Admin flow:

1. pilih payment paid;
2. masukkan amount;
3. validate max refundable;
4. reason;
5. create refund requested;
6. proses provider;
7. simpan response;
8. update payment status;
9. bila earn point sudah terjadi, evaluasi reversal;
10. audit.

# 13.12 Promo

CRUD dengan:

- code;
- type;
- value;
- date;
- minimum;
- max discount;
- quota;
- active.

Preview validasi.

# 13.13 Loyalty Admin

Boleh:

- lihat saldo;
- ledger;
- expiry;
- adjustment.

Adjustment:

- angka positif/negatif;
- reason wajib;
- admin actor;
- transaction baru;
- jangan edit transaksi lama.

# 13.14 Room Block

Reason categories sesuai tabel.

Conflict booking harus ditampilkan.

# 13.15 Galeri

Upload aman, sort, active.

# 13.16 Kebijakan

Create version baru, preview, publish.

Publish current harus transaction.

# 13.17 Pengeluaran

CRUD sederhana + receipt.

# 13.18 Pengaturan

Group:

- general;
- contact;
- booking;
- loyalty;
- whatsapp;
- seo.

Secret payment/OAuth tidak boleh ada di UI setting.

---

# 14. LAPORAN

# 14.1 Laporan Reservasi

Filter:

- tanggal booking;
- stay date;
- status;
- source.

Output:

- count;
- total;
- detail.

# 14.2 Laporan Pendapatan

Pisahkan:

- website;
- WhatsApp;
- OTA;
- walk-in;
- phone;
- other.

Definisikan pendapatan dari payment/booking yang benar-benar recognized. Jangan menghitung pending sebagai pendapatan.

# 14.3 Tingkat Hunian

Formula:

`occupied room nights / available room nights × 100%`

Available room nights harus mempertimbangkan room inactive dan room block sesuai definisi laporan.

Dokumentasikan definisi agar angka konsisten.

# 14.4 Pembayaran

Filter:

- provider;
- status;
- date;
- payment type.

# 14.5 Loyalty

- earned;
- redeemed;
- expired;
- adjustment;
- reversal;
- outstanding balance.

# 14.6 Sumber Booking

- count;
- revenue;
- average booking value.

# 14.7 Estimasi Laba Bersih

`Pendapatan yang diakui - Pengeluaran = Estimasi Laba Bersih`

Tampilkan disclaimer:

> Angka ini merupakan estimasi operasional, bukan laporan akuntansi resmi.

---

# 15. INVOICE PDF

Invoice berisi:

- logo/nama;
- alamat/contact bila tersedia;
- invoice number;
- booking code;
- guest;
- room type snapshot;
- room snapshot;
- check-in/out;
- nights;
- price/night snapshot;
- subtotal;
- promo discount;
- points redeemed dan discount;
- total;
- payment method/status;
- transaction date;
- status booking.

Rules:

- invoice number unique;
- authorization wajib;
- member hanya booking miliknya;
- guest perlu token/verifikasi;
- admin boleh semua;
- data dari snapshot;
- jangan memuat internal notes.

---

# 16. WHATSAPP

V1 direct link.

Templates:

- hubungi penginapan;
- kirim ringkasan booking;
- konfirmasi;
- reminder manual.

Nomor:

- ambil dari setting;
- normalisasi ke format link internasional;
- encode message.

Jangan otomatis mengirim melalui API berbayar.

Jangan memasukkan guest access token ke pesan yang tidak perlu.

---

# 17. KEBIJAKAN DAN PERSETUJUAN

Sebelum booking dibuat:

checkbox required:

`Saya telah membaca dan menyetujui kebijakan penginapan.`

Backend:

- checkbox harus true;
- current policy wajib ada;
- simpan policy_version_id;
- simpan accepted_at.

Jika admin belum publish policy:

- website jangan diam-diam membuat booking tanpa policy;
- tampilkan konfigurasi belum lengkap pada admin;
- public booking dapat ditutup sampai policy tersedia.

---

# 18. SECURITY REQUIREMENTS

# 18.1 CSRF

Gunakan CSRF untuk seluruh form web kecuali webhook provider yang memang bukan session form. Webhook memiliki verifikasi sendiri.

# 18.2 XSS

- output Blade escaped default;
- jangan gunakan raw HTML dari guest;
- sanitize policy content jika rich text;
- alt/title escaped.

# 18.3 Mass Assignment

- `$fillable`/DTO terkontrol;
- jangan `$request->all()` ke model finansial;
- assign field sensitif secara eksplisit.

# 18.4 Authorization

Gunakan:

- middleware auth;
- guard admin;
- policy;
- ownership checks.

Test IDOR:

- member A tidak bisa membuka booking B;
- guest token A tidak bisa membuka booking B;
- admin route tidak bisa diakses member.

# 18.5 Rate Limiting

Terapkan pada:

- login;
- register;
- forgot password;
- cek booking;
- claim;
- availability abusive requests;
- webhook secara hati-hati agar provider tidak diblokir normal.

# 18.6 Webhook Security

- signature;
- amount;
- order;
- idempotency;
- logs;
- server status fallback.

# 18.7 OAuth State

Gunakan flow stateful default kecuali ada alasan resmi untuk stateless. Jangan mematikan state protection hanya agar callback “berhasil”.

# 18.8 Password

- hash bawaan Laravel;
- jangan log;
- jangan kirim plain text;
- production admin password tidak di-seed default.

# 18.9 Signed URL dan Token

- random cryptographic token;
- simpan hash;
- expiry;
- one-time untuk claim;
- jangan pakai ID mentah.

# 18.10 File Upload

Allowed MIME sesuai kebutuhan, misalnya:

- JPEG;
- PNG;
- WebP;
- PDF/image untuk receipt jika diizinkan.

Batas ukuran yang masuk akal.

- random filename;
- storage path terkontrol;
- jangan percaya original filename;
- jangan izinkan executable;
- image processing opsional dengan library aman.

# 18.11 Secret

`.env` / secret manager:

- APP_KEY;
- DB credentials;
- MIDTRANS keys;
- Google secret;
- mail credentials.

Tidak boleh:

- commit `.env`;
- print secret ke debug;
- simpan secret di database setting;
- kirim Server Key ke browser.

# 18.12 Audit

Audit aksi sensitif yang telah disebutkan.

# 18.13 Logging

Jangan log:

- password;
- full OAuth token;
- raw guest access token;
- full claim token;
- Server Key;
- Client Secret;
- data kartu.

# 18.14 Error Handling

Public:

- pesan aman dan mudah dipahami.

Log internal:

- context ID;
- booking code;
- provider order ID;
- exception tanpa secret.

---

# 19. UI/UX

## 19.1 Tema

- hijau alami;
- putih;
- netral hangat;
- modern;
- profesional;
- tidak berlebihan.

## 19.2 Mobile First

Prioritas:

- form tanggal mudah;
- CTA besar;
- booking summary jelas;
- sticky bottom CTA boleh digunakan;
- calendar admin dapat scroll.

## 19.3 Form Checkout

Jangan satu halaman sangat panjang tanpa struktur.

Kelompok:

1. detail menginap;
2. data tamu;
3. promo/poin bila member;
4. kebijakan;
5. ringkasan pembayaran.

## 19.4 Rupiah

Gunakan format:

`Rp150.000`

Jangan menampilkan decimal yang tidak perlu.

## 19.5 Empty/Error State

Wajib ada untuk:

- no availability;
- payment pending;
- payment failed;
- booking expired;
- no points;
- no history;
- no room image.

---

# 20. SEO

Implementasi:

- meta title;
- meta description;
- Open Graph;
- favicon;
- canonical URL bila diperlukan;
- sitemap;
- robots;
- heading semantic;
- alt text;
- clean slug.

Halaman booking guest yang sensitif:

- `noindex`;
- jangan masuk sitemap.

---

# 21. TESTING STRATEGY

# 21.1 Unit Test

Wajib:

1. night calculation;
2. overlap boundary;
3. pricing;
4. percentage promo;
5. fixed promo;
6. max discount;
7. point earn floor;
8. point redemption value;
9. minimum redeem;
10. maximum 20%;
11. point expiry selection;
12. status transition rules.

# 21.2 Feature Test

Wajib:

- guest booking;
- member booking;
- no login requirement;
- capacity rejection;
- inactive room rejection;
- room block;
- pending hold;
- hold expiry;
- continue payment;
- webhook duplicate;
- invalid signature;
- amount mismatch;
- out-of-order webhook;
- payment success;
- late payment after expiry;
- Google login mock;
- email auth;
- claim;
- member ownership;
- check-in;
- checkout;
- complete;
- point award once;
- point redemption reversal;
- promo quota concurrency;
- manual booking.

# 21.3 Critical Concurrency Test

Skenario:

- dua request;
- same room;
- same dates;
- run concurrently.

Expected:

- one success;
- one conflict;
- one blocking booking only.

Jangan hanya menjalankan dua request sequential dan menyebutnya concurrency test.

# 21.4 Database-Specific Test

Karena `lockForUpdate` memiliki perilaku database-dependent, critical locking test harus dijalankan terhadap MySQL test database, bukan hanya SQLite in-memory.

# 21.5 Payment Test

Mock Midtrans pada automated tests.

Sandbox manual:

- pending;
- settlement;
- expire;
- cancel/deny;
- duplicate notification;
- notification delayed.

# 21.6 Final Regression

- `php artisan test`;
- frontend build;
- route list;
- migration status;
- scheduler list;
- logs.

---

# 22. SEEDER

## 22.1 Development Admin

Env:

- `DEV_ADMIN_NAME`;
- `DEV_ADMIN_EMAIL`;
- `DEV_ADMIN_PASSWORD`.

Rules:

- seeder production tidak boleh punya password fallback;
- bila environment production dan credential dev ditemukan, abort/warn keras sesuai policy.

## 22.2 Room Type

`Twin`

Data unknown:

- capacity;
- bed type;
- base price.

Jika belum dikonfirmasi, gunakan placeholder yang jelas dan jangan publish otomatis.

## 22.3 Rooms

- Twin 01;
- Twin 02.

## 22.4 Facilities

Jangan mengarang.

Buat hanya berdasarkan data yang benar-benar dikonfirmasi.

---

# 23. ENVIRONMENT DAN SETUP

Contoh `.env.example` yang harus disiapkan tanpa nilai rahasia:

```dotenv
APP_NAME="Penginapan Kelapa Sawit"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

APP_TIMEZONE=Asia/Makassar

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=penginapan_kelapa_sawit
DB_USERNAME=
DB_PASSWORD=

MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=

MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"

DEV_ADMIN_NAME=
DEV_ADMIN_EMAIL=
DEV_ADMIN_PASSWORD=
```

Catatan:

- sesuaikan nama variable dengan SDK/config;
- jangan expose Server Key;
- Sandbox default;
- `.env.example` tidak berisi key nyata.

## 23.1 Setup Checklist

- Composer install;
- npm install;
- copy env;
- app key;
- DB create;
- migration;
- seeder;
- storage link;
- frontend build;
- scheduler;
- mail;
- Midtrans sandbox;
- Google OAuth.

## 23.2 Scheduler Production

Server cron:

- panggil `schedule:run` setiap menit sesuai dokumentasi Laravel;
- pastikan timezone bisnis benar.

Task:

- expire booking holds;
- expire points;
- payment reconciliation;
- optional cleanup.

## 23.3 Queue

Gunakan queue untuk:

- email;
- PDF generation berat bila perlu;
- non-critical notification.

Jangan menaruh authoritative booking lock atau payment confirmation hanya di queue tanpa alasan dan idempotency.

---

# 24. MIDTRANS SANDBOX-FIRST POLICY

Urutan wajib:

1. setup Sandbox;
2. create Snap token;
3. open Snap;
4. test pending;
5. test settlement;
6. test expire;
7. test cancel/deny;
8. test duplicate webhook;
9. test delayed webhook;
10. test status API;
11. test amount mismatch;
12. test invalid signature;
13. baru siapkan production config.

Production activation memerlukan checklist terpisah dan secret production.

---

# 25. ATURAN KERJA KIRO

## 25.1 Sebelum Coding

Wajib:

1. baca struktur project;
2. baca file terkait;
3. cari implementasi serupa;
4. pahami naming convention;
5. cek test existing;
6. cek fitur yang dapat rusak;
7. buat rencana perubahan.

## 25.2 Saat Coding

Wajib:

- konsisten;
- tidak duplikasi logic;
- reusable component;
- transaction kritis;
- error handling;
- Form Request;
- authorization;
- test.

## 25.3 Setelah Coding

Wajib:

- jalankan test target;
- jalankan regression relevan;
- migration status;
- route check;
- frontend build;
- log check;
- ringkas file berubah;
- catat risiko/remaining issue.

## 25.4 Aturan Feature Spec

Untuk fitur kompleks:

1. identifikasi Spec aktif;
2. baca `requirements`, `design`, dan `tasks` Spec;
3. pastikan task saat ini jelas;
4. jangan mengerjakan task lain diam-diam;
5. jika menemukan dependency tersembunyi, dokumentasikan dan perbarui task plan;
6. jika design tidak lagi sesuai project nyata, perbaiki design sebelum implementasi besar;
7. tandai task selesai hanya setelah test dan acceptance criteria terpenuhi.

Jangan membuat Spec hanya untuk formalitas. Artifact Spec harus benar-benar menjadi kontrol scope dan traceability.

## 25.5 Aturan Steering

Sebelum perubahan arsitektur besar:

- baca Steering yang relevan;
- cek apakah keputusan baru bertentangan dengan Steering;
- jika keputusan resmi berubah, update Steering;
- jangan memasukkan detail sementara ke Steering;
- jangan memasukkan secret atau data pribadi.

Steering tidak menggantikan pembacaan source code aktual.

## 25.6 Aturan Hooks

Hook harus:

- memiliki tujuan jelas;
- scope file/event sempit;
- tidak destructive;
- tidak menyembunyikan side effect;
- mudah dinonaktifkan saat debugging.

Jangan membuat Hook hanya karena fitur tersebut tersedia.

## 25.7 Aturan Parallel Work dan Subagent

Jika Kiro menggunakan pekerjaan paralel:

- pembagian scope harus tidak tumpang tindih;
- satu agent tidak boleh mengubah migration yang sama dengan agent lain;
- hasil paralel harus direview sebelum merge;
- test integrasi wajib dijalankan setelah hasil digabung;
- area booking, payment, webhook, loyalty, dan locking harus memiliki jalur implementasi yang terkendali.

## 25.8 Larangan

Jangan:

- hapus fitur existing tanpa alasan;
- hardcode secret;
- percaya harga frontend;
- percaya payment status frontend;
- award point dua kali;
- award sebelum completed;
- izinkan double booking;
- insert booking tanpa overlap check;
- wajibkan akun;
- campur booking/payment status;
- claim berdasarkan nama;
- hapus loyalty history;
- ubah booking lama mengikuti harga baru;
- anggap JS callback bukti pembayaran;
- gabungkan promo+poin V1.


# FASE 0 — AUDIT DAN PERSIAPAN

> **Mode Kiro:** Project Audit + Steering Preparation + SPEC 01 Preparation. Belum boleh mengimplementasikan fitur aplikasi.

## Tujuan Fase

Menetapkan kondisi nyata project sebelum satu file pun diubah. Fase ini mencegah Kiro mengasumsikan versi framework, menimpa pola existing, atau memasang dependency yang tidak kompatibel. Fase ini juga menyiapkan Steering yang benar dan scope SPEC 01 tanpa langsung menulis fitur.

## Exit Gate Fase

Ada catatan kondisi awal, versi environment telah diverifikasi, backup/branch aman tersedia, dependency risk diketahui, test baseline tercatat, Steering telah dibuat/disesuaikan berdasarkan project nyata, dan SPEC 01 telah memiliki requirements/design/tasks yang siap direview. Tidak ada fitur aplikasi yang diimplementasikan sebelum exit gate ini.


## TASK 0.1 — Audit Project dan Environment

### Tujuan

Membaca seluruh project dan menentukan apakah sistem dibangun dari kosong atau dikembangkan di atas kode existing.

### Kondisi Sebelum Perubahan

Belum ada asumsi yang boleh dibuat tentang versi Laravel, PHP, package, auth, database, atau frontend.

### File yang Harus Dibaca

- `composer.json` dan `composer.lock`
- `package.json` dan lock file npm
- seluruh `routes/`
- `bootstrap/app.php` dan config utama
- seluruh migration
- model, controller, service, middleware, policy, provider
- layout/view/component utama
- test existing
- README/dokumentasi project

### File yang Dibuat

- `docs/PROJECT_AUDIT.md` hanya jika project mempunyai folder docs atau dokumentasi internal sesuai convention

### File yang Diubah

- Tidak ada source code aplikasi yang diubah pada task audit, kecuali file dokumentasi internal

### Database yang Terlibat

- Baca konfigurasi koneksi; jangan mengubah schema

### Detail Implementasi

Catat secara eksplisit:

1. versi Laravel aktual;
2. versi PHP aktual dan extension penting;
3. versi MySQL;
4. package auth;
5. frontend stack;
6. middleware dan guard;
7. struktur database;
8. service pattern;
9. test runner;
10. status migration;
11. apakah project sudah memiliki booking/payment/member/admin;
12. konflik nama tabel yang mungkin terjadi;
13. code style dan naming convention;
14. semua fitur existing yang tidak boleh rusak.

Jika project kosong, tulis bahwa tidak ada legacy constraint. Jika project existing, buat matriks “pertahankan / modifikasi / deprecate nanti” tanpa menghapus apa pun.

### Business Rules

- Jangan mengubah framework hanya karena dokumen menargetkan Laravel 13.
- Versi Laravel dipilih berdasarkan compatibility nyata.
- Fitur existing yang bekerja adalah constraint sampai terbukti harus diubah.

### Validation Rules

- Verifikasi command tersedia dan file dapat dibaca.
- Jangan menulis secret ke dokumen audit.

### Security Considerations

- Redact credential dari output audit.
- Jangan menjalankan command destructive.

### Edge Cases

- Project tidak memiliki test.
- Migration gagal karena environment belum lengkap.
- Terdapat kode booking/payment partial yang tidak terdokumentasi.

### Testing

- Jalankan test baseline jika tersedia.
- Jalankan route list secara read-only.
- Jalankan migration status tanpa migrate.
- Jalankan build check bila dependency sudah terpasang.

### Acceptance Criteria

- Laporan audit menjelaskan kondisi nyata.
- Tidak ada file penting terhapus.
- Baseline error tercatat sebelum perubahan.

### Checklist

- [ ] Versi framework terkonfirmasi
- [ ] Versi PHP terkonfirmasi
- [ ] Database terkonfirmasi
- [ ] Auth existing terkonfirmasi
- [ ] Test baseline dicatat
- [ ] Secret tidak tertulis


## TASK 0.2 — Siapkan Branch, Backup, dan Strategi Perubahan

### Tujuan

Memastikan implementasi dapat dilacak dan dipulihkan.

### Kondisi Sebelum Perubahan

Audit selesai dan scope awal diketahui.

### File yang Harus Dibaca

- status Git
- branch aktif
- `.gitignore`
- deployment notes bila ada

### File yang Dibuat

- Dokumen change log internal bila convention project memerlukannya

### File yang Diubah

- `.gitignore` hanya jika ada kekurangan nyata seperti `.env` atau file upload sensitif belum diabaikan

### Database yang Terlibat

- Tidak ada perubahan database.

### Detail Implementasi

Pastikan semua perubahan dikerjakan pada branch aman. Jangan membuat backup database produksi melalui command yang berisiko tanpa instruksi operator. Untuk environment lokal, catat cara rollback migration dan data fixture.

Buat strategi commit kecil per task. Setiap commit harus dapat menjelaskan satu perubahan utama dan test yang dijalankan.

### Business Rules

- Jangan bekerja langsung pada production branch bila workflow repo memiliki branch protection.
- Jangan commit `.env`, database dump sensitif, atau file upload tamu.

### Validation Rules

- Pastikan `.gitignore` benar.

### Security Considerations

- Backup tidak boleh mengekspos data personal.

### Edge Cases

- Repo belum menggunakan Git.
- Working tree sudah berisi perubahan user.

### Testing

- Cek `git status` sebelum dan setelah task.
- Pastikan file user existing tidak di-reset.

### Acceptance Criteria

- Strategi rollback jelas.
- Perubahan user existing tidak hilang.

### Checklist

- [ ] Branch aman
- [ ] Status Git dicatat
- [ ] `.env` tidak tracked
- [ ] Tidak ada reset destructive


## TASK 0.3 — Verifikasi Dependency Resmi dan Rencana Versi

### Tujuan

Menentukan dependency minimum yang benar sebelum instalasi.

### Kondisi Sebelum Perubahan

Versi environment sudah diketahui.

### File yang Harus Dibaca

- `composer.json`
- `package.json`
- dokumentasi resmi Laravel sesuai versi
- dokumentasi resmi Midtrans
- dokumentasi resmi Socialite

### File yang Dibuat

- Tidak wajib membuat file baru

### File yang Diubah

- `composer.json` dan `package.json` belum diubah pada task ini kecuali project kosong dan keputusan versi sudah final

### Database yang Terlibat

- Tidak ada.

### Detail Implementasi

Susun daftar dependency:

- framework Laravel;
- Socialite;
- Midtrans PHP SDK resmi;
- PDF engine;
- Alpine/Tailwind;
- package auth bila diperlukan.

Untuk setiap package, cek compatibility PHP/Laravel. Jangan memasang package Laravel 5 lama untuk Midtrans. Jangan memasang dua package yang menyelesaikan kebutuhan sama.

### Business Rules

- Utamakan package resmi dan aktif.
- Jumlah dependency harus minimal.
- Jangan upgrade major dependency unrelated di fase ini.

### Validation Rules

- Semua constraint versi harus dapat diselesaikan Composer/npm.

### Security Considerations

- Periksa advisories dan package abandoned bila tooling mendukung.

### Edge Cases

- Package existing mengunci versi lama.
- PDF package existing masih dipakai.

### Testing

- Lakukan dry reasoning dependency.
- Jika instalasi dilakukan, jalankan test baseline setelahnya.

### Acceptance Criteria

- Daftar dependency final konsisten dengan environment.
- Tidak ada package duplikat/obsolete yang ditambahkan.

### Checklist

- [ ] Laravel compatible
- [ ] Midtrans SDK resmi
- [ ] Socialite compatible
- [ ] PDF strategy jelas
- [ ] Tidak ada package berlebihan


## TASK 0.4 — Buat atau Sesuaikan Kiro Steering

### Tujuan

Membuat konteks persistent yang ringkas agar Kiro memahami produk, teknologi, struktur project, business rules, dan critical safety rules pada interaksi berikutnya.

### Kondisi Sebelum Perubahan

Audit project dan verifikasi environment selesai. Kiro sudah mengetahui apakah project kosong atau existing.

### File yang Harus Dibaca

- file master requirements ini;
- `docs/PROJECT_AUDIT.md` bila dibuat;
- `composer.json`;
- `package.json`;
- struktur `app/`, `routes/`, `database/`, `resources/`, `tests/`;
- `.kiro/steering/` jika sudah ada.

### File yang Dibuat

Jika belum ada dan sesuai hasil audit:

- `.kiro/steering/product.md`;
- `.kiro/steering/tech.md`;
- `.kiro/steering/structure.md`;
- `.kiro/steering/business-rules.md`;
- `.kiro/steering/critical-safety-rules.md`;
- `.kiro/steering/workflow.md`.

### File yang Diubah

Steering existing yang relevan, tanpa menghapus konteks user yang masih benar.

### Database yang Terlibat

Tidak ada perubahan schema atau data.

### Detail Implementasi

1. Baca Steering existing lebih dulu.
2. Jangan menyalin seluruh file master ke Steering.
3. Isi `product.md` hanya dengan konteks produk yang stabil.
4. Isi `tech.md` dengan stack aktual hasil audit, bukan versi yang diasumsikan.
5. Isi `structure.md` dengan struktur dan convention aktual.
6. Isi `business-rules.md` dengan invariant bisnis lintas fitur.
7. Isi `critical-safety-rules.md` dengan larangan dan kontrol risiko paling penting.
8. Isi `workflow.md` dengan proses kerja Spec-driven untuk project ini.
9. Pastikan semua Steering ringkas, konsisten, dan tidak saling bertentangan.
10. Jika project existing memiliki Steering, merge secara konservatif.

### Business Rules

- Master requirements tetap sumber kebutuhan lengkap.
- Steering adalah persistent context, bukan duplikasi master.
- Perubahan rule bisnis resmi harus memperbarui Steering terkait.

### Validation Rules

- Tidak boleh ada secret.
- Tidak boleh ada data pribadi tamu.
- Versi teknologi harus sesuai hasil audit.
- Tidak boleh ada rule yang bertentangan dengan dokumen master tanpa catatan keputusan.

### Security Considerations

- Redact credential.
- Jangan masukkan contoh key nyata.
- Jangan masukkan production URL privat atau token akses.

### Edge Cases

- Steering sudah ada dan memiliki aturan berbeda.
- Project multi-root.
- Project existing menggunakan struktur yang berbeda dari rekomendasi master.

### Testing

- Review manual setiap Steering.
- Cari duplikasi/kontradiksi.
- Pastikan file dapat dibaca Kiro dari workspace.

### Acceptance Criteria

- Kiro memiliki persistent context yang cukup untuk bekerja konsisten.
- Steering ringkas dan mencerminkan project nyata.
- Tidak ada secret atau data sensitif.

### Checklist

- [ ] product.md sesuai produk
- [ ] tech.md sesuai environment aktual
- [ ] structure.md sesuai source code aktual
- [ ] business-rules.md memuat invariant utama
- [ ] critical-safety-rules.md memuat kontrol risiko utama
- [ ] workflow.md memuat alur Spec-driven
- [ ] Tidak ada duplikasi master secara berlebihan
- [ ] Tidak ada secret


## TASK 0.5 — Siapkan SPEC 01 — Project Foundation

### Tujuan

Membuat Feature Spec pertama yang membatasi scope fondasi project sebelum coding dimulai.

### Kondisi Sebelum Perubahan

Audit selesai dan Steering telah disiapkan berdasarkan project nyata.

### File yang Harus Dibaca

- file master requirements ini;
- seluruh hasil audit;
- seluruh Steering;
- Fase 1 pada dokumen ini;
- source code fondasi yang relevan.

### File yang Dibuat

Artifact Kiro Feature Spec untuk:

`SPEC 01 — Project Foundation`

Secara konseptual mencakup:

- requirements;
- design;
- tasks.

### File yang Diubah

Tidak ada source code aplikasi.

### Database yang Terlibat

Hanya desain dan rencana migration. Jangan menjalankan migration pada task ini.

### Detail Implementasi

Scope SPEC 01 hanya boleh mencakup:

1. project bootstrap/configuration bila diperlukan;
2. timezone dan locale;
3. enum/status foundation;
4. state transition guard dasar;
5. autentikasi member email dasar;
6. autentikasi admin terpisah;
7. layout publik/member/admin;
8. authorization foundation;
9. test foundation;
10. environment/documentation foundation.

Jangan memasukkan:

- room inventory lengkap;
- public room pages;
- availability;
- booking;
- Midtrans;
- Google OAuth;
- loyalty;
- promo;
- admin calendar;
- reports.

Requirements harus dapat diuji. Design harus menyesuaikan project aktual. Tasks harus kecil dan mengikuti task Fase 1.

### Business Rules

- Fondasi tidak boleh mengunci project ke keputusan yang belum diperlukan.
- Admin dan member harus memiliki boundary authorization yang jelas.
- Secret hanya dari environment.

### Validation Rules

- Scope Spec tidak boleh melebar.
- Setiap task harus memiliki test/verification.
- Dependency antar task harus jelas.

### Security Considerations

- Auth boundary.
- Password hashing.
- Session security.
- CSRF.
- No public admin registration.
- No hardcoded credential.

### Edge Cases

- Project sudah memiliki auth.
- Project sudah memiliki Tailwind/layout.
- Project sudah menggunakan enum/status pattern sendiri.

### Testing

Pada task ini hanya review artifact Spec:

- requirement completeness;
- design consistency;
- task ordering;
- traceability ke Fase 1.

### Acceptance Criteria

- SPEC 01 siap diimplementasikan satu task pada satu waktu.
- Scope tidak mencakup fitur fase lanjut.
- Tidak ada source code fitur yang diubah pada task persiapan Spec.

### Checklist

- [ ] requirements siap
- [ ] design siap
- [ ] tasks siap
- [ ] scope terbatas ke foundation
- [ ] dependency jelas
- [ ] test strategy jelas
- [ ] belum ada implementasi fitur
- [ ] siap direview sebelum Fase 1



# FASE 1 — FONDASI PROJECT

> **Kiro Spec:** SPEC 01 — Project Foundation

## Tujuan Fase

Membuat fondasi aplikasi, konfigurasi, status enum, autentikasi dasar, layout, dan struktur layanan tanpa membangun booking engine lebih dulu.

## Exit Gate Fase

Project dapat dijalankan; auth member/admin terpisah; enum dan config tersedia; migration fondasi sukses; frontend build sukses.


## TASK 1.1 — Bootstrap Project dan Konfigurasi Dasar

### Tujuan

Menyiapkan project Laravel dan konfigurasi environment yang benar.

### Kondisi Sebelum Perubahan

Fase audit selesai. Jika project kosong, belum ada struktur aplikasi. Jika existing, struktur harus dipertahankan.

### File yang Harus Dibaca

- `composer.json`
- `package.json`
- config aplikasi
- `.env.example`
- Vite/Tailwind config

### File yang Dibuat

- Project Laravel bila benar-benar kosong
- `config/booking.php`
- `config/loyalty.php`
- `config/midtrans.php`

### File yang Diubah

- `.env.example`
- config timezone/app
- frontend entrypoint jika project kosong

### Database yang Terlibat

- Belum membuat tabel domain besar pada task ini.

### Detail Implementasi

Set `Asia/Makassar` sebagai zona waktu bisnis untuk kalkulasi jadwal dan tampilan. Gunakan `DATE` untuk tanggal stay sehingga perbedaan timezone tidak menggeser check-in/out.

Konfigurasikan default:

- booking hold minutes = 30;
- currency = IDR;
- loyalty earn divisor = 1000;
- point value = 50;
- minimum redeem = 100;
- max redemption percent = 20;
- expiry months = 18;
- eligible booking sources sebagai konfigurasi awal.

Midtrans default harus Sandbox.

Jangan menyimpan key nyata di config source; baca dari env.

### Business Rules

- Semua business constant terpusat.
- Timezone bisnis konsisten.
- Production tidak boleh default ke debug true atau Midtrans production tanpa explicit config.

### Validation Rules

- Nilai integer configuration harus memiliki default aman.
- Boolean env diparse dengan benar.

### Security Considerations

- Secret hanya env.
- Tidak ada key asli di repository.

### Edge Cases

- Environment tidak mendukung Laravel 13.
- Project existing sudah memiliki timezone convention berbeda.

### Testing

- Boot application.
- Config cache test.
- Frontend build.

### Acceptance Criteria

- Aplikasi dapat boot.
- Config dapat dibaca.
- Midtrans production false secara default.

### Checklist

- [ ] APP timezone benar
- [ ] Booking hold 30 menit
- [ ] Loyalty config benar
- [ ] Secret tidak hardcoded
- [ ] Build berhasil


## TASK 1.2 — Buat Enum dan State Transition Guard

### Tujuan

Memusatkan seluruh status dan sumber booking sebelum model domain dibuat.

### Kondisi Sebelum Perubahan

Config dasar tersedia; belum ada string status tersebar.

### File yang Harus Dibaca

- seluruh enum/constant existing
- model existing yang mempunyai status
- controller/service yang membandingkan status

### File yang Dibuat

- `app/Enums/BookingStatus.php`
- `app/Enums/PaymentStatus.php`
- `app/Enums/BookingSource.php`
- `app/Enums/LoyaltyTransactionType.php`
- `app/Enums/PromotionType.php`
- `app/Enums/PromotionUsageStatus.php`
- `app/Enums/RefundStatus.php`
- `app/Enums/RoomStatus.php`

### File yang Diubah

- Model existing jika cast perlu ditambahkan tanpa merusak data

### Database yang Terlibat

- Belum perlu schema baru.

### Detail Implementasi

Buat enum backed string. Tambahkan helper label hanya untuk presentation ringan.

Buat mekanisme transition map untuk booking, idealnya di enum atau dedicated state transition validator. Jangan membuat endpoint yang bisa mengubah status bebas.

Transisi harus mengikuti bagian status dokumen ini. Semua service nanti menggunakan validator yang sama.

### Business Rules

- Status tidak boleh berupa magic string baru.
- Transition invalid melempar domain exception.
- Terminal status tidak dapat berpindah tanpa workflow koreksi eksplisit.

### Validation Rules

- Input status dari request admin tidak diterima bebas.

### Security Considerations

- Authorization tetap di service/controller; enum tidak menggantikan policy.

### Edge Cases

- Data existing memiliki status lama yang tidak sama.
- Case sensitivity.

### Testing

- Unit test daftar allowed transition.
- Unit test invalid transition.

### Acceptance Criteria

- Seluruh status domain tersedia.
- Transition map teruji.

### Checklist

- [ ] Semua enum dibuat
- [ ] Label Indonesia tersedia bila perlu
- [ ] Invalid transition ditolak
- [ ] Test lulus


## TASK 1.3 — Autentikasi Member dan Admin Terpisah

### Tujuan

Menyediakan fondasi login email member dan login admin dengan guard terpisah.

### Kondisi Sebelum Perubahan

Belum ada auth atau auth existing telah diaudit.

### File yang Harus Dibaca

- `config/auth.php`
- route auth existing
- User model
- middleware auth
- login views

### File yang Dibuat

- `app/Models/Admin.php`
- migration `admins` bila belum ada
- admin auth controller/request
- admin login view
- middleware/guard config yang diperlukan

### File yang Diubah

- `config/auth.php`
- User model
- routes
- layout auth

### Database yang Terlibat

- users
- admins
- password reset tables sesuai framework

### Detail Implementasi

Member:

- register;
- login;
- logout;
- email verification;
- forgot/reset password.

Admin:

- login terpisah;
- route prefix misalnya `/admin`;
- guard `admin`;
- tidak ada public registration admin.

Jika menggunakan Fortify, gunakan backend resmi dan UI Blade custom. Jika existing auth sudah benar, integrasikan tanpa rewrite besar.

### Business Rules

- Guest booking tetap tidak bergantung pada auth.
- Inactive member/admin ditolak.
- Session diregenerasi saat login.
- Admin tidak dibuat melalui route publik.

### Validation Rules

- Email normalized dan unique.
- Password confirmed saat register.
- WhatsApp normalized.

### Security Considerations

- Rate limit login.
- CSRF.
- Hash password resmi Laravel.
- Generic auth error.

### Edge Cases

- Google-only user password NULL.
- Existing user dengan email case berbeda.

### Testing

- Register/login/logout member.
- Email verification.
- Password reset.
- Admin login.
- Member tidak dapat admin route.

### Acceptance Criteria

- Member auth berjalan.
- Admin guard terpisah.
- Guest public route tetap dapat diakses.

### Checklist

- [ ] Member register
- [ ] Member login
- [ ] Verify email
- [ ] Reset password
- [ ] Admin login
- [ ] Guard terpisah


## TASK 1.4 — Layout Publik, Member, dan Admin

### Tujuan

Membuat shell UI yang konsisten sebelum fitur domain.

### Kondisi Sebelum Perubahan

Auth dasar berjalan.

### File yang Harus Dibaca

- layout/view existing
- Tailwind config
- component existing

### File yang Dibuat

- `resources/views/layouts/public.blade.php`
- `resources/views/layouts/member.blade.php`
- `resources/views/layouts/admin.blade.php`
- komponen nav, alert, form error, button, badge

### File yang Diubah

- frontend CSS/JS entrypoint
- route placeholder terkontrol

### Database yang Terlibat

- Tidak ada.

### Detail Implementasi

Buat desain mobile-first dengan warna hijau alami, putih, netral hangat.

Public header:

- logo/nama;
- Kamar;
- Tentang;
- Lokasi;
- Kebijakan;
- Cek Booking;
- Login/member.

Admin sidebar:

- menu lengkap, tetapi item fitur belum jadi boleh disabled atau diarahkan ke placeholder jelas;
- jangan membuat fake data.

Buat komponen reusable untuk currency, status badge, empty state, validation error.

### Business Rules

- Bahasa Indonesia.
- CTA booking paling jelas.
- Tidak ada data fasilitas palsu.

### Validation Rules

- Semua link route harus valid atau disabled.

### Security Considerations

- Escape output.
- Jangan tampilkan admin nav ke public.

### Edge Cases

- Mobile nav.
- Long admin menu.
- No logo yet.

### Testing

- Responsive check.
- Keyboard focus.
- Frontend build.

### Acceptance Criteria

- Tiga area visual terpisah.
- Mobile nyaman.
- Tidak ada broken route.

### Checklist

- [ ] Public layout
- [ ] Member layout
- [ ] Admin layout
- [ ] Mobile nav
- [ ] Build lulus



# FASE 2 — TIPE KAMAR DAN KAMAR FISIK

> **Kiro Spec:** SPEC 02 — Room Management & Public Website

## Tujuan Fase

Membangun inventory dasar: tipe kamar, fasilitas, foto, dan kamar fisik. Tidak ada booking engine yang boleh dibuat sebelum inventory benar.

## Exit Gate Fase

Admin dapat mengelola tipe kamar dan kamar fisik; data Twin/Twin 01/Twin 02 tersedia; upload aman; kamar tidak dapat dihapus jika memiliki histori.


## TASK 2.1 — Schema dan Model Inventory Kamar

### Tujuan

Membuat struktur data room type, room, facility, pivot, dan room image.

### Kondisi Sebelum Perubahan

Fondasi project stabil dan belum ada domain inventory lengkap.

### File yang Harus Dibaca

- migration existing
- model existing
- enum RoomStatus
- storage config

### File yang Dibuat

- migration room_types
- migration rooms
- migration facilities
- migration room_type_facility
- migration room_images
- model terkait
- factory minimal untuk test

### File yang Diubah

- database seeder registry

### Database yang Terlibat

- room_types
- rooms
- facilities
- room_type_facility
- room_images

### Detail Implementasi

Implementasikan schema sesuai rancangan database. Tambahkan cast enum/status dan relasi.

Pastikan room type menyimpan base price dan kapasitas. Room menyimpan unit fisik. Jangan menaruh `jumlah_kamar` sebagai satu-satunya inventory pada room type.

Buat scope active/sellable. Jangan membangun availability query di task ini.

### Business Rules

- Satu room belongs to satu room type.
- Room code unique.
- Room type slug unique.
- Harga integer Rupiah.

### Validation Rules

- Capacity >= 1.
- Bed count >= 1.
- Base price >= 0.
- Name/code length dibatasi.

### Security Considerations

- Mass assignment eksplisit.
- Foreign key restrict pada master yang dipakai.

### Edge Cases

- Existing schema memiliki kolom serupa.
- Rename room type setelah booking nanti tidak boleh memengaruhi snapshot.

### Testing

- Migration fresh test.
- Model relation test.
- Unique constraint test.

### Acceptance Criteria

- Schema sesuai dokumen.
- Relasi bekerja.
- Tidak ada kolom inventory aggregate yang menggantikan kamar fisik.

### Checklist

- [ ] Migration lulus
- [ ] Model relasi benar
- [ ] Unique constraint aktif
- [ ] Money integer


## TASK 2.2 — Admin CRUD Tipe Kamar, Fasilitas, dan Foto

### Tujuan

Membuat admin dapat mengelola produk kamar tanpa menyentuh database langsung.

### Kondisi Sebelum Perubahan

Schema inventory sudah ada.

### File yang Harus Dibaca

- admin layout
- RoomType model
- Facility model
- RoomImage model
- upload pattern existing

### File yang Dibuat

- admin RoomTypeController
- Form Request create/update
- policy bila diperlukan
- views index/create/edit
- image upload service/helper bila belum ada

### File yang Diubah

- admin routes
- admin sidebar

### Database yang Terlibat

- room_types
- facilities
- room_type_facility
- room_images
- audit_logs bila sudah tersedia

### Detail Implementasi

CRUD room type harus mendukung:

- nama;
- slug otomatis namun dapat distabilkan;
- deskripsi;
- kapasitas;
- bed count/type;
- base price;
- active;
- fasilitas;
- multi-image;
- cover;
- sort order.

Upload foto dilakukan setelah validasi. Jika database save gagal, file baru dibersihkan. Jika mengganti/hapus foto, jangan hapus file lama sebelum perubahan DB dipastikan berhasil.

Tipe kamar yang sudah dipakai booking tidak boleh hard-delete; nonaktifkan.

### Business Rules

- Hanya room type active yang tampil publik.
- Satu cover efektif per room type.
- Harga baru hanya untuk booking baru.

### Validation Rules

- Image MIME JPEG/PNG/WebP.
- Batas ukuran masuk akal.
- Facility IDs harus valid.

### Security Considerations

- Admin guard.
- CSRF.
- Random filename.
- No executable upload.

### Edge Cases

- No image.
- Cover image deleted.
- Duplicate slug.
- Facility deactivated.

### Testing

- CRUD feature test.
- Unauthorized access.
- Upload validation.
- Existing image preservation on failed update.

### Acceptance Criteria

- Admin dapat mengelola tipe kamar.
- Upload aman.
- Histori tidak terpengaruh.

### Checklist

- [ ] CRUD room type
- [ ] Facility assignment
- [ ] Multi image
- [ ] Cover
- [ ] Security upload


## TASK 2.3 — Admin CRUD Kamar Fisik dan Seeder Awal

### Tujuan

Membuat dua kamar Twin sebagai inventory nyata dan menyediakan CRUD future-proof.

### Kondisi Sebelum Perubahan

Room type management tersedia.

### File yang Harus Dibaca

- Room model
- RoomType model
- admin layout
- seeder existing

### File yang Dibuat

- admin RoomController
- Room Store/Update Request
- views room
- RoomSeeder/initial business seeder

### File yang Diubah

- routes
- sidebar
- DatabaseSeeder

### Database yang Terlibat

- rooms
- room_types

### Detail Implementasi

Admin dapat membuat/edit:

- room type;
- code;
- name;
- floor optional;
- notes internal;
- status;
- active;
- sort order.

Seeder:

- room type Twin;
- Twin 01;
- Twin 02.

Jangan mengisi harga, kapasitas, fasilitas yang belum pasti secara diam-diam. Jika perlu placeholder, set inactive sampai admin mengisi data final.

### Business Rules

- Room tidak hard-delete jika memiliki booking.
- Inactive room tidak dapat dipesan.
- Room type inactive membuat room tidak sellable.

### Validation Rules

- Code/name unique.
- Room type valid.
- Status enum valid.

### Security Considerations

- Admin-only.
- Audit perubahan status room.

### Edge Cases

- Room type inactive.
- Room dengan future booking ingin dinonaktifkan.
- Duplicate code.

### Testing

- CRUD test.
- Seeder idempotent atau aman pada environment yang ditargetkan.
- Delete restriction.

### Acceptance Criteria

- Twin 01 dan Twin 02 tersedia.
- Admin dapat menambah kamar di masa depan.

### Checklist

- [ ] Twin type ada
- [ ] Twin 01 ada
- [ ] Twin 02 ada
- [ ] CRUD room
- [ ] Delete aman



# FASE 3 — WEBSITE PUBLIK

> **Kiro Spec:** SPEC 02 — Room Management & Public Website

## Tujuan Fase

Membangun halaman pemasaran dan informasi yang memakai data nyata dari database/settings tanpa terlebih dahulu mengaktifkan transaksi booking.

## Exit Gate Fase

Beranda, daftar kamar, detail kamar, tentang, lokasi, kontak, kebijakan, dan SEO dasar bekerja secara mobile-first.


## TASK 3.1 — Settings Publik dan Konten Dasar

### Tujuan

Menyediakan sumber data bisnis publik yang dapat diubah tanpa hardcode berulang.

### Kondisi Sebelum Perubahan

Layout publik dan inventory kamar tersedia.

### File yang Harus Dibaca

- settings schema bila sudah dibuat
- public layout
- config/app
- existing content management pattern

### File yang Dibuat

- migration settings jika belum dibuat
- Setting model/service/repository ringan
- admin SettingsController dan request
- views settings groups

### File yang Diubah

- public layout
- admin routes/sidebar

### Database yang Terlibat

- settings
- audit_logs bila tersedia

### Detail Implementasi

Buat setting group:

- general: nama penginapan, short description;
- contact: WhatsApp, email, address;
- location: map URL/embed/coordinates jika diizinkan;
- booking: check-in time, check-out time;
- whatsapp: template dasar;
- seo: title, description, OG image.

Buat API/helper pembacaan setting yang tidak query database berulang pada setiap partial. Cache boleh digunakan dengan invalidasi saat update.

Secret tidak boleh disimpan di settings.

### Business Rules

- Unknown value boleh kosong dan UI harus graceful.
- Public hanya membaca setting `is_public` atau key whitelist.
- Perubahan setting penting diaudit.

### Validation Rules

- URL valid.
- Time format valid.
- WhatsApp normalized.
- Text length bounded.

### Security Considerations

- Admin-only edit.
- Escape output.
- No secret fields.

### Edge Cases

- Setting kosong.
- Cache stale.
- Nomor WhatsApp belum diisi.

### Testing

- Admin settings feature test.
- Public setting read test.
- Cache invalidation test bila cache digunakan.

### Acceptance Criteria

- Data publik tidak tersebar sebagai hardcode.
- Secret tidak dapat dimasukkan melalui admin setting.

### Checklist

- [ ] Group setting tersedia
- [ ] Admin edit bekerja
- [ ] Cache aman
- [ ] Public fallback aman


## TASK 3.2 — Beranda, Daftar Kamar, dan Detail Kamar

### Tujuan

Membuat pengalaman publik yang menonjolkan booking dan informasi kamar.

### Kondisi Sebelum Perubahan

Data room type, room image, facility, dan settings tersedia.

### File yang Harus Dibaca

- public layout
- RoomType model
- RoomImage model
- Facility model
- settings helper

### File yang Dibuat

- Public HomeController
- Public RoomController
- views home
- views room index/detail
- public components room card, facility list, CTA

### File yang Diubah

- public routes
- public navigation

### Database yang Terlibat

- room_types
- room_images
- facilities
- settings
- galleries bila tersedia

### Detail Implementasi

Beranda wajib berisi seluruh blok minimum. Harga mulai dari dihitung dari room type active termurah, bukan hardcode.

Daftar kamar hanya menampilkan tipe aktif. Detail room menggunakan slug.

Form availability pada beranda/detail untuk sekarang boleh mengarah ke route pencarian yang akan diimplementasikan fase 4. Jangan membuat hasil palsu.

Jika foto belum ada, gunakan placeholder visual netral yang jelas, bukan foto fasilitas yang tidak nyata.

### Business Rules

- Hanya active room type.
- Harga Rupiah.
- Jangan klaim fasilitas yang tidak ada.
- CTA booking jelas.

### Validation Rules

- Slug valid.
- Room type inactive menghasilkan 404 atau tidak tersedia.

### Security Considerations

- No raw HTML guest.
- No hidden admin data.

### Edge Cases

- No image.
- No active room type.
- Base price 0 placeholder.

### Testing

- Public page 200.
- Inactive room hidden.
- Mobile responsive manual check.

### Acceptance Criteria

- Beranda lengkap.
- Room list/detail memakai DB.
- Tidak ada broken CTA.

### Checklist

- [ ] Hero
- [ ] Availability form
- [ ] Room preview
- [ ] Room list
- [ ] Room detail
- [ ] Mobile check


## TASK 3.3 — Halaman Informasi, Kebijakan, SEO, dan Galeri Dasar

### Tujuan

Melengkapi halaman publik dan struktur SEO.

### Kondisi Sebelum Perubahan

Public core pages berjalan.

### File yang Harus Dibaca

- settings
- public layout
- existing SEO package/pattern

### File yang Dibuat

- views Tentang
- views Lokasi & Kontak
- views Kebijakan
- meta component
- sitemap route/controller bila diperlukan
- migration/model policy_versions bila belum ada
- migration/model galleries bila belum ada

### File yang Diubah

- routes
- footer
- head/meta

### Database yang Terlibat

- policy_versions
- galleries
- settings

### Detail Implementasi

Kebijakan dibaca dari current policy version. Jika belum ada current policy, tampilkan pesan belum tersedia dan blokir booking transaksi pada fase berikutnya.

SEO:

- title per page;
- description;
- OG;
- canonical sesuai kebutuhan;
- favicon;
- heading hierarchy;
- image alt;
- sitemap hanya public indexable pages.

Guest booking access pages harus noindex nanti.

### Business Rules

- Satu current policy per key.
- Versi lama immutable setelah dipakai.
- Sitemap tidak memuat URL bertoken.

### Validation Rules

- Policy publish requires title/content/version.
- Gallery upload rules aman.

### Security Considerations

- Sanitize rich policy content.
- No token in sitemap/log.

### Edge Cases

- No current policy.
- No gallery.
- Map setting kosong.

### Testing

- Policy current display test.
- Sitemap content test.
- Meta rendering check.

### Acceptance Criteria

- Semua halaman informasi tersedia.
- SEO dasar benar.
- Booking dapat mengetahui policy current.

### Checklist

- [ ] About
- [ ] Location/contact
- [ ] Policy
- [ ] Meta
- [ ] Sitemap
- [ ] No sensitive URL



# FASE 4 — AVAILABILITY ENGINE

> **Kiro Spec:** SPEC 03 — Availability & Guest Booking Engine

## Tujuan Fase

Membangun mesin ketersediaan kamar fisik sebagai fondasi semua kanal booking dan kalender.

## Exit Gate Fase

Search availability benar di batas tanggal, room block diperhitungkan, pending hold valid diperhitungkan, dan unit/feature test lulus.


## TASK 4.1 — Implementasi AvailabilityService

### Tujuan

Membuat satu sumber logika availability yang dipakai publik dan admin.

### Kondisi Sebelum Perubahan

Inventory kamar tersedia; belum ada query overlap terpusat.

### File yang Harus Dibaca

- Room/RoomType model
- enum BookingStatus
- config booking
- schema bookings/room_blocks bila sudah ada

### File yang Dibuat

- `app/Services/AvailabilityService.php`
- domain exception availability
- unit/feature tests

### File yang Diubah

- model scopes hanya bila benar-benar reusable

### Database yang Terlibat

- rooms
- room_types
- bookings
- room_blocks

### Detail Implementasi

Implementasikan method yang didefinisikan pada bagian service.

Query harus:

- mulai dari room aktif dengan room type aktif;
- filter capacity;
- exclude booking overlap blocking;
- pending hanya memblokir jika belum expired;
- exclude room block overlap;
- menerima `excludeBookingId` untuk reschedule/admin verification.

Gunakan tanggal sebagai immutable value object/CarbonImmutable bila convention mendukung.

Jangan memilih room secara random. Urutan deterministik: `sort_order`, lalu ID.

### Business Rules

- Interval `[check_in, check_out)`.
- Search result tidak mengunci kamar.
- Checked-out/completed tidak blocking menurut aturan V1.

### Validation Rules

- Check-out setelah check-in.
- Guest count >= 1.
- Range maksimum pencarian dapat dibatasi untuk anti-abuse.

### Security Considerations

- Query parameter bound.
- No raw SQL interpolation.

### Edge Cases

- Checkout date sama dengan new check-in.
- Pending expired belum diproses scheduler.
- Room inactive.
- Room type inactive.

### Testing

- Unit test overlap boundaries.
- Feature test room block.
- Feature test pending active/expired.

### Acceptance Criteria

- Availability benar pada seluruh boundary.
- Satu service dipakai sebagai sumber.

### Checklist

- [ ] Overlap formula benar
- [ ] Pending expiry diperhitungkan
- [ ] Room block diperhitungkan
- [ ] Capacity filter
- [ ] Test lulus


## TASK 4.2 — Halaman Pencarian Ketersediaan

### Tujuan

Menghubungkan form publik ke availability engine dan menampilkan tipe kamar yang benar-benar tersedia.

### Kondisi Sebelum Perubahan

AvailabilityService telah teruji.

### File yang Harus Dibaca

- form beranda
- room detail booking form
- AvailabilityService
- PricingService stub/strategy

### File yang Dibuat

- AvailabilitySearchRequest
- Public AvailabilityController
- view hasil pencarian

### File yang Diubah

- routes public
- form beranda/detail

### Database yang Terlibat

- rooms
- room_types
- bookings
- room_blocks

### Detail Implementasi

Request memuat check-in, check-out, guest count.

Tampilkan:

- input summary;
- nights;
- available room types;
- room count available;
- harga preview;
- CTA pilih.

Jangan expose room physical ID sebagai jaminan kepada guest. Guest memilih room type. Assignment physical room terjadi authoritative pada create booking.

Preserve query params menuju checkout.

### Business Rules

- Hanya hasil available.
- No availability memberi alternatif kembali ke form, bukan hasil palsu.
- Harga preview diberi label sesuai data saat ini.

### Validation Rules

- Tanggal valid.
- Tidak boleh tanggal lampau sesuai policy booking.
- Guest count sesuai batas global masuk akal.

### Security Considerations

- Rate limit ringan.
- Escaped query output.

### Edge Cases

- Search zero results.
- Invalid dates.
- Price changed between search and checkout.

### Testing

- Search feature test.
- No-result test.
- Inactive room hidden.

### Acceptance Criteria

- User hanya melihat tipe yang tersedia.
- Query dapat diteruskan ke checkout.

### Checklist

- [ ] Search form bekerja
- [ ] Hasil benar
- [ ] No result state
- [ ] Query preserved


## TASK 4.3 — Concurrency Foundation dan Locking Test

### Tujuan

Membuktikan pola row-lock kamar sebelum BookingService penuh dibuat.

### Kondisi Sebelum Perubahan

Availability query sudah benar tetapi belum authoritative terhadap race.

### File yang Harus Dibaca

- database connection config
- AvailabilityService
- Room model
- test infrastructure

### File yang Dibuat

- integration test khusus MySQL concurrency
- test helper untuk parallel requests bila diperlukan

### File yang Diubah

- CI config bila perlu menyediakan MySQL

### Database yang Terlibat

- rooms
- bookings

### Detail Implementasi

Buat proof/test bahwa dua transaction yang mengunci row room sama tidak dapat keduanya melewati authoritative recheck dan insert blocking booking.

Jangan bergantung pada SQLite untuk test ini. Gunakan MySQL environment.

Pattern final:

1. begin;
2. lock room;
3. recheck;
4. insert;
5. commit.

Test harus benar-benar parallel atau memakai mekanisme concurrency yang mampu menguji lock, bukan dua call sequential.

### Business Rules

- Room row adalah serialization point per physical room.
- Lock hanya selama transaction database; jangan call Midtrans sambil memegangnya.

### Validation Rules

- Test fixture harus valid.

### Security Considerations

- Test DB terpisah dari production.
- Timeout/deadlock ditangani secara test-safe.

### Edge Cases

- CI tidak mendukung parallel.
- Deadlock retry.

### Testing

- Concurrency test.
- Pastikan exactly one success.
- Count blocking rows = 1.

### Acceptance Criteria

- Pola locking terbukti.
- Test dapat diulang stabil.

### Checklist

- [ ] MySQL digunakan
- [ ] Parallel nyata
- [ ] Satu sukses
- [ ] Satu conflict



# FASE 5 — GUEST BOOKING

> **Kiro Spec:** SPEC 03 — Availability & Guest Booking Engine

## Tujuan Fase

Membangun checkout tanpa login, booking hold 30 menit, idempotency, kode booking, guest access aman, dan expiry workflow.

## Exit Gate Fase

Guest dapat membuat booking tanpa akun; kamar terkunci 30 menit; submit ganda tidak menduplikasi booking; expiry melepas inventory; akses booking guest aman.


## TASK 5.1 — Schema Booking, Sequence, Status History, dan Guest Token

### Tujuan

Membuat persistence inti booking sebelum membangun checkout.

### Kondisi Sebelum Perubahan

Availability engine tersedia; tabel booking belum lengkap.

### File yang Harus Dibaca

- seluruh migration
- Room model
- User/Admin model
- PolicyVersion model
- Promotion model jika sudah ada
- enum booking/payment/source

### File yang Dibuat

- migration bookings
- migration document_sequences
- migration booking_status_histories
- Booking model
- BookingStatusHistory model
- DocumentNumberService/helper
- factory booking

### File yang Diubah

- relasi User/Room/Admin/PolicyVersion

### Database yang Terlibat

- bookings
- document_sequences
- booking_status_histories

### Detail Implementasi

Implementasikan seluruh kolom booking sesuai desain. Jika promotions belum ada, urutkan migration atau buat foreign key setelah tabel tersedia.

Buat generator booking code yang menggunakan sequence row lock, bukan count.

Buat guest access token:

- 32 byte atau lebih dari CSPRNG;
- raw token hanya tersedia sesaat untuk URL/response;
- simpan SHA-256 hash;
- jangan pernah menampilkan hash sebagai token.

Setiap booking insert awal harus menghasilkan status history.

### Business Rules

- Booking code bukan ID.
- One booking = one room.
- Guest `user_id` nullable.
- Historical snapshot wajib.

### Validation Rules

- Database constraints sesuai schema.
- Status/source enum cast valid.

### Security Considerations

- Token tidak di-log.
- Foreign key delete behavior aman.

### Edge Cases

- Sequence race.
- Invoice number masih null.
- Guest tanpa email tetapi WhatsApp wajib.

### Testing

- Migration test.
- Sequence concurrency test.
- Token hash verification test.

### Acceptance Criteria

- Booking persistence siap.
- Kode unik pada concurrent generation.
- Public token tidak disimpan raw.

### Checklist

- [ ] Booking schema lengkap
- [ ] Sequence aman
- [ ] History dibuat
- [ ] Token hash


## TASK 5.2 — PricingService dan Checkout Summary

### Tujuan

Menghitung ringkasan booking dari data backend sebelum mutasi booking.

### Kondisi Sebelum Perubahan

Booking schema tersedia; promo/point belum wajib aktif.

### File yang Harus Dibaca

- RoomType model
- config booking/loyalty
- AvailabilityService
- public search flow

### File yang Dibuat

- PricingService
- quote DTO/value object
- CheckoutRequest untuk preview
- Public CheckoutController
- checkout view
- unit tests

### File yang Diubah

- routes
- search result CTA

### Database yang Terlibat

- room_types
- rooms
- policy_versions

### Detail Implementasi

V1 tanpa promo/point terlebih dahulu:

- validate dates;
- resolve room type;
- calculate nights;
- read base price;
- subtotal;
- total.

Checkout view berisi:

1. stay summary;
2. guest data;
3. arrival estimate;
4. special request;
5. policy checkbox;
6. price summary.

Member yang login boleh autofill, tetapi task guest tetap berjalan tanpa login.

Jangan memilih room fisik final terlalu awal. Saat preview boleh mengecek kandidat; saat create akan assign authoritative.

### Business Rules

- Price from backend only.
- Nights snapshot.
- Policy current required.

### Validation Rules

- Name required.
- WhatsApp required normalized.
- Email valid bila diisi; untuk payment receipt disarankan required sesuai keputusan final.
- Guest count capacity.
- Policy accepted.

### Security Considerations

- CSRF.
- No hidden total trusted.
- Escape special request.

### Edge Cases

- Price changes after search.
- No current policy.
- Availability disappears.

### Testing

- Pricing unit test.
- Checkout without login.
- Tampered hidden price ignored.

### Acceptance Criteria

- Guest dapat sampai checkout.
- Ringkasan berasal dari backend.
- No account required.

### Checklist

- [ ] Nights benar
- [ ] Price server-side
- [ ] Guest form
- [ ] Policy required
- [ ] No login gate


## TASK 5.3 — BookingService Create dengan Lock, Idempotency, dan Assignment Kamar

### Tujuan

Membuat booking authoritative yang aman dari race condition.

### Kondisi Sebelum Perubahan

Checkout preview tersedia dan concurrency pattern sudah dibuktikan.

### File yang Harus Dibaca

- AvailabilityService
- PricingService
- Booking model
- Room model
- Document sequence
- policy current

### File yang Dibuat

- BookingService
- CreateBookingCommand/DTO bila sesuai convention
- domain exceptions
- feature tests

### File yang Diubah

- Public CheckoutController store action
- routes

### Database yang Terlibat

- bookings
- rooms
- room_blocks
- document_sequences
- booking_status_histories

### Detail Implementasi

User memilih room type. BookingService:

1. cek idempotency key;
2. begin transaction;
3. ambil kandidat room IDs deterministik;
4. untuk setiap kandidat, coba lock room row;
5. setelah lock, authoritative overlap + block check;
6. pilih room pertama yang masih tersedia;
7. jika tidak ada, rollback dengan conflict;
8. calculate price ulang;
9. lock sequence;
10. create booking pending;
11. set expiry +30 menit;
12. snapshot room type/name/price;
13. save guest/token/policy;
14. history;
15. commit.

Jangan call external payment di dalam room lock transaction.

Setelah commit, arahkan ke payment preparation phase; sebelum Midtrans tersedia, booking pending page boleh menunjukkan payment integration belum aktif di development, tetapi jangan fake paid.

### Business Rules

- Exactly one physical room assigned.
- Pending valid memblokir.
- Idempotency same key returns same booking.
- Same key + different payload ditolak.

### Validation Rules

- All checkout rules revalidated.
- Room type and room active.
- Total non-negative.

### Security Considerations

- Transaction.
- LockForUpdate.
- No frontend room/price trust.

### Edge Cases

- All candidate rooms become unavailable.
- Duplicate browser submit.
- DB deadlock retry policy.
- Sequence collision.

### Testing

- Guest booking feature test.
- Tamper room type/price.
- Idempotency test.
- Concurrency test with actual create workflow.

### Acceptance Criteria

- Guest booking works without login.
- No double booking.
- No duplicate submit.
- 30-minute hold stored.

### Checklist

- [ ] Authoritative recheck
- [ ] Room lock
- [ ] Idempotency
- [ ] Snapshot
- [ ] Hold expiry
- [ ] Concurrency pass


## TASK 5.4 — Pending Page, Cek Booking Aman, dan Expiry Scheduler

### Tujuan

Membuat booking pending dapat diakses aman dan dilepas otomatis saat kedaluwarsa.

### Kondisi Sebelum Perubahan

Guest booking pending dapat dibuat.

### File yang Harus Dibaca

- BookingService
- booking model
- scheduler config
- public layout

### File yang Dibuat

- GuestBookingAccess middleware/service
- Public BookingStatusController
- views booking status/pending/expired
- ExpirePendingBookings command/service
- scheduler entry

### File yang Diubah

- routes
- BookingService expire method

### Database yang Terlibat

- bookings
- booking_status_histories
- promotion_usages bila ada
- loyalty_transactions bila ada

### Detail Implementasi

Akses via:

- booking code + raw access token URL; atau
- manual form booking code + verified email/WhatsApp.

Status page menampilkan data minimum yang diizinkan.

Expiry service:

- process pending past expiry;
- lock booking;
- recheck payment status;
- set expired;
- release promo;
- reverse redeem;
- history/audit.

Availability query sudah harus menganggap pending melewati expiry non-blocking meskipun scheduler terlambat.

### Business Rules

- Server time source.
- Frontend countdown display only.
- Expired booking tidak dapat membuat payment baru.

### Validation Rules

- Token compare constant-time bila helper mendukung.
- Manual lookup response generik.

### Security Considerations

- Rate limit lookup.
- No IDOR.
- No token logs.

### Edge Cases

- Scheduler delay.
- User opens two tabs.
- Booking expired while page open.

### Testing

- Token access test.
- Wrong token denied.
- Manual lookup rate/identity test.
- Expiry command idempotent.

### Acceptance Criteria

- Guest dapat melihat booking sendiri.
- Expired hold dilepas.
- Data booking lain aman.

### Checklist

- [ ] Secure access
- [ ] Manual check booking
- [ ] Scheduler configured
- [ ] Expiry idempotent
- [ ] Inventory released



# FASE 6 — INTEGRASI MIDTRANS SNAP

> **Kiro Spec:** SPEC 04 — Midtrans Payment

## Tujuan Fase

Menghubungkan booking pending ke pembayaran Midtrans Sandbox dengan payment attempt, webhook terverifikasi, reconciliation, dan continue payment.

## Exit Gate Fase

Snap Sandbox bekerja; payment dan booking status terpisah; webhook idempotent; duplicate/late/out-of-order aman; tidak ada ketergantungan pada callback frontend.


## TASK 6.1 — Schema Payment dan Konfigurasi Midtrans Sandbox

### Tujuan

Menyediakan persistence payment attempt dan konfigurasi SDK resmi.

### Kondisi Sebelum Perubahan

Guest booking pending sudah ada; belum ada transaksi provider.

### File yang Harus Dibaca

- `composer.json`
- `config/midtrans.php`
- `.env.example`
- Booking model
- dokumentasi Midtrans resmi

### File yang Dibuat

- migration payments
- Payment model
- MidtransPaymentService skeleton/configuration
- payment factory

### File yang Diubah

- `composer.json` bila SDK resmi belum ada
- Booking relation
- `.env.example`

### Database yang Terlibat

- payments
- bookings

### Detail Implementasi

Install package resmi yang kompatibel. Konfigurasikan Server Key, Client Key, mode production, sanitization, dan 3DS melalui environment.

Model payment menyimpan attempt history. Provider order ID harus unik per attempt, misalnya mengandung booking code, nomor attempt, dan suffix acak. Jangan menggunakan booking code mentah sebagai satu-satunya reusable order ID.

### Business Rules

- Sandbox first.
- One booking may have multiple attempts.
- Provider order ID tidak boleh dipakai ulang untuk transaksi aktif atau paid.

### Validation Rules

- Gross amount > 0.
- Booking pending dan belum expired sebelum attempt baru.

### Security Considerations

- Server Key backend only.
- No secret logs.
- Config cache safe.

### Edge Cases

- SDK incompatible.
- Network unavailable.
- Existing payment package.

### Testing

- Config test.
- Model constraint test.
- Provider order unique test.

### Acceptance Criteria

- Payment schema siap.
- SDK configured tanpa hardcode.
- Production tidak aktif default.

### Checklist

- [ ] SDK resmi
- [ ] Sandbox
- [ ] Env example
- [ ] Payment model
- [ ] Unique provider order


## TASK 6.2 — Create/Resume Snap Payment dan Lanjutkan Pembayaran

### Tujuan

Membuat Snap token dari backend dan memungkinkan user kembali ke payment yang sama.

### Kondisi Sebelum Perubahan

Payment schema dan Midtrans config tersedia.

### File yang Harus Dibaca

- BookingService
- MidtransPaymentService
- booking pending page
- payment model

### File yang Dibuat

- payment controller/endpoint
- Snap Blade component/JS integration
- payment feature tests dengan fake client

### File yang Diubah

- pending booking view
- routes
- MidtransPaymentService

### Database yang Terlibat

- bookings
- payments

### Detail Implementasi

Flow:

1. authorize guest/member access;
2. lock/reload booking;
3. verify pending dan belum expired;
4. jika payment pending dengan Snap token yang dapat dilanjutkan, reuse;
5. bila attempt gagal dan hold masih aktif, buat attempt baru;
6. call Midtrans backend untuk token;
7. simpan token;
8. frontend membuka Snap.

Frontend callbacks `onSuccess`, `onPending`, `onError`, dan `onClose` hanya mengarahkan UI untuk refresh status. Callback tidak boleh mengubah database menjadi paid.

Tombol `Lanjutkan Pembayaran` selalu memanggil backend untuk resolve current valid attempt, bukan membuat booking baru.

### Business Rules

- Satu booking aktif.
- Tidak membuat booking baru saat resume.
- Booking expired tidak dapat membayar.
- Gross amount dari snapshot.

### Validation Rules

- Booking code/token authorized.
- Payment attempt state valid.

### Security Considerations

- No Server Key in JS.
- CSRF on session endpoint.
- Authorization.

### Edge Cases

- Snap close.
- Network error after token created.
- Double-click pay.
- Payment attempt already paid.

### Testing

- Fake SDK create token.
- Resume returns same token.
- Expired rejects.
- Frontend callback does not mark paid.

### Acceptance Criteria

- User dapat membuka Snap.
- Close lalu lanjutkan bekerja.
- Tidak ada duplicate booking.

### Checklist

- [ ] Snap token backend
- [ ] Resume payment
- [ ] No frontend truth
- [ ] Expiry respected


## TASK 6.3 — Webhook Inbox, Signature Verification, dan State Mapping

### Tujuan

Memproses notification Midtrans secara aman dan idempotent.

### Kondisi Sebelum Perubahan

Snap payment dapat dibuat.

### File yang Harus Dibaca

- dokumentasi webhook/signature Midtrans terbaru
- MidtransPaymentService
- Payment dan Booking model
- konfigurasi CSRF/middleware route

### File yang Dibuat

- migration payment_webhook_events
- PaymentWebhookEvent model
- Webhook controller
- webhook parser/value object bila diperlukan
- tests webhook

### File yang Diubah

- route webhook
- MidtransPaymentService
- pengecualian CSRF hanya untuk endpoint webhook yang tepat

### Database yang Terlibat

- payment_webhook_events
- payments
- bookings
- booking_status_histories
- promotion_usages

### Detail Implementasi

Implementasikan urutan verifikasi:

1. parse payload tanpa mempercayainya;
2. hitung dan verifikasi signature sesuai dokumentasi resmi Midtrans yang berlaku;
3. lookup payment dengan provider order ID;
4. cocokkan booking;
5. normalisasi dan cocokkan gross amount;
6. hitung deduplication key;
7. insert/resolve webhook inbox event;
8. bila event sama sudah diproses, return respons 2xx aman;
9. lock payment dan booking;
10. map status provider ke status aplikasi;
11. cegah state downgrade;
12. update payment;
13. confirm booking bila paid dan hold masih valid;
14. jika paid setelah booking expired, simpan payment paid tetapi tandai booking `needs_attention`;
15. consume promo bila memang payment sukses;
16. jangan award loyalty point;
17. tulis status history/audit;
18. commit;
19. return respons cepat.

Simpan payload yang sudah direduksi atau di-redact. Jangan menyimpan atau mencetak secret.

### Business Rules

- Webhook terverifikasi menjadi sumber kebenaran.
- Duplicate event aman.
- Out-of-order event tidak boleh menurunkan status final.
- Booking status dan payment status tetap terpisah.
- Late payment tidak otomatis menghidupkan booking expired.

### Validation Rules

- Provider order ID harus dikenal.
- Nominal harus sama persis setelah normalisasi Rupiah.
- Signature harus valid.

### Security Considerations

- Endpoint publik tetapi diverifikasi.
- Jangan menonaktifkan CSRF global.
- Jangan log secret atau token.
- Gunakan transaction dan row lock.

### Edge Cases

- Webhook duplikat.
- Payment tidak ditemukan.
- Amount mismatch.
- Late paid notification.
- Pending event datang setelah paid.
- Payload malformed.

### Testing

- Valid signature diproses.
- Invalid signature tidak mengubah data.
- Duplicate idempotent.
- Amount mismatch tidak confirm booking.
- Late paid menghasilkan needs_attention.
- Out-of-order tidak downgrade paid.

### Acceptance Criteria

- Webhook path aman dan idempotent.
- Payment dan booking berubah hanya melalui mapping terpusat.
- Tidak ada point award dari webhook.

### Checklist

- [ ] Signature diverifikasi
- [ ] Nominal diverifikasi
- [ ] Dedup aktif
- [ ] Row lock aktif
- [ ] State mapping terpusat
- [ ] Late payment path teruji


## TASK 6.4 — Status API Reconciliation dan Sandbox Scenario Testing

### Tujuan

Menangani webhook terlambat dan membuktikan seluruh siklus Sandbox.

### Kondisi Sebelum Perubahan

Webhook dasar bekerja.

### File yang Harus Dibaca

- Midtrans status API docs
- MidtransPaymentService
- scheduler config
- admin payment list bila sudah ada

### File yang Dibuat

- reconciliation command/service
- scheduler entry
- sandbox test checklist internal

### File yang Diubah

- MidtransPaymentService
- admin attention widget bila sudah ada

### Database yang Terlibat

- payments
- bookings
- payment_webhook_events
- audit_logs

### Detail Implementasi

Reconciliation menargetkan payment:

- pending lebih lama dari threshold;
- webhook processing gagal;
- booking/payment needs attention.

Fetch status server-to-server dan proses melalui mapping yang sama dengan webhook. Jangan membuat jalur update status kedua yang memiliki aturan berbeda.

Sandbox manual testing wajib mencakup pending, settlement, expire, cancel/deny, duplicate notification, delayed notification, serta close Snap kemudian resume. Catat hasil tanpa secret.

### Business Rules

- Reconciliation idempotent.
- Gunakan state transition engine yang sama.
- Tidak ada point award.

### Validation Rules

- Hanya payment yang diketahui dapat direkonsiliasi.

### Security Considerations

- Command hanya system/admin.
- Batasi frekuensi request provider.

### Edge Cases

- Provider outage.
- Status API timeout.
- Unknown provider status.

### Testing

- Command test dengan fake status API.
- Manual Sandbox checklist.
- Scheduler list check.

### Acceptance Criteria

- Payment pending dapat direkonsiliasi.
- Semua skenario Sandbox penting lulus sebelum production.

### Checklist

- [ ] Reconciliation bekerja
- [ ] Pending test
- [ ] Settlement test
- [ ] Expire test
- [ ] Duplicate webhook test
- [ ] Delayed webhook test



# FASE 7 — ADMIN RESERVATION DAN KALENDER

> **Kiro Spec:** SPEC 05 — Admin Reservation & Member

## Tujuan Fase

Membangun operasi reservasi admin, booking manual semua sumber, kalender kamar, room block, dan manajemen status tanpa melewati availability engine.

## Exit Gate Fase

Admin dapat melihat inventory per tanggal, membuat booking manual tanpa bentrok, memblokir kamar secara aman, dan menangani reservasi dari semua sumber.


## TASK 7.1 — Dashboard Admin Operasional dan Daftar Reservasi

### Tujuan

Membuat admin melihat kondisi operasional nyata, bukan statistik dekoratif.

### Kondisi Sebelum Perubahan

Booking/payment sudah tersedia.

### File yang Harus Dibaca

- admin layout
- Booking/Payment/Room models
- enum status/source
- query/report pattern existing

### File yang Dibuat

- Admin DashboardController
- Admin ReservationController index/show
- views dashboard
- views reservation index/detail

### File yang Diubah

- admin routes
- sidebar

### Database yang Terlibat

- bookings
- payments
- rooms
- room_blocks

### Detail Implementasi

Dashboard query:

- booking dibuat hari ini;
- check-in hari ini;
- check-out hari ini;
- room occupied saat ini;
- room available saat ini;
- pending payment aktif;
- month revenue;
- recent bookings;
- needs attention.

Setiap card link ke filter relevan.

Reservation list menyediakan filter tanggal, stay date, status, payment status, source, room, dan search guest/booking code.

### Business Rules

- Pending expired tidak dihitung active hold.
- Revenue tidak memasukkan pending/unpaid.
- Needs attention selalu terlihat.

### Validation Rules

- Filter tanggal valid.
- Pagination bounded.

### Security Considerations

- Admin guard.
- Escape search.
- No payment secret.

### Edge Cases

- Timezone boundary.
- No bookings.
- Large date range.

### Testing

- Dashboard metrics test.
- Reservation filters test.
- Admin auth test.

### Acceptance Criteria

- Dashboard berguna untuk operasi.
- Detail booking lengkap tanpa data rahasia.

### Checklist

- [ ] Metrics benar
- [ ] Filters
- [ ] Needs attention
- [ ] Admin-only


## TASK 7.2 — Booking Manual Admin Semua Sumber

### Tujuan

Memungkinkan reservasi WhatsApp, OTA, walk-in, telepon, dan lainnya masuk ke inventory yang sama.

### Kondisi Sebelum Perubahan

Admin dapat melihat reservasi; BookingService create website sudah ada.

### File yang Harus Dibaca

- BookingService
- AvailabilityService
- PricingService
- Admin ReservationController
- BookingSource enum

### File yang Dibuat

- ManualBookingRequest
- manual booking create view
- BookingService manual workflow
- audit event

### File yang Diubah

- admin routes
- reservation detail

### Database yang Terlibat

- bookings
- rooms
- booking_status_histories
- audit_logs
- payments bila manual payment dicatat

### Detail Implementasi

Form input sesuai kebutuhan. Admin memilih physical room karena operasi internal.

Workflow tetap:

- transaction;
- lock room;
- overlap;
- room block;
- price validation;
- snapshot;
- booking code;
- status history.

Untuk source website, gunakan flow public, bukan manual form.

Admin price override:

- backend menerima nominal integer;
- tampilkan base price sebagai referensi;
- reason wajib bila berbeda dari default di atas threshold yang dipilih;
- audit before/after.

Payment manual dapat dicatat sebagai paid/unpaid dengan metode text/enum internal, tetapi jangan membuat seolah-olah Midtrans jika bukan Midtrans.

### Business Rules

- OTA booking masuk kalender.
- OTA tidak eligible point V1.
- Manual booking tidak boleh force conflict diam-diam.
- Source enum wajib.

### Validation Rules

- Dates, capacity, room active.
- Price >= 0.
- Source valid.
- Paid amount/status consistent.

### Security Considerations

- Admin only.
- Audit override.
- Transaction/lock.

### Edge Cases

- Admin double-click.
- OTA booking tanggal lalu.
- Room conflict.
- Walk-in same day.

### Testing

- Manual booking each source.
- Conflict rejection.
- Audit price override.
- OTA appears calendar query.

### Acceptance Criteria

- Semua sumber dapat dicatat.
- Inventory tetap tunggal.
- Double booking tetap dicegah.

### Checklist

- [ ] Source lengkap
- [ ] Room lock
- [ ] Price audit
- [ ] OTA calendar


## TASK 7.3 — Kalender Kamar Timeline

### Tujuan

Menampilkan kamar fisik sebagai baris dan tanggal sebagai kolom dengan status konsisten.

### Kondisi Sebelum Perubahan

Manual dan website booking memakai database yang sama.

### File yang Harus Dibaca

- AvailabilityService
- Booking model
- Room/RoomBlock models
- admin layout

### File yang Dibuat

- Admin CalendarController
- calendar query/view model
- timeline Blade view
- Alpine interactions ringan

### File yang Diubah

- admin routes/sidebar

### Database yang Terlibat

- rooms
- bookings
- room_blocks

### Detail Implementasi

Calendar menerima start/end range dengan batas maksimal agar query tidak berat.

Per cell/range tampil:

- available;
- pending;
- confirmed;
- checked-in;
- blocked.

Booking bar dapat span beberapa tanggal. End date exclusive.

Interaction:

- click booking → detail;
- click empty range → manual booking prefilled;
- click room → room detail;
- block room action.

Gunakan warna konsisten dan legend. Mobile/tablet: horizontal scroll, sticky room names.

### Business Rules

- Calendar tidak memiliki logic availability berbeda.
- Pending expired tampil available.
- Room block menang sesuai interval.

### Validation Rules

- Range valid dan bounded.
- Room filter valid.

### Security Considerations

- Admin-only.
- No raw internal notes in data attributes.

### Edge Cases

- Long booking spans range.
- Overnight boundary.
- No rooms.

### Testing

- Calendar rendering test.
- Boundary date test.
- Pending expired display test.
- Responsive manual check.

### Acceptance Criteria

- Admin memahami occupancy per room.
- Warna/status konsisten.

### Checklist

- [ ] Room rows
- [ ] Date columns
- [ ] Status legend
- [ ] Click actions
- [ ] Mobile scroll


## TASK 7.4 — Room Block dengan Conflict Detection

### Tujuan

Membuat kamar dapat diblokir tanpa menimpa reservasi sah.

### Kondisi Sebelum Perubahan

Kalender tersedia.

### File yang Harus Dibaca

- RoomBlock schema/model
- AvailabilityService
- CalendarController
- audit service

### File yang Dibuat

- RoomBlockController
- RoomBlockRequest
- views create/edit/detail

### File yang Diubah

- admin calendar
- routes

### Database yang Terlibat

- room_blocks
- rooms
- bookings
- audit_logs

### Detail Implementasi

Create block:

1. validate interval;
2. lock room;
3. query blocking booking overlap;
4. jika ada conflict, tampilkan booking code/status/date;
5. reject default;
6. jangan sediakan “force” tanpa desain approval yang jelas.

Edit block menjalankan recheck yang sama.

Delete block boleh dilakukan admin dan diaudit.

### Business Rules

- Block interval half-open.
- Block memengaruhi AvailabilityService.
- Conflict tidak boleh diam-diam.

### Validation Rules

- End > start.
- Reason required.
- Room active/existing.

### Security Considerations

- Admin-only.
- Audit create/update/delete.

### Edge Cases

- Existing future booking.
- Adjacent block.
- Overlapping blocks.

### Testing

- Block availability test.
- Conflict booking test.
- Adjacent date test.

### Acceptance Criteria

- Room block mencegah booking.
- Existing booking dilindungi.

### Checklist

- [ ] CRUD block
- [ ] Conflict shown
- [ ] Availability integration
- [ ] Audit



# FASE 8 — LOGIN GOOGLE DAN MEMBER

> **Kiro Spec:** SPEC 05 — Admin Reservation & Member

## Tujuan Fase

Menambahkan OAuth Google, dashboard member, profile, booking ownership, dan klaim guest booking aman tanpa mengubah guest booking menjadi wajib login.

## Exit Gate Fase

Member dapat login email/Google, melihat data sendiri, dan mengklaim guest booking hanya melalui verifikasi yang sah.


## TASK 8.1 — Integrasi Laravel Socialite dan Google OAuth

### Tujuan

Menyediakan login Google yang aman dan dapat menghubungkan identitas provider ke user.

### Kondisi Sebelum Perubahan

Email auth member sudah ada.

### File yang Harus Dibaca

- `config/services.php`
- `config/auth.php`
- User model
- auth routes/views
- dokumentasi Socialite resmi

### File yang Dibuat

- migration social_accounts
- SocialAccount model
- GoogleAuthController
- OAuth tests dengan mock/fake

### File yang Diubah

- `composer.json` bila Socialite belum ada
- `config/services.php`
- `.env.example`
- login/register views
- routes

### Database yang Terlibat

- users
- social_accounts

### Detail Implementasi

Implement redirect dan callback stateful.

Callback resolution order:

1. exact provider + provider_user_id;
2. jika tidak ada, evaluasi email provider;
3. hanya link ke user existing bila email provider benar-benar verified dan normalized cocok;
4. jika tidak aman, jangan auto-link;
5. create user baru bila valid;
6. create social account;
7. login dan regenerate session.

Google-only user boleh password NULL. UI profile dapat menawarkan set password melalui flow aman, bukan menampilkan password.

### Business Rules

- Login Google opsional.
- Satu provider identity tidak ke dua user.
- Guest booking tetap tanpa login.

### Validation Rules

- Provider ID required.
- Email required sesuai kebijakan app.
- Email normalized.

### Security Considerations

- OAuth state.
- No unnecessary token storage.
- Session regeneration.

### Edge Cases

- Provider email missing.
- Existing email unverified.
- Duplicate callback.
- User inactive.

### Testing

- Redirect test.
- Callback create user.
- Callback existing social account.
- Safe link existing verified email.
- Reject ambiguous linking.

### Acceptance Criteria

- Google login berhasil.
- Tidak ada duplicate user/social identity.
- Linking aman.

### Checklist

- [ ] Socialite installed
- [ ] Env example
- [ ] State protection
- [ ] Safe linking
- [ ] Tests mocked


## TASK 8.2 — Dashboard Member dan Booking Saya

### Tujuan

Member mendapatkan manfaat nyata dari login.

### Kondisi Sebelum Perubahan

User dapat login email/Google; booking memiliki user_id nullable.

### File yang Harus Dibaca

- member layout
- Booking model
- User model
- Invoice placeholder

### File yang Dibuat

- Member DashboardController
- Member BookingController
- views dashboard
- views booking active/history/cancelled/detail
- BookingPolicy

### File yang Diubah

- member routes/navigation

### Database yang Terlibat

- users
- bookings
- payments

### Detail Implementasi

Dashboard menampilkan:

- nama;
- loyalty balance cache/service;
- estimasi nilai poin;
- active bookings;
- CTA.

Booking Saya dibagi status.

Member booking detail menggunakan policy ownership `booking.user_id == auth user id`. Jangan mencari booking hanya berdasarkan booking code tanpa ownership.

Booking guest yang belum diklaim tidak otomatis muncul.

### Business Rules

- Member hanya data sendiri.
- Active definition terpusat.
- Invoice hanya jika eligible.

### Validation Rules

- Route model binding tetap melalui policy.

### Security Considerations

- IDOR tests.
- No admin notes.
- No guest token exposure.

### Edge Cases

- User has no booking.
- Claimed booking appears.
- Booking user set null later.

### Testing

- Ownership allow/deny.
- Dashboard data.
- Status tabs.

### Acceptance Criteria

- Member melihat booking miliknya saja.
- Dashboard memberi manfaat nyata.

### Checklist

- [ ] Dashboard
- [ ] Active bookings
- [ ] History
- [ ] Policy ownership
- [ ] IDOR test


## TASK 8.3 — Profil Member dan Autofill Checkout

### Tujuan

Menyimpan data member dan mempercepat booking tanpa mengurangi validasi.

### Kondisi Sebelum Perubahan

Dashboard member tersedia.

### File yang Harus Dibaca

- User model
- checkout controller/view
- email verification flow

### File yang Dibuat

- Member ProfileController
- ProfileUpdateRequest
- profile view

### File yang Diubah

- checkout prefill logic
- member navigation

### Database yang Terlibat

- users
- bookings

### Detail Implementasi

Profil dapat mengubah name, WhatsApp, avatar optional.

Perubahan email:

- separate action;
- re-auth jika perlu;
- set verification state ulang;
- kirim verify email.

Checkout saat login:

- prefill name/email/WhatsApp;
- user tetap dapat mengubah snapshot guest data untuk booking tersebut;
- booking `user_id` tetap member login.

Autofill bukan alasan untuk mempercayai client data.

### Business Rules

- Booking snapshot tidak berubah ketika profile berubah nanti.
- Email claim membutuhkan verified email.

### Validation Rules

- Name/WhatsApp/email/avatar rules.

### Security Considerations

- Re-auth email change.
- Secure upload.
- CSRF.

### Edge Cases

- Google-only no password.
- Email already used.
- Avatar upload failure.

### Testing

- Profile update.
- Email verification reset.
- Checkout prefill.
- Old booking snapshot unchanged.

### Acceptance Criteria

- Profile aman.
- Checkout lebih cepat untuk member.

### Checklist

- [ ] Profile edit
- [ ] Email change secure
- [ ] Autofill
- [ ] Snapshot preserved


## TASK 8.4 — Guest Booking Claim yang Aman

### Tujuan

Menghubungkan guest booking ke member tanpa klaim berdasarkan nama atau nomor yang belum diverifikasi.

### Kondisi Sebelum Perubahan

Guest booking, member auth, dan verified email tersedia.

### File yang Harus Dibaca

- Booking model
- User model
- guest booking status page
- auth post-login redirect flow

### File yang Dibuat

- migration booking_claim_tokens
- BookingClaimToken model
- BookingClaimService
- ClaimController
- claim views
- admin manual claim action

### File yang Diubah

- guest booking status page
- member dashboard
- audit logging

### Database yang Terlibat

- booking_claim_tokens
- bookings
- users
- audit_logs

### Detail Implementasi

Issue claim token untuk booking guest yang memiliki email. Token raw dikirim melalui link yang sah, hash disimpan.

Claim:

- user login;
- user email verified;
- normalized email sama;
- token valid/unexpired/unused;
- booking user_id null;
- lock booking;
- link;
- mark token used;
- audit.

Setelah guest booking selesai, offer login/register. Jangan auto-link hanya karena orang login di browser yang sama jika email tidak cocok.

Admin manual claim perlu user selection, reason, audit.

### Business Rules

- Name never sufficient.
- Unverified WhatsApp never sufficient.
- Email mismatch = reject.
- Claim does not award points early.

### Validation Rules

- Token required per flow.
- Verified email exact normalized match.

### Security Considerations

- Token hash.
- Rate limit.
- One-time.
- No token logs.
- IDOR protection.

### Edge Cases

- Token expired.
- Booking already claimed.
- Two users claim concurrently.
- Email changed.

### Testing

- Successful claim.
- Mismatch reject.
- Expired token reject.
- Concurrent claim only one.
- Manual claim audited.

### Acceptance Criteria

- Claim aman dan idempotent.
- Claimed booking muncul di member dashboard.

### Checklist

- [ ] Hash token
- [ ] Verified email match
- [ ] One-time
- [ ] Concurrency
- [ ] Manual audit



# FASE 9 — LOYALTY POINT

> **Kiro Spec:** SPEC 06 — Loyalty & Promotion

## Tujuan Fase

Membangun ledger poin, FIFO lot, earn setelah completed, redemption, reversal, expiry 18 bulan, dan admin adjustment secara aman.

## Exit Gate Fase

Saldo dapat direkonsiliasi dari ledger; earn satu kali; redeem maksimal 20%; expiry otomatis; reversal tidak menghapus histori; OTA tidak mendapat poin V1.


## TASK 9.1 — Schema Ledger, Allocation, dan Loyalty Configuration

### Tujuan

Menyediakan struktur data loyalty yang dapat diaudit dan tidak bergantung pada satu kolom saldo.

### Kondisi Sebelum Perubahan

Member dan booking tersedia; loyalty belum aktif.

### File yang Harus Dibaca

- User model
- Booking model
- config/loyalty.php
- BookingSource enum

### File yang Dibuat

- migration loyalty_transactions
- migration loyalty_point_allocations
- LoyaltyTransaction model
- LoyaltyPointAllocation model
- LoyaltyPointService skeleton

### File yang Diubah

- User/Booking relations
- settings loyalty group bila admin-configurable

### Database yang Terlibat

- loyalty_transactions
- loyalty_point_allocations
- users
- bookings
- settings

### Detail Implementasi

Implement ledger dan allocation sesuai schema.

Config default:

- earn divisor 1000;
- point value 50;
- min redeem 100;
- max percent 20;
- expiry months 18;
- eligible sources website/whatsapp/walk_in.

Sumber kebenaran saldo = urutan ledger. `users.loyalty_balance_cache` hanya cache.

Buat method reconciliation internal yang membandingkan cache dengan latest ledger balance.

### Business Rules

- Ledger tidak dihapus.
- Poin integer.
- Positive lot mempunyai remaining_points dan expiry.
- Idempotency key wajib.

### Validation Rules

- Points transaction non-zero kecuali event khusus benar-benar dibutuhkan.
- Balance after tidak negatif untuk normal workflow.

### Security Considerations

- Transaction/locks.
- Admin tidak dapat edit ledger raw.

### Edge Cases

- Existing users cache mismatch.
- No ledger yet.

### Testing

- Migration.
- Relation test.
- Idempotency unique test.
- Balance reconciliation unit test.

### Acceptance Criteria

- Ledger foundation benar.
- Config bisnis sesuai kebutuhan.

### Checklist

- [ ] Ledger
- [ ] Allocation
- [ ] Config
- [ ] Cache reconciliation


## TASK 9.2 — Award Poin Setelah Booking Completed

### Tujuan

Member mendapatkan poin hanya setelah seluruh siklus menginap selesai.

### Kondisi Sebelum Perubahan

Ledger tersedia; booking status flow tersedia atau akan dipakai.

### File yang Harus Dibaca

- LoyaltyPointService
- BookingService/status transition
- Payment/Refund models
- booking source config

### File yang Dibuat

- award workflow
- unit/feature tests

### File yang Diubah

- complete booking action akan memanggil service

### Database yang Terlibat

- loyalty_transactions
- users
- bookings
- payments
- refunds

### Detail Implementasi

Award conditions:

1. booking status completed;
2. user_id tidak null;
3. source eligible;
4. payment/eligible payment condition terpenuhi;
5. belum pernah award;
6. eligible amount setelah refund policy dihitung.

Points:

`floor(eligible_loyalty_amount / 1000)`

Jika 0, boleh tidak membuat earn transaction, tetapi keputusan harus konsisten dan terdokumentasi.

Transaction:

- lock booking;
- lock user;
- idempotency key;
- create earn;
- remaining = points;
- expires_at +18 months;
- update cache.

### Business Rules

- Tidak award saat booking dibuat.
- Tidak award saat payment paid.
- Tidak award saat check-in.
- Tidak award saat check-out sebelum completed.
- OTA default tidak eligible.

### Validation Rules

- Booking must be completed.
- User active linkage exists.

### Security Considerations

- Idempotency.
- Lock user/booking.
- No admin endpoint to trigger arbitrary duplicate earn.

### Edge Cases

- Complete called twice.
- Booking claimed after completion.
- Refund after award.

### Testing

- Award once.
- Second call no duplicate.
- OTA no award.
- Correct floor math.

### Acceptance Criteria

- Satu booking maksimal satu earn event.
- Expiry date benar.
- Cache dan ledger konsisten.

### Checklist

- [ ] Completion-only
- [ ] Source eligibility
- [ ] Floor calculation
- [ ] Idempotency
- [ ] Expiry 18 months


## TASK 9.3 — Preview dan Redeem Poin pada Booking

### Tujuan

Member dapat memakai poin sebagai diskon dengan batas aman.

### Kondisi Sebelum Perubahan

Award/balance tersedia; checkout guest/member sudah ada.

### File yang Harus Dibaca

- PricingService
- BookingService
- LoyaltyPointService
- member checkout

### File yang Dibuat

- redemption preview endpoint/action bila diperlukan
- redemption workflow
- checkout UI point choice
- tests

### File yang Diubah

- PricingService
- BookingService transaction
- checkout view

### Database yang Terlibat

- loyalty_transactions
- loyalty_point_allocations
- users
- bookings

### Detail Implementasi

UI member menampilkan:

- current balance;
- nilai Rupiah;
- minimum 100;
- max discount 20%;
- input points;
- preview.

Saat booking create:

1. ignore client-computed discount;
2. lock user;
3. load unexpired lots FIFO expiry;
4. recheck balance;
5. compute max discount;
6. validate requested points;
7. create redeem negative transaction;
8. allocate to credit lots;
9. decrement remaining;
10. update cache;
11. save booking point snapshot;
12. commit bersama booking.

Promo option harus mutually exclusive di request dan service.

### Business Rules

- Min 100 points.
- 1 point = Rp50.
- Max 20% eligible booking value.
- No promo together.
- Only unexpired points.

### Validation Rules

- Requested integer.
- Enough balance.
- Discount not exceed max.
- Total not negative.

### Security Considerations

- User lock.
- Server calculation.
- IDOR impossible because auth user only.

### Edge Cases

- Two tabs redeem same points.
- Points expire between preview/create.
- Max percent yields non-round points.

### Testing

- Min reject.
- Insufficient reject.
- 20% cap.
- Concurrent redemption prevents overspend.
- Allocation sum correct.

### Acceptance Criteria

- Member can redeem safely.
- No negative balance.
- Booking snapshot correct.

### Checklist

- [ ] Preview
- [ ] FIFO lots
- [ ] 20% cap
- [ ] No promo
- [ ] Concurrency


## TASK 9.4 — Reversal Redemption, Reversal Earn, dan Expiry

### Tujuan

Menangani cancellation, hold expiry, refund, dan point expiration tanpa menghapus histori.

### Kondisi Sebelum Perubahan

Redeem dan earn tersedia.

### File yang Harus Dibaca

- LoyaltyPointService
- BookingService expiry/cancel
- Refund flow
- scheduler

### File yang Dibuat

- expiry command/service
- reversal methods
- scheduler entry
- tests

### File yang Diubah

- BookingService expire/cancel
- refund processing

### Database yang Terlibat

- loyalty_transactions
- loyalty_point_allocations
- users
- bookings
- refunds

### Detail Implementasi

Redemption reversal dipanggil jika booking pending expired/cancel dan redeem sudah terjadi.

Earn reversal dipanggil bila koreksi/refund setelah award memerlukan pengurangan poin.

Expiry harian:

- find expired positive lots with remaining > 0;
- process per user transaction;
- create expire debit;
- allocations;
- zero remaining;
- cache update.

Semua event menggunakan idempotency key.

Policy reversal lot yang sudah expired harus eksplisit. Default aman: jangan memperpanjang masa berlaku secara tidak sengaja.

### Business Rules

- Never delete old transaction.
- Reversal event references source.
- Expiry source lot once.

### Validation Rules

- Refund reversal amount/point computation cannot exceed awarded points.

### Security Considerations

- Admin/system only mutation.
- Transaction/lock.

### Edge Cases

- Redeem reversal after source lot expiry.
- Partial refund.
- Repeated scheduler.

### Testing

- Booking expiry reverses redeem.
- Second expiry no duplicate.
- Point expiry debit once.
- Refund earn reversal.

### Acceptance Criteria

- Histori lengkap.
- Saldo benar setelah reversal/expiry.

### Checklist

- [ ] Redeem reversal
- [ ] Earn reversal
- [ ] Daily expiry
- [ ] Idempotency
- [ ] Cache consistent


## TASK 9.5 — Dashboard Poin Member dan Admin Adjustment

### Tujuan

Member dapat memahami poin dan admin dapat koreksi dengan audit.

### Kondisi Sebelum Perubahan

Loyalty workflows lengkap.

### File yang Harus Dibaca

- member layout/dashboard
- admin layout
- LoyaltyPointService
- audit log

### File yang Dibuat

- Member LoyaltyController/views
- Admin LoyaltyController/views
- AdjustmentRequest

### File yang Diubah

- member/admin navigation

### Database yang Terlibat

- loyalty_transactions
- users
- audit_logs

### Detail Implementasi

Member view:

- saldo;
- estimated value;
- transaction history;
- next expiry.

Admin view:

- search user;
- ledger;
- balance;
- adjustment form.

Adjustment:

- signed integer;
- reason required;
- lock user;
- ensure negative adjustment not below zero unless explicit policy disallows;
- create transaction;
- positive adjustment expiry policy must be explicit;
- audit actor.

### Business Rules

- Member cannot edit.
- Admin adjustment creates ledger event.
- No direct balance edit.

### Validation Rules

- Reason required.
- Points integer non-zero.

### Security Considerations

- Admin-only.
- Audit.
- CSRF.

### Edge Cases

- Large adjustment.
- Negative beyond balance.
- User inactive.

### Testing

- Member history.
- Admin adjustment.
- Direct cache tamper not used as source.

### Acceptance Criteria

- Poin transparan bagi member.
- Admin correction traceable.

### Checklist

- [ ] Member points page
- [ ] Next expiry
- [ ] Admin ledger
- [ ] Adjustment audit



# FASE 10 — PROMO

> **Kiro Spec:** SPEC 06 — Loyalty & Promotion

## Tujuan Fase

Membangun kode promo dengan validasi backend, quota reservation, snapshot discount, dan larangan kombinasi dengan loyalty point.

## Exit Gate Fase

Promo dapat dipakai secara aman tanpa oversell quota; expired/cancel melepas quota; payment success mengonsumsi quota; promo dan poin tidak dapat digabung.


## TASK 10.1 — Schema dan Admin CRUD Promo

### Tujuan

Menyediakan master promo yang dapat dikonfigurasi admin.

### Kondisi Sebelum Perubahan

Booking dan pricing tersedia; promo belum aktif.

### File yang Harus Dibaca

- PricingService
- admin layout
- enum PromotionType/UsageStatus

### File yang Dibuat

- migration promotions
- migration promotion_usages
- Promotion model
- PromotionUsage model
- Admin PromotionController
- Promo Requests
- views promo

### File yang Diubah

- admin routes/sidebar
- model relations

### Database yang Terlibat

- promotions
- promotion_usages

### Detail Implementasi

CRUD field sesuai schema. Normalize code uppercase dan trim.

Untuk percentage, pilih representasi yang konsisten. Rekomendasi sederhana: integer percent 1–100 pada V1. Jika perlu presisi pecahan, gunakan basis points dan dokumentasikan.

Admin melihat usage:

- reserved;
- consumed;
- released;
- quota remaining.

### Business Rules

- Code unique case-insensitive melalui normalisasi.
- Ends after starts.
- Inactive promo cannot apply.

### Validation Rules

- Percentage range valid.
- Fixed amount >= 0.
- Quota positive or null.
- Minimum/max discount >= 0.

### Security Considerations

- Admin-only.
- Mass assignment explicit.
- Audit changes important.

### Edge Cases

- Code case variants.
- Promo ended while checkout open.
- Quota null.

### Testing

- CRUD test.
- Normalization test.
- Invalid date/value test.

### Acceptance Criteria

- Admin dapat mengelola promo.
- Schema siap quota reservation.

### Checklist

- [ ] CRUD
- [ ] Code normalized
- [ ] Date validation
- [ ] Quota fields


## TASK 10.2 — PromotionService, Quote, dan Reservation Quota

### Tujuan

Memvalidasi dan mereservasi promo pada booking secara concurrent-safe.

### Kondisi Sebelum Perubahan

Promo CRUD tersedia.

### File yang Harus Dibaca

- PricingService
- BookingService
- Promotion/Usage models
- checkout

### File yang Dibuat

- PromotionService
- promo validation endpoint/action
- tests

### File yang Diubah

- PricingService
- BookingService
- checkout UI

### Database yang Terlibat

- promotions
- promotion_usages
- bookings

### Detail Implementasi

Quote promo:

- normalize code;
- active;
- within time;
- subtotal minimum;
- compute discount;
- cap maximum.

Saat booking create:

1. lock promotion row;
2. revalidate;
3. count reserved+consumed;
4. enforce quota;
5. enforce per-user limit bila configured;
6. create booking;
7. create usage reserved;
8. snapshot promo code/discount.

Jika quota habis setelah preview, create harus menolak dengan pesan jelas.

Poin dan promo harus ditolak bila keduanya dikirim.

### Business Rules

- Server validation only.
- Quota reserved during pending hold.
- No stacking with points.

### Validation Rules

- Promo code valid.
- Subtotal eligible.
- Quota available.

### Security Considerations

- Transaction/lock.
- No client discount trust.

### Edge Cases

- Two users use last quota.
- Promo ends during hold.
- Guest per-user limit unavailable.

### Testing

- Quote test.
- Last-quota concurrency test.
- Promo+points reject.
- Max discount test.

### Acceptance Criteria

- Quota tidak oversold.
- Booking snapshot benar.

### Checklist

- [ ] Validate
- [ ] Calculate
- [ ] Reserve
- [ ] Concurrent quota
- [ ] No stacking


## TASK 10.3 — Consume/Release Promo dalam Lifecycle Booking

### Tujuan

Menjaga quota konsisten saat payment berhasil, booking expired, atau cancelled.

### Kondisi Sebelum Perubahan

Promo dapat reserved.

### File yang Harus Dibaca

- PromotionService
- webhook flow
- BookingService expire/cancel

### File yang Dibuat

- lifecycle integration tests

### File yang Diubah

- MidtransPaymentService
- BookingService

### Database yang Terlibat

- promotion_usages
- bookings
- payments

### Detail Implementasi

Transitions:

- booking pending dibuat → reserved;
- payment paid/booking confirmed → consumed;
- pending expired → released;
- pending cancelled → released.

Untuk confirmed lalu cancelled, kebijakan mengembalikan quota harus ditentukan. Rekomendasi V1: quota yang sudah consumed tidak otomatis kembali kecuali admin action eksplisit; ini mencegah penyalahgunaan dan membuat laporan stabil.

Semua transitions idempotent.

### Business Rules

- Reserved+consumed count toward active quota.
- Released no longer counts.
- No duplicate usage.

### Validation Rules

- Usage status transition valid.

### Security Considerations

- System-only mutation.
- Row lock where needed.

### Edge Cases

- Duplicate webhook.
- Expiry called twice.
- Paid then cancel.

### Testing

- Consume once.
- Release once.
- Duplicate event safe.

### Acceptance Criteria

- Quota lifecycle konsisten.
- Tidak ada leaked reserved quota.

### Checklist

- [ ] Consume on paid
- [ ] Release on expiry
- [ ] Release on pending cancel
- [ ] Idempotent



# FASE 11 — CHECK-IN, CHECK-OUT, COMPLETION, CANCELLATION, DAN REFUND

> **Kiro Spec:** SPEC 07 — Property Operations

## Tujuan Fase

Menyelesaikan lifecycle operasional booking dan menghubungkannya ke loyalty serta refund tanpa transisi status sembarangan.

## Exit Gate Fase

Admin dapat menjalankan check-in → check-out → completed, cancel/no-show secara valid, dan refund tercatat tanpa menghapus histori.


## TASK 11.1 — Check-in dengan Payment Warning dan Audit

### Tujuan

Membuat admin mencatat kedatangan aktual hanya dari state yang sah.

### Kondisi Sebelum Perubahan

Confirmed booking tersedia.

### File yang Harus Dibaca

- Booking transition guard
- Admin Reservation detail
- Payment status
- audit service

### File yang Dibuat

- check-in action/controller
- check-in confirmation UI
- tests

### File yang Diubah

- reservation detail actions
- BookingService transition

### Database yang Terlibat

- bookings
- booking_status_histories
- audit_logs

### Detail Implementasi

Check-in allowed dari `confirmed`.

Tampilkan:

- guest;
- room;
- dates;
- payment status;
- warning jika payment belum paid.

Untuk website booking normal, confirmed seharusnya paid. Untuk manual/OTA, status pembayaran dapat berbeda sesuai operasi.

Jika admin melanjutkan check-in dengan warning unpaid:

- confirmation explicit;
- reason/notes;
- audit.

Set `checked_in_at = now` dan history dalam transaction.

### Business Rules

- Only confirmed → checked_in.
- Actual timestamp server.
- Unpaid warning tidak boleh tersembunyi.

### Validation Rules

- Booking state valid.
- Admin confirmation for override.

### Security Considerations

- Admin guard.
- CSRF.
- Audit override.

### Edge Cases

- Early check-in.
- Already checked in.
- Room changed unexpectedly.

### Testing

- Normal check-in.
- Invalid state reject.
- Unpaid warning/override audit.

### Acceptance Criteria

- Check-in state valid dan traceable.

### Checklist

- [ ] State guard
- [ ] Timestamp
- [ ] Payment warning
- [ ] Audit


## TASK 11.2 — Check-out dan Complete dengan Award Loyalty Idempotent

### Tujuan

Menyelesaikan stay dan memberikan poin pada waktu yang benar.

### Kondisi Sebelum Perubahan

Booking checked_in tersedia; LoyaltyPointService award tersedia.

### File yang Harus Dibaca

- BookingService transition
- LoyaltyPointService
- reservation detail

### File yang Dibuat

- check-out action
- complete action
- tests lifecycle

### File yang Diubah

- admin reservation actions

### Database yang Terlibat

- bookings
- booking_status_histories
- loyalty_transactions
- users

### Detail Implementasi

Check-out:

- only checked_in;
- set checked_out_at;
- state checked_out.

Complete:

- only checked_out;
- set completed_at;
- state completed;
- setelah status commit atau dalam workflow transaksi terkoordinasi, panggil LoyaltyPointService idempotent.

Pastikan failure point award tidak membuat booking kembali checked_out tanpa jejak. Rekomendasi:

- status completion transaction committed;
- loyalty award idempotent dipanggil;
- jika award gagal, log/needs_attention dan dapat diretry.

Karena award memakai idempotency key, retry aman.

### Business Rules

- Award only completed.
- Complete repeated tidak double award.
- OTA source no award.

### Validation Rules

- Valid state sequence.

### Security Considerations

- Admin-only.
- Audit transitions.

### Edge Cases

- Award service temporary failure.
- Complete double click.
- Claim after completion.

### Testing

- Checked_in → checked_out.
- Checked_out → completed.
- Award once.
- Retry award safe.

### Acceptance Criteria

- Lifecycle selesai.
- Poin diberikan sesuai waktu dan sumber.

### Checklist

- [ ] Checkout
- [ ] Complete
- [ ] Award hook
- [ ] Retry safe


## TASK 11.3 — Cancellation dan No-show

### Tujuan

Menangani booking yang tidak dilanjutkan tanpa kehilangan histori dan tanpa otomatis refund.

### Kondisi Sebelum Perubahan

Booking lifecycle tersedia.

### File yang Harus Dibaca

- BookingService
- PromotionService
- LoyaltyPointService
- reservation detail

### File yang Dibuat

- cancel action/request
- no-show action/request
- tests

### File yang Diubah

- admin actions

### Database yang Terlibat

- bookings
- booking_status_histories
- promotion_usages
- loyalty_transactions
- audit_logs

### Detail Implementasi

Cancel pending:

- set cancelled;
- release promo;
- reverse redemption;
- room released.

Cancel confirmed:

- set cancelled;
- do not automatically refund;
- if payment paid, flag attention/refund decision.

No-show:

- allowed from confirmed;
- admin reason/confirmation;
- no automatic point.

Store cancellation fields and actor.

### Business Rules

- Terminal status.
- Paid cancellation != automatic refund.
- No-show no points.

### Validation Rules

- Reason required.
- State valid.

### Security Considerations

- Admin-only.
- Audit.
- No hidden refund call.

### Edge Cases

- Cancel after check-in.
- Duplicate cancel.
- Paid manual booking.

### Testing

- Pending cancel lifecycle.
- Paid confirmed cancel attention.
- No-show.
- Duplicate safe.

### Acceptance Criteria

- Cancellation safe dan inventory benar.

### Checklist

- [ ] Pending cancel
- [ ] Promo release
- [ ] Redeem reversal
- [ ] Paid attention
- [ ] No-show


## TASK 11.4 — Refund Full dan Partial Admin

### Tujuan

Mencatat dan memproses refund dengan batas nominal, provider response, dan loyalty correction.

### Kondisi Sebelum Perubahan

Payment paid dan cancellation flow tersedia.

### File yang Harus Dibaca

- Payment model
- Midtrans official refund API docs sesuai metode transaksi
- LoyaltyPointService
- admin payment detail

### File yang Dibuat

- migration refunds
- Refund model
- RefundService atau bagian payment service yang terpisah jelas
- Admin RefundController/Request
- views refund
- tests

### File yang Diubah

- Payment model relations
- admin payment/booking detail

### Database yang Terlibat

- refunds
- payments
- bookings
- loyalty_transactions
- audit_logs

### Detail Implementasi

Admin membuat refund request dengan amount dan reason.

Sebelum provider call:

- payment paid;
- amount > 0;
- cumulative successful refund + amount <= paid amount.

Status:

- requested;
- processing;
- succeeded/failed.

Set payment normalized status:

- full → refunded;
- partial → partial_refund.

Set booking status tidak otomatis diubah hanya karena refund; cancellation flow terpisah.

Jika booking completed dan earn sudah ada, hitung koreksi point sesuai kebijakan dan buat reversal, bukan delete.

### Business Rules

- No guest instant refund.
- No over-refund.
- Provider response persisted safely.
- History preserved.

### Validation Rules

- Amount integer.
- Reason required.
- Payment eligible.

### Security Considerations

- Admin-only.
- Re-auth/confirmation optional for production.
- No secret logs.

### Edge Cases

- Provider refund unsupported for method.
- Timeout after provider accepted.
- Duplicate submit.
- Partial multiple refunds.

### Testing

- Full refund.
- Partial refund.
- Over-refund reject.
- Duplicate idempotency.
- Point correction.

### Acceptance Criteria

- Refund traceable dan amount-safe.

### Checklist

- [ ] Refund schema
- [ ] Full/partial
- [ ] Max refundable
- [ ] Provider response
- [ ] Loyalty correction



# FASE 12 — INVOICE DAN WHATSAPP

> **Kiro Spec:** SPEC 07 — Property Operations

## Tujuan Fase

Menyediakan dokumen transaksi historis dan komunikasi manual melalui direct link WhatsApp tanpa menambah API berbayar.

## Exit Gate Fase

Invoice PDF menggunakan snapshot booking lama; guest/member/admin authorization benar; template WhatsApp bekerja dan tidak membocorkan token.


## TASK 12.1 — Invoice Number dan InvoiceService

### Tujuan

Membuat invoice dari data transaksi snapshot dengan nomor unik.

### Kondisi Sebelum Perubahan

Booking, payment, dan document sequence tersedia.

### File yang Harus Dibaca

- Booking model
- Payment model
- DocumentNumberService
- PDF package existing/decision

### File yang Dibuat

- InvoiceService
- invoice Blade template
- PDF controller
- invoice tests

### File yang Diubah

- member booking detail
- guest booking status
- admin reservation detail

### Database yang Terlibat

- bookings
- payments
- document_sequences

### Detail Implementasi

Tentukan kapan invoice number dibuat. Rekomendasi V1: saat payment menjadi paid atau saat admin menandai booking manual sebagai paid.

Generate invoice number idempotent. Jika sudah ada, reuse.

Invoice mengambil:

- booking snapshots;
- totals;
- promotion/point snapshots;
- effective payment;
- dates.

Jangan query current room price untuk isi historis.

### Business Rules

- Old invoice immutable by new room price.
- One invoice number per booking V1.
- No internal notes.

### Validation Rules

- Booking must be eligible.
- Invoice number unique.

### Security Considerations

- Authorization.
- No temporary file exposure.
- Escape content.

### Edge Cases

- Payment paid but invoice generation failed.
- Booking manual.
- Room renamed.

### Testing

- Historical snapshot test.
- Unique invoice concurrent test.
- Authorization test.

### Acceptance Criteria

- PDF dapat dibuat dan konsisten.
- Harga lama tetap sama setelah master berubah.

### Checklist

- [ ] Invoice number
- [ ] PDF template
- [ ] Snapshot only
- [ ] Authorization


## TASK 12.2 — Akses Invoice Guest, Member, dan Admin

### Tujuan

Membatasi invoice sesuai identitas dan konteks.

### Kondisi Sebelum Perubahan

InvoiceService tersedia.

### File yang Harus Dibaca

- GuestBookingAccess
- BookingPolicy
- admin guard
- invoice controller

### File yang Dibuat

- route terpisah atau authorization layer invoice
- tests IDOR

### File yang Diubah

- booking detail pages

### Database yang Terlibat

- bookings

### Detail Implementasi

Authorization:

- guest: booking token/manual verified access context;
- member: booking.user_id = current user;
- admin: admin guard.

Jangan membuat route `/invoice/{id}` yang hanya mengandalkan ID.

Header PDF/download harus tepat. Guest sensitive pages noindex.

### Business Rules

- One authorization rule per context.
- No public guessing.

### Validation Rules

- Booking eligible.

### Security Considerations

- Rate limit guest access.
- No token in analytics/log.

### Edge Cases

- Token expired policy.
- Booking claimed after guest link.

### Testing

- Guest correct token allowed.
- Wrong token denied.
- Member other booking denied.
- Admin allowed.

### Acceptance Criteria

- Tidak ada IDOR invoice.

### Checklist

- [ ] Guest auth
- [ ] Member ownership
- [ ] Admin access
- [ ] IDOR tests


## TASK 12.3 — Template dan Direct Link WhatsApp

### Tujuan

Memudahkan admin dan tamu berkomunikasi melalui link langsung.

### Kondisi Sebelum Perubahan

Settings contact tersedia dan booking detail stabil.

### File yang Harus Dibaca

- settings WhatsApp
- booking/invoice data
- public/member/admin detail pages

### File yang Dibuat

- WhatsAppLinkService/helper
- message templates
- tests encoding

### File yang Diubah

- CTA public
- booking status
- admin actions

### Database yang Terlibat

- settings
- bookings

### Detail Implementasi

Sediakan template:

- hubungi penginapan umum;
- detail booking;
- konfirmasi manual;
- pengingat manual.

Gunakan URL encoding.

Isi booking message minimum:

- booking code;
- dates;
- room snapshot;
- status.

Jangan memasukkan guest access token, claim token, internal notes, atau secret.

Nomor tujuan dari setting official. Untuk admin menghubungi guest, gunakan nomor booking snapshot normalized.

### Business Rules

- No paid API.
- Manual send.
- Template data from booking snapshot.

### Validation Rules

- WhatsApp number normalized.
- Message length reasonable.

### Security Considerations

- No secret/token.
- Escape/encode.

### Edge Cases

- Missing official number.
- Guest number invalid.
- Special characters.

### Testing

- URL encoding test.
- No token leakage test.
- Missing setting fallback.

### Acceptance Criteria

- WhatsApp link valid dan aman.

### Checklist

- [ ] Public contact
- [ ] Booking detail template
- [ ] Admin reminder
- [ ] No token leakage



# FASE 13 — LAPORAN DAN PENGELUARAN

> **Kiro Spec:** SPEC 08 — Reports, Security & Release

## Tujuan Fase

Menyediakan laporan operasional, pendapatan, occupancy, payment, loyalty, source booking, expense, dan estimasi laba bersih dengan definisi metrik yang konsisten.

## Exit Gate Fase

Admin dapat memfilter laporan; angka pending tidak dihitung sebagai pendapatan; occupancy terdefinisi; expense tercatat; estimasi laba diberi disclaimer.


## TASK 13.1 — Modul Pengeluaran

### Tujuan

Mencatat pengeluaran operasional sederhana dengan bukti opsional.

### Kondisi Sebelum Perubahan

Admin layout tersedia.

### File yang Harus Dibaca

- admin CRUD pattern
- file upload security helper
- audit service

### File yang Dibuat

- migration expenses
- Expense model
- ExpenseController
- ExpenseRequest
- views expense

### File yang Diubah

- admin routes/sidebar

### Database yang Terlibat

- expenses
- admins
- audit_logs

### Detail Implementasi

Kategori:

- listrik;
- air;
- internet;
- laundry;
- perlengkapan_kamar;
- perbaikan;
- gaji;
- lainnya.

Field: date, category, amount, description, receipt.

Receipt optional dengan validasi MIME/size dan random filename.

Edit/delete diaudit. Jika hard delete diizinkan untuk expense karena salah input, audit before value wajib; alternatif lebih aman adalah status void, tetapi jangan over-engineer jika belum perlu.

### Business Rules

- Amount integer Rupiah.
- Laporan menyebut estimasi, bukan accounting official.

### Validation Rules

- Date valid.
- Amount > 0.
- Description required.
- Receipt safe.

### Security Considerations

- Admin-only.
- Upload hardening.
- Audit.

### Edge Cases

- Receipt upload fails.
- Expense date future.
- Edit amount.

### Testing

- CRUD test.
- Upload validation.
- Filter category/date.

### Acceptance Criteria

- Pengeluaran dapat dicatat dan difilter.

### Checklist

- [ ] Schema
- [ ] Categories
- [ ] Receipt
- [ ] Audit


## TASK 13.2 — Laporan Reservasi, Pendapatan, dan Sumber Booking

### Tujuan

Menghasilkan laporan bisnis yang dapat ditelusuri ke data transaksi.

### Kondisi Sebelum Perubahan

Booking/payment data cukup tersedia.

### File yang Harus Dibaca

- Booking/Payment models
- enum source/status
- admin report layout

### File yang Dibuat

- ReportService atau query classes terpisah per report
- Admin ReportController
- views report reservation/revenue/source
- tests

### File yang Diubah

- admin routes/sidebar

### Database yang Terlibat

- bookings
- payments

### Detail Implementasi

Reservation report filters:

- booking created range;
- stay date range;
- status;
- source.

Revenue:

- hanya recognized paid amount setelah refund sesuai definisi;
- group source;
- date basis harus jelas: paid_at atau booking completion. Pilih satu untuk report default dan beri label.

Source report:

- count;
- revenue;
- average booking value.

Jangan membuat satu query raksasa yang sulit diuji.

### Business Rules

- Pending/unpaid not revenue.
- Refund reduces recognized revenue sesuai policy.
- Source enum labels consistent.

### Validation Rules

- Date range bounded.
- Enum filters valid.

### Security Considerations

- Admin-only.
- No raw SQL injection.

### Edge Cases

- Late payment.
- Partial refund.
- Manual cash booking.

### Testing

- Known fixture totals.
- Filter tests.
- Refund impact test.

### Acceptance Criteria

- Angka dapat direkonsiliasi dengan data detail.

### Checklist

- [ ] Reservation report
- [ ] Revenue report
- [ ] Source report
- [ ] Refund-aware


## TASK 13.3 — Laporan Occupancy dan Pembayaran

### Tujuan

Menghitung tingkat hunian dan status pembayaran dengan definisi formal.

### Kondisi Sebelum Perubahan

Room inventory, blocks, bookings, dan payments tersedia.

### File yang Harus Dibaca

- AvailabilityService
- Room/RoomBlock/Booking models
- Payment model

### File yang Dibuat

- occupancy report query/service
- payment report
- views/tests

### File yang Diubah

- ReportController/routes

### Database yang Terlibat

- rooms
- room_blocks
- bookings
- payments

### Detail Implementasi

Occupancy formula:

`occupied room nights / available room nights × 100%`.

Definisikan:

- available room nights mengecualikan room inactive sepanjang periode sesuai data yang tersedia;
- room block dapat mengurangi denominator bila memang tidak sellable;
- occupied nights berasal dari confirmed/checked-in/completed stay nights sesuai report business definition;
- pending payment tidak dianggap occupied.

Payment report:

- provider;
- status;
- payment type;
- paid date.

Tampilkan definisi metrik di UI/tooltips.

### Business Rules

- No divide by zero.
- Pending not occupancy.
- End date exclusive.

### Validation Rules

- Range valid and bounded.

### Security Considerations

- Admin-only.

### Edge Cases

- Room added mid-period.
- Room disabled without historical status log.
- All rooms blocked.

### Testing

- Fixture occupancy exact percent.
- Boundary dates.
- Zero denominator.
- Payment filters.

### Acceptance Criteria

- Occupancy reproducible dan definisinya jelas.

### Checklist

- [ ] Numerator defined
- [ ] Denominator defined
- [ ] Blocks handled
- [ ] Payment report


## TASK 13.4 — Laporan Loyalty dan Estimasi Laba Bersih

### Tujuan

Memberi insight point liability dan estimasi operasional.

### Kondisi Sebelum Perubahan

Loyalty ledger dan expenses tersedia.

### File yang Harus Dibaca

- LoyaltyPointService/ledger
- Expense model
- revenue report

### File yang Dibuat

- loyalty report
- profit estimate report
- views/tests

### File yang Diubah

- report navigation

### Database yang Terlibat

- loyalty_transactions
- expenses
- payments
- bookings

### Detail Implementasi

Loyalty report:

- earned;
- redeemed;
- expired;
- adjustment;
- reversal;
- outstanding balance.

Profit estimate:

`recognized revenue - expenses`

Tampilkan disclaimer eksplisit bahwa bukan laporan akuntansi resmi.

Gunakan date filters yang konsisten dan jelaskan basis tanggal.

### Business Rules

- Ledger types not merged ambiguously.
- Profit is estimate only.

### Validation Rules

- Date range valid.

### Security Considerations

- Admin-only.

### Edge Cases

- Reversal across period boundary.
- Expense backdated.

### Testing

- Known ledger aggregation.
- Profit fixture.
- Disclaimer visible.

### Acceptance Criteria

- Laporan dapat dijelaskan dan ditelusuri.

### Checklist

- [ ] Loyalty report
- [ ] Outstanding balance
- [ ] Profit estimate
- [ ] Disclaimer



# FASE 14 — SECURITY HARDENING

> **Kiro Spec:** SPEC 08 — Reports, Security & Release

## Tujuan Fase

Melakukan peninjauan keamanan menyeluruh setelah fitur utama selesai, dengan fokus pada authorization, webhook, file upload, rate limit, audit, secret, dan data exposure.

## Exit Gate Fase

Tidak ada IDOR yang diketahui; webhook terverifikasi; secret tidak bocor; upload dibatasi; sensitive routes rate-limited; audit tersedia.


## TASK 14.1 — Authorization dan IDOR Review

### Tujuan

Membuktikan setiap route membaca/mengubah hanya resource yang diizinkan.

### Kondisi Sebelum Perubahan

Fitur publik/member/admin telah tersedia.

### File yang Harus Dibaca

- seluruh route
- seluruh controller
- policies/gates
- middleware
- guest token access

### File yang Dibuat

- policy yang masih kurang
- security regression tests

### File yang Diubah

- route/controller yang ditemukan lemah

### Database yang Terlibat

- bookings
- payments
- loyalty_transactions
- users
- admins

### Detail Implementasi

Buat matriks route:

- public;
- guest-token;
- member-owner;
- admin.

Uji:

- member A mencoba booking B;
- guest token A mencoba booking B;
- invoice ID tampered;
- claim token untuk booking lain;
- member mencoba admin;
- inactive admin/member.

Jangan mengandalkan tombol hidden sebagai authorization.

### Business Rules

- Every sensitive action has server authorization.
- Route model binding still requires policy.

### Validation Rules

- Identifier tampering must fail.

### Security Considerations

- Use 403/404 strategy without leaking data.

### Edge Cases

- Claimed booking old guest token.
- Admin deactivated mid-session.

### Testing

- IDOR suite.
- Guard matrix.

### Acceptance Criteria

- Semua sensitive route memiliki authorization eksplisit.
- Cross-user access gagal.

### Checklist

- [ ] Route matrix
- [ ] Member IDOR
- [ ] Guest token IDOR
- [ ] Admin guard
- [ ] Invoice IDOR


## TASK 14.2 — Rate Limit, CSRF, Session, OAuth, dan Webhook Review

### Tujuan

Mengeraskan entry point yang rawan abuse tanpa memblokir flow sah.

### Kondisi Sebelum Perubahan

Route matrix tersedia.

### File yang Harus Dibaca

- route middleware
- CSRF config
- login/lookup/claim routes
- OAuth controller
- webhook route

### File yang Dibuat

- custom rate limit definitions bila perlu
- tests

### File yang Diubah

- route middleware

### Database yang Terlibat

- Tidak ada schema wajib.

### Detail Implementasi

Review:

- login member/admin;
- register;
- forgot password;
- cek booking;
- claim;
- availability high-frequency;
- payment resume;
- webhook.

CSRF hanya dikecualikan untuk webhook provider yang benar-benar perlu. OAuth state jangan dimatikan.

Session regenerate saat login dan privilege transition.

Webhook rate limit harus mempertimbangkan retry provider; signature tetap primary control.

### Business Rules

- No global CSRF disable.
- No OAuth stateless unless documented necessity.
- Guest lookup generic response.

### Validation Rules

- Rate values configured sensibly.

### Security Considerations

- Avoid account enumeration.
- Secure cookies production.
- SameSite appropriate.

### Edge Cases

- Provider burst.
- User behind NAT.
- Repeated payment polling.

### Testing

- Rate limit tests.
- CSRF tests.
- OAuth state test.
- Webhook still reachable.

### Acceptance Criteria

- Abuse surface berkurang tanpa memecahkan provider flow.

### Checklist

- [ ] Login limit
- [ ] Lookup limit
- [ ] Claim limit
- [ ] CSRF scope
- [ ] OAuth state


## TASK 14.3 — File Upload, XSS, Logging, dan Secret Review

### Tujuan

Mencegah upload berbahaya, output tak ter-escape, dan kebocoran credential.

### Kondisi Sebelum Perubahan

Semua modul upload dan integrasi sudah ada.

### File yang Harus Dibaca

- room image upload
- gallery upload
- expense receipt upload
- policy content rendering
- logging config
- `.env.example`
- CI config

### File yang Dibuat

- shared secure upload helper/service jika duplikasi nyata
- tests

### File yang Diubah

- upload validators
- views raw HTML yang tidak aman
- logging redaction

### Database yang Terlibat

- room_images
- galleries
- expenses
- policy_versions
- audit_logs

### Detail Implementasi

Audit repository untuk pola:

- `request->all()`;
- unescaped `{!! !!}`;
- original filename;
- unrestricted file type;
- hardcoded keys;
- logging entire request;
- raw webhook payload with sensitive fields;
- debug production.

Policy rich content harus disanitize saat write atau render dengan library/strategy aman.

Pastikan storage files tidak executable.

### Business Rules

- Random filenames.
- MIME and size validation.
- No secret in logs/repo.

### Validation Rules

- All upload routes have strict rules.

### Security Considerations

- Path traversal.
- Polyglot files.
- Stored XSS.

### Edge Cases

- No file from user controls server path.

### Testing

- Malicious extension rejection.
- Oversize rejection.
- XSS regression.
- Secret grep review.

### Acceptance Criteria

- Upload dan rendering hardened.
- No known secret committed.

### Checklist

- [ ] MIME
- [ ] Size
- [ ] Random name
- [ ] XSS
- [ ] Secret scan
- [ ] Logging redact


## TASK 14.4 — Audit Trail dan Anomaly Handling

### Tujuan

Memastikan aksi sensitif dan kasus pembayaran anomali dapat ditindaklanjuti.

### Kondisi Sebelum Perubahan

Audit log dan needs_attention digunakan di berbagai modul.

### File yang Harus Dibaca

- audit helper/service
- admin actions
- payment late path
- refund
- loyalty adjustment

### File yang Dibuat

- admin audit log view read-only bila diperlukan
- admin needs-attention queue/view
- tests

### File yang Diubah

- dashboard admin
- actions yang belum diaudit

### Database yang Terlibat

- audit_logs
- bookings
- payments
- refunds
- loyalty_transactions

### Detail Implementasi

Pastikan audit untuk:

- manual booking;
- price override;
- cancellation;
- unpaid check-in override;
- check-in/out/complete;
- refund;
- point adjustment/reversal;
- manual claim;
- policy publish;
- room block;
- important settings.

Needs attention cases:

- late paid after expiry;
- amount mismatch;
- unknown payment mapping;
- refund uncertain after timeout;
- loyalty award failure.

Admin view harus memprioritaskan kasus unresolved.

### Business Rules

- Audit append-only.
- No secret in before/after.
- Anomaly not silently ignored.

### Validation Rules

- Action/reason codes bounded.

### Security Considerations

- Admin-only audit view.
- PII exposure minimized.

### Edge Cases

- Actor deleted.
- System actor.
- Duplicate anomaly.

### Testing

- Audit event tests.
- Needs attention dashboard test.

### Acceptance Criteria

- Aksi sensitif traceable.
- Anomali operasional terlihat.

### Checklist

- [ ] Audit coverage
- [ ] No secret
- [ ] Attention queue
- [ ] Dashboard link



# FASE 15 — TESTING FINAL DAN PRODUCTION READINESS

> **Kiro Spec:** SPEC 08 — Reports, Security & Release

## Tujuan Fase

Menjalankan verifikasi end-to-end, concurrency, Midtrans Sandbox, responsive, scheduler, deployment, dan kesiapan produksi sebelum secret production dipasang.

## Exit Gate Fase

Seluruh acceptance criteria global lulus, critical test hijau, checklist Sandbox lengkap, backup/rollback/deployment jelas, dan production mode tidak diaktifkan prematur.


## TASK 15.1 — Regression Test dan Critical Flow Matrix

### Tujuan

Menjalankan semua automated tests dan memastikan flow utama tidak rusak setelah integrasi lintas fase.

### Kondisi Sebelum Perubahan

Seluruh fitur utama telah diimplementasikan.

### File yang Harus Dibaca

- seluruh tests
- test config
- CI config
- critical services

### File yang Dibuat

- missing regression tests yang ditemukan
- test matrix document internal bila convention project mendukung

### File yang Diubah

- kode yang gagal test hanya setelah root cause dipahami

### Database yang Terlibat

- Semua tabel domain.

### Detail Implementasi

Buat matriks flow:

1. guest search → checkout → booking → Snap → paid;
2. member booking;
3. booking with points;
4. booking with promo;
5. pending expiry;
6. manual booking;
7. OTA calendar;
8. check-in/out/complete;
9. point award;
10. claim guest booking;
11. invoice;
12. refund.

Jalankan full suite. Jangan mengubah expected test hanya untuk membuat hijau jika perilaku sebenarnya salah.

### Business Rules

- Critical flows must be automated as far as practical.
- Known flaky test must be fixed, not ignored.

### Validation Rules

- Test data isolated.

### Security Considerations

- No production DB.
- No real Midtrans production call.

### Edge Cases

- Timezone-dependent test.
- Parallel order.
- Mail/queue fake.

### Testing

- `php artisan test` atau command project.
- Targeted retry after fixes.
- Coverage review by risk, not percentage only.

### Acceptance Criteria

- Full suite green.
- Critical flow matrix covered.

### Checklist

- [ ] Full test green
- [ ] Guest flow
- [ ] Member flow
- [ ] Loyalty
- [ ] Promo
- [ ] Admin flow


## TASK 15.2 — Final Concurrency dan Data Integrity Test

### Tujuan

Membuktikan sistem tahan race pada booking, promo, loyalty, dan document sequence.

### Kondisi Sebelum Perubahan

Regression suite hijau.

### File yang Harus Dibaca

- BookingService
- PromotionService
- LoyaltyPointService
- DocumentNumberService
- MySQL test setup

### File yang Dibuat

- concurrency tests tambahan bila coverage kurang

### File yang Diubah

- service yang gagal integrity test

### Database yang Terlibat

- rooms
- bookings
- promotion_usages
- loyalty_transactions
- document_sequences

### Detail Implementasi

Run concurrent scenarios:

- same physical room/same dates;
- last promo quota;
- same member points in two tabs;
- booking code generation;
- duplicate webhook.

Verify database post-condition, bukan hanya HTTP response.

Setelah test:

- no negative loyalty;
- no duplicate earn;
- no over-quota;
- no duplicate blocking booking;
- unique codes.

### Business Rules

- Exactly one winner where resource exclusive.
- Loser gets safe business error.

### Validation Rules

- MySQL required.

### Security Considerations

- Deadlock handling should be bounded and observable.

### Edge Cases

- CI parallel limitations.
- Transaction isolation differences.

### Testing

- Repeat concurrency tests several times.
- Inspect final rows.

### Acceptance Criteria

- Integrity invariants hold under race.

### Checklist

- [ ] Double booking test
- [ ] Promo quota test
- [ ] Point overspend test
- [ ] Sequence test
- [ ] Duplicate webhook test


## TASK 15.3 — Manual UX, Mobile, Accessibility, dan Browser Verification

### Tujuan

Memastikan sistem nyaman dipakai tamu dan admin di perangkat nyata.

### Kondisi Sebelum Perubahan

Backend tests hijau.

### File yang Harus Dibaca

- public/member/admin pages
- responsive components
- forms
- Snap integration

### File yang Dibuat

- Tidak wajib file baru; perbaiki issue yang ditemukan

### File yang Diubah

- views/CSS/Alpine yang bermasalah

### Database yang Terlibat

- Tidak ada schema wajib.

### Detail Implementasi

Verifikasi minimum pada viewport mobile dan desktop:

Public:

- home;
- date form;
- room list/detail;
- availability;
- checkout;
- pending payment;
- booking status;
- login/register;
- member dashboard.

Admin:

- sidebar;
- calendar horizontal;
- reservation form;
- report filters.

Check:

- touch targets;
- keyboard focus;
- validation messages;
- empty states;
- long guest names;
- Rupiah formatting;
- time/date labels.

### Business Rules

- Booking CTA remains obvious.
- No horizontal overflow except intentional calendar.
- No color-only status without label.

### Validation Rules

- Forms retain safe input after validation error.

### Security Considerations

- No sensitive data in client source.

### Edge Cases

- Small screen.
- Slow network.
- No room image.
- Long policy.

### Testing

- Manual browser matrix.
- Frontend build production.
- Console error check.

### Acceptance Criteria

- Core flow comfortable on mobile.
- No blocking UI bug.

### Checklist

- [ ] Mobile public
- [ ] Mobile checkout
- [ ] Member
- [ ] Admin calendar
- [ ] Keyboard/focus
- [ ] Console clean


## TASK 15.4 — Deployment, Scheduler, Queue, Backup, dan Production Switch

### Tujuan

Menyiapkan operasi produksi tanpa mengaktifkan credential production sebelum semua verifikasi lulus.

### Kondisi Sebelum Perubahan

Sandbox dan tests lulus.

### File yang Harus Dibaca

- deployment docs
- server config
- scheduler
- queue config
- storage
- env example
- Midtrans dashboard config
- Google OAuth redirect URIs

### File yang Dibuat

- deployment checklist/runbook
- rollback notes
- health check notes bila project mendukung

### File yang Diubah

- production config template tanpa secret

### Database yang Terlibat

- Semua migration.

### Detail Implementasi

Deployment checklist:

- PHP/extensions;
- Composer install production;
- npm build;
- env secret injection;
- APP_KEY;
- DB backup;
- migrate `--force` only after backup/review;
- storage link;
- permissions;
- scheduler cron;
- queue worker if used;
- mail;
- HTTPS;
- trusted proxy/secure cookie config;
- Midtrans notification URL;
- Google callback;
- logging/monitoring.

Production switch Midtrans:

1. Sandbox checklist fully green;
2. production keys inserted outside repo;
3. `MIDTRANS_IS_PRODUCTION=true`;
4. production notification URL verified;
5. low-value controlled transaction;
6. verify webhook and status;
7. monitor needs attention.

Jangan mengubah production mode hanya karena coding selesai.

### Business Rules

- Backup before migration.
- Rollback plan.
- HTTPS required for production payment/OAuth.
- Scheduler mandatory.

### Validation Rules

- Production env complete.

### Security Considerations

- Secrets manager/env.
- APP_DEBUG false.
- Secure cookies.
- No dev admin password.

### Edge Cases

- Migration failure.
- Webhook URL unreachable.
- Scheduler missing.
- Queue stopped.

### Testing

- Config cache.
- Route cache if compatible.
- Scheduler list.
- Health smoke test.
- Controlled production payment only after approval.

### Acceptance Criteria

- Runbook complete.
- No production secret in repo.
- Operational tasks active.

### Checklist

- [ ] Backup
- [ ] Migration plan
- [ ] Scheduler
- [ ] Queue if used
- [ ] HTTPS
- [ ] Midtrans URL
- [ ] Google callback
- [ ] APP_DEBUG false




# 26. DEFINITION OF DONE SETIAP TASK

Sebuah task **belum selesai** hanya karena source code telah ditulis.

Task dianggap selesai jika:

1. seluruh file target telah dibaca sebelum perubahan;
2. implementasi mengikuti pattern project;
3. migration aman;
4. validation server-side ada;
5. authorization ada bila diperlukan;
6. error handling ada;
7. test target ada dan lulus;
8. test regression relevan lulus;
9. frontend build lulus bila view/asset berubah;
10. tidak ada secret hardcoded;
11. tidak ada TODO kritis yang disembunyikan;
12. ringkasan perubahan dibuat.

## Format Ringkasan Kiro Setelah Setiap Task

Kiro harus melaporkan:

### Spec Aktif

- nama/nomor Spec;
- fase master requirements terkait.

### Task Selesai

- task number;
- tujuan singkat;
- requirement yang dipenuhi.

### Artifact Spec

- apakah requirements berubah;
- apakah design berubah;
- apakah tasks/progress diperbarui;
- alasan perubahan jika ada.

### Steering

- apakah ada keputusan persistent yang berubah;
- file Steering yang diperbarui jika memang perlu;
- jangan mengubah Steering untuk detail sementara.

### File Dibuat

- daftar.

### File Diubah

- daftar.

### Database

- migration/schema changes;
- migration dijalankan atau belum;
- rollback consideration.

### Business Rule yang Diimplementasikan

- daftar ringkas.

### Test yang Dijalankan

- command;
- hasil nyata;
- test yang belum dapat dijalankan dan alasannya.

### Risiko/Remaining Issue

- hanya issue nyata;
- jangan mengklaim selesai jika masih gagal.

### Checklist

- checked items;
- exit gate task.

Jangan menulis laporan seolah-olah test lulus jika command tidak dijalankan. Jangan menandai task Spec selesai hanya karena file telah dibuat.

---

# 27. ERROR HANDLING DAN DOMAIN EXCEPTION

Gunakan domain exception terstruktur untuk kasus bisnis, misalnya:

- `InvalidStayDateRange`;
- `RoomNoLongerAvailable`;
- `RoomCapacityExceeded`;
- `BookingExpired`;
- `InvalidBookingTransition`;
- `PromotionInvalid`;
- `PromotionQuotaExhausted`;
- `LoyaltyBalanceInsufficient`;
- `LoyaltyRedemptionLimitExceeded`;
- `PaymentAmountMismatch`;
- `PaymentSignatureInvalid`;
- `BookingClaimNotAllowed`.

Controller mengubah exception menjadi:

- validation error bila input salah;
- HTTP 409 untuk race/resource no longer available;
- 403 untuk authorization;
- 404 bila strategi anti-enumeration memerlukan;
- safe redirect/message untuk guest.

Jangan menampilkan stack trace di production.

---

# 28. OBSERVABILITY MINIMUM

Tanpa membuat monitoring system berlebihan, pastikan:

1. payment creation error memiliki correlation context;
2. webhook processing status tercatat;
3. payment reconciliation error tercatat;
4. booking needs attention terlihat admin;
5. scheduler failure dapat ditemukan di log;
6. loyalty reconciliation mismatch dapat ditemukan;
7. audit trail tersedia.

Gunakan context:

- booking code;
- payment ID;
- provider order ID;
- user/admin ID bila aman.

Jangan gunakan context:

- raw token;
- password;
- Server Key;
- OAuth secret.

---

# 29. TRACEABILITY KEBUTUHAN KE MODUL

| Kebutuhan | Modul Utama |
|---|---|
| Guest booking tanpa login | Public Checkout + BookingService |
| Room availability | AvailabilityService |
| Double booking | Room row lock + transaction + overlap recheck |
| 30 menit hold | BookingService + Scheduler |
| Midtrans Snap | MidtransPaymentService |
| Webhook aman | Webhook controller + PaymentWebhookEvent |
| Resume payment | Payment endpoint + active attempt resolver |
| Cek booking guest | GuestBookingAccess |
| Google login | Socialite + SocialAccount |
| Member history | Member BookingController + Policy |
| Claim guest booking | BookingClaimService |
| Earn point | LoyaltyPointService on completed |
| Redeem point | Pricing + LoyaltyPointService |
| Point expiry | Scheduler + FIFO lots |
| Promo | PromotionService |
| Booking manual | Admin Reservation + BookingService |
| OTA calendar | Booking source + common inventory |
| Room block | RoomBlock + AvailabilityService |
| Check-in/out | State-specific admin actions |
| Invoice | InvoiceService |
| WhatsApp | Direct Link Service |
| Reports | Report services/queries |
| Expenses | Expense module |
| Audit | AuditLog |
| Production readiness | Phase 15 |

---

# 30. ACCEPTANCE CRITERIA GLOBAL

Sistem dianggap berhasil hanya jika seluruh poin berikut terpenuhi.

1. Guest dapat booking tanpa login.
2. Member dapat login Google.
3. Member dapat login email.
4. Kamar hanya tampil jika tersedia.
5. Double booking dicegah.
6. Pending booking mengunci kamar.
7. Hold expired otomatis.
8. Midtrans Snap bekerja di Sandbox sebelum Production.
9. Webhook terverifikasi memperbarui pembayaran.
10. Status payment terpisah dari booking.
11. Member mendapat poin setelah booking completed.
12. Poin hanya diberikan sekali per booking.
13. Poin dapat digunakan maksimal 20% dari nilai booking eligible.
14. Poin dan promo tidak dapat digabung pada V1.
15. Guest booking dapat diklaim secara aman.
16. Admin dapat membuat booking manual.
17. Booking OTA masuk kalender kamar yang sama.
18. Admin dapat check-in dan check-out dengan transisi valid.
19. Room block memblokir availability.
20. Invoice dapat dibuat dari snapshot.
21. Histori booking tersimpan.
22. Histori poin tersimpan dan tidak dihapus.
23. Website nyaman di HP.
24. Seluruh flow kritis memiliki test.

Tambahan wajib:

25. Dua request concurrent untuk kamar sama hanya menghasilkan satu booking blocking.
26. Duplicate webhook tidak memproses pembayaran dua kali.
27. Late payment setelah booking expired tidak otomatis menimpa inventory.
28. Satu booking tidak dapat memberikan poin dua kali.
29. Satu member tidak dapat menghabiskan poin yang sama di dua checkout concurrent.
30. Promo quota tidak dapat oversold.
31. Guest/member tidak dapat membaca booking orang lain.
32. Secret tidak ada di repository.
33. Scheduler aktif di production.
34. Invoice lama tidak berubah ketika harga master berubah.
35. Booking lama tidak berubah ketika room type/room name diubah.
36. Production Midtrans tidak aktif sebelum Sandbox checklist lulus.

---

# 31. JANGAN LAKUKAN INI

1. Jangan hapus fitur yang sudah ada tanpa analisis.
2. Jangan hardcode API key atau secret.
3. Jangan percaya harga dari frontend.
4. Jangan percaya status pembayaran dari frontend.
5. Jangan memberi poin dua kali.
6. Jangan memberi poin sebelum booking completed.
7. Jangan izinkan double booking.
8. Jangan membuat booking tanpa overlap check authoritative.
9. Jangan mewajibkan akun untuk booking.
10. Jangan mencampur payment status dan booking status.
11. Jangan claim booking berdasarkan nama.
12. Jangan claim booking berdasarkan WhatsApp tidak terverifikasi.
13. Jangan menghapus histori poin.
14. Jangan mengubah booking lama mengikuti harga baru.
15. Jangan menganggap callback JS sebagai bukti pembayaran.
16. Jangan menggabungkan promo dan poin pada V1.
17. Jangan menjalankan call Midtrans sambil memegang room lock terlalu lama.
18. Jangan membuat sequence dengan `count + 1`.
19. Jangan menyimpan raw guest token bila hash cukup.
20. Jangan menaruh Server Key di JavaScript.
21. Jangan menonaktifkan CSRF global untuk webhook.
22. Jangan mematikan OAuth state hanya agar callback mudah.
23. Jangan menggunakan SQLite saja untuk concurrency test.
24. Jangan hard delete booking/payment/ledger.
25. Jangan membuat report pending payment sebagai revenue.
26. Jangan menyebut estimasi laba sebagai laporan akuntansi resmi.
27. Jangan mengarang fasilitas penginapan.
28. Jangan memasang dependency lama hanya karena contoh tutorial.
29. Jangan membuat satu God Service.
30. Jangan menyebar magic string status.

---

# FINAL PROJECT VERIFICATION CHECKLIST

> Checklist ini wajib diperiksa setelah seluruh fase selesai. Jangan menandai item selesai tanpa bukti test/manual verification.

## Public Website

- [ ] Beranda menampilkan nama Penginapan Kelapa Sawit.
- [ ] Lokasi Kota Bangun, Kalimantan Timur ditampilkan dengan benar.
- [ ] Hero memiliki CTA booking.
- [ ] Tombol WhatsApp bekerja.
- [ ] Harga mulai dari berasal dari data aktif.
- [ ] Tipe kamar berasal dari database.
- [ ] Detail kamar menggunakan slug bersih.
- [ ] Fasilitas yang ditampilkan hanya data nyata.
- [ ] Foto mempunyai alt text.
- [ ] Tentang tersedia.
- [ ] Lokasi dan Kontak tersedia.
- [ ] Kebijakan current tersedia.
- [ ] Meta title dan description tersedia.
- [ ] Open Graph tersedia.
- [ ] Sitemap tidak memuat URL sensitif.
- [ ] Guest access pages `noindex`.

## Booking

- [ ] Guest tidak dipaksa login.
- [ ] Check-in required.
- [ ] Check-out required.
- [ ] Check-out setelah check-in.
- [ ] Guest count minimal 1.
- [ ] Capacity enforced.
- [ ] Guest name tersimpan.
- [ ] WhatsApp tersimpan normalized.
- [ ] Email tersimpan sesuai rule.
- [ ] Arrival estimate didukung.
- [ ] Special request didukung.
- [ ] Policy checkbox wajib.
- [ ] Policy version tersimpan.
- [ ] Booking code bukan ID mentah.
- [ ] Snapshot room type tersimpan.
- [ ] Snapshot room name tersimpan.
- [ ] Snapshot price tersimpan.
- [ ] Nights tersimpan.
- [ ] Subtotal tersimpan.
- [ ] Discounts tersimpan.
- [ ] Total final tersimpan.
- [ ] Idempotency key mencegah submit ganda.

## Availability

- [ ] Ketersediaan berdasarkan kamar fisik.
- [ ] Room type inactive tidak muncul.
- [ ] Room inactive tidak muncul.
- [ ] Capacity difilter.
- [ ] Booking blocking difilter.
- [ ] Active pending hold difilter.
- [ ] Expired pending tidak memblokir.
- [ ] Room block memblokir.
- [ ] Formula overlap benar.
- [ ] Checkout date dapat dipakai sebagai check-in booking berikutnya.
- [ ] Search result hanya informasional.
- [ ] Authoritative recheck dilakukan saat create.

## Double Booking Protection

- [ ] Search check tersedia.
- [ ] Checkout recheck tersedia.
- [ ] Booking create recheck tersedia.
- [ ] DB transaction digunakan.
- [ ] Room row `lockForUpdate` digunakan.
- [ ] External API tidak dipanggil di dalam room lock transaction.
- [ ] Parallel concurrency test menggunakan MySQL.
- [ ] Dua request kamar sama hanya satu sukses.
- [ ] Post-condition database diperiksa.
- [ ] Conflict response user-friendly.

## Midtrans

- [ ] SDK resmi/kompatibel digunakan.
- [ ] Server Key hanya backend.
- [ ] Client Key dikonfigurasi sesuai Snap.
- [ ] Sandbox default.
- [ ] Provider order ID unique.
- [ ] Gross amount berasal dari booking.
- [ ] Snap token dibuat backend.
- [ ] Snap dapat dibuka.
- [ ] Close Snap tidak menghapus booking.
- [ ] Lanjutkan Pembayaran bekerja.
- [ ] Resume tidak membuat booking baru.
- [ ] Expired booking tidak dapat membuat payment baru.
- [ ] Multiple payment attempts memiliki histori.

## Webhook

- [ ] Endpoint provider dapat diakses.
- [ ] CSRF exception hanya scoped.
- [ ] Signature diverifikasi.
- [ ] Provider order ID diverifikasi.
- [ ] Nominal diverifikasi.
- [ ] Deduplication key digunakan.
- [ ] Duplicate event aman.
- [ ] Payment row di-lock.
- [ ] Booking row di-lock.
- [ ] Out-of-order event tidak downgrade paid.
- [ ] Callback frontend tidak dianggap bukti.
- [ ] Late payment menghasilkan attention.
- [ ] Webhook tidak award points.
- [ ] Payload log ter-redact.
- [ ] Status API reconciliation tersedia.

## Guest Checkout

- [ ] Guest booking tanpa akun berhasil.
- [ ] Hold 30 menit tersimpan server-side.
- [ ] Countdown hanya display.
- [ ] Server menentukan expiry.
- [ ] Expiry scheduler bekerja.
- [ ] Guest access token acak.
- [ ] Database menyimpan hash token.
- [ ] Token salah ditolak.
- [ ] Manual cek booking memerlukan verifikasi tambahan.
- [ ] Lookup rate-limited.
- [ ] Data sensitif tidak tampil sebelum verifikasi.
- [ ] Invoice guest terlindungi.

## Google Login

- [ ] Laravel Socialite digunakan.
- [ ] Google Client Secret hanya env.
- [ ] OAuth state aktif.
- [ ] Provider ID unique.
- [ ] Social account terpisah.
- [ ] Verified provider email diperiksa sebelum auto-link.
- [ ] Duplicate callback aman.
- [ ] Google-only user didukung.
- [ ] OAuth token tidak disimpan tanpa kebutuhan.
- [ ] Test flow dapat dimock.

## Member

- [ ] Register email.
- [ ] Login email.
- [ ] Logout.
- [ ] Email verification.
- [ ] Forgot/reset password.
- [ ] Profile.
- [ ] WhatsApp profile.
- [ ] Email change aman.
- [ ] Autofill checkout.
- [ ] Booking aktif.
- [ ] Riwayat selesai.
- [ ] Batal/expired.
- [ ] Booking ownership policy.
- [ ] Member A tidak dapat booking B.
- [ ] Dashboard memberi manfaat nyata.

## Loyalty Point

- [ ] Ledger menjadi sumber kebenaran.
- [ ] Balance cache bukan sumber tunggal.
- [ ] Rp1.000 = 1 poin.
- [ ] Pembulatan ke bawah.
- [ ] 1 poin = Rp50.
- [ ] Minimal redeem 100.
- [ ] Maksimal discount 20%.
- [ ] Poin dan promo tidak bersamaan.
- [ ] Earn hanya completed.
- [ ] Earn hanya sekali.
- [ ] Website eligible.
- [ ] WhatsApp eligible.
- [ ] Walk-in eligible.
- [ ] Booking.com tidak eligible V1.
- [ ] Agoda tidak eligible V1.
- [ ] Traveloka tidak eligible V1.
- [ ] Eligible sources configurable.
- [ ] Poin expired 18 bulan.
- [ ] FIFO lot digunakan.
- [ ] Expiry scheduler idempotent.
- [ ] Redeem menggunakan lock.
- [ ] Two-tab overspend dicegah.
- [ ] Redemption reversal tersedia.
- [ ] Earn reversal tersedia.
- [ ] Histori tidak dihapus.
- [ ] Admin adjustment diaudit.
- [ ] Outstanding balance dapat direkonsiliasi.

## Promo

- [ ] Code normalized.
- [ ] Percentage supported.
- [ ] Fixed supported.
- [ ] Start/end supported.
- [ ] Minimum booking supported.
- [ ] Maximum discount supported.
- [ ] Active status supported.
- [ ] Usage quota supported.
- [ ] Quote backend.
- [ ] Promo row lock saat reserve.
- [ ] Last quota concurrency aman.
- [ ] Reserved status.
- [ ] Consumed status.
- [ ] Released status.
- [ ] Expiry releases reserved quota.
- [ ] Pending cancel releases quota.
- [ ] Promo snapshot tersimpan.
- [ ] Promo + points ditolak.

## Admin

- [ ] Admin guard terpisah.
- [ ] Tidak ada public admin registration.
- [ ] Dashboard operasional.
- [ ] Booking hari ini.
- [ ] Check-in hari ini.
- [ ] Check-out hari ini.
- [ ] Occupied rooms.
- [ ] Available rooms.
- [ ] Active pending payment.
- [ ] Month revenue.
- [ ] Recent bookings.
- [ ] Needs attention.
- [ ] Reservation filters.
- [ ] Booking manual.
- [ ] Source lengkap.
- [ ] Price override diaudit.
- [ ] Tamu view.
- [ ] Payment view.
- [ ] Loyalty admin.
- [ ] Promo admin.
- [ ] Gallery admin.
- [ ] Expense admin.
- [ ] Settings admin.
- [ ] Policy versioning.

## Calendar

- [ ] Row adalah kamar fisik.
- [ ] Column adalah tanggal.
- [ ] Available status.
- [ ] Pending status.
- [ ] Confirmed status.
- [ ] Checked-in status.
- [ ] Blocked status.
- [ ] Legend tersedia.
- [ ] Date range filter.
- [ ] Horizontal scroll.
- [ ] Sticky room labels.
- [ ] Click booking.
- [ ] Add manual booking dari kalender.
- [ ] Room block dari kalender.
- [ ] OTA booking muncul.
- [ ] Pending expired tidak tampak blocking.

## Check-in

- [ ] Hanya dari confirmed.
- [ ] Payment status terlihat.
- [ ] Unpaid warning.
- [ ] Override diaudit.
- [ ] Actual timestamp tersimpan.
- [ ] Double check-in ditolak.

## Check-out

- [ ] Hanya dari checked_in.
- [ ] Actual timestamp tersimpan.
- [ ] State menjadi checked_out.
- [ ] Complete terpisah.
- [ ] Complete memicu award idempotent.
- [ ] Retry award aman.

## Invoice

- [ ] Format `INV-YYYYMM-0001`.
- [ ] Sequence concurrency safe.
- [ ] Booking code ditampilkan.
- [ ] Guest ditampilkan.
- [ ] Room snapshot.
- [ ] Dates.
- [ ] Nights.
- [ ] Price snapshot.
- [ ] Subtotal.
- [ ] Promo discount.
- [ ] Point redemption/discount.
- [ ] Total.
- [ ] Payment.
- [ ] Status.
- [ ] Old invoice tidak berubah oleh harga baru.
- [ ] Guest authorization.
- [ ] Member authorization.
- [ ] Admin authorization.

## WhatsApp

- [ ] Direct link, bukan API berbayar.
- [ ] Official number dari setting.
- [ ] Public contact button.
- [ ] Booking detail template.
- [ ] Confirmation template.
- [ ] Manual reminder template.
- [ ] URL encoded.
- [ ] Guest access token tidak masuk pesan.
- [ ] Claim token tidak masuk pesan.
- [ ] Internal notes tidak masuk pesan.

## Reports

- [ ] Reservation report.
- [ ] Date filter.
- [ ] Status filter.
- [ ] Source filter.
- [ ] Revenue by source.
- [ ] Pending tidak dihitung revenue.
- [ ] Refund impact handled.
- [ ] Occupancy report.
- [ ] Occupancy definition displayed.
- [ ] Payment report.
- [ ] Loyalty report.
- [ ] Source booking report.
- [ ] Data dapat ditelusuri.

## Security

- [ ] CSRF aktif.
- [ ] XSS output escaped.
- [ ] Policy rich text sanitized.
- [ ] Mass assignment terkontrol.
- [ ] Authorization policies.
- [ ] IDOR tests.
- [ ] Login rate limit.
- [ ] Guest lookup rate limit.
- [ ] Claim rate limit.
- [ ] OAuth state.
- [ ] Password hashing.
- [ ] Token hashed.
- [ ] Token expiry.
- [ ] Claim one-time.
- [ ] Secret di env.
- [ ] No secret in Git.
- [ ] No secret in logs.
- [ ] Upload MIME validation.
- [ ] Upload size limit.
- [ ] Random filenames.
- [ ] No executable upload.
- [ ] Audit trail.
- [ ] Needs attention handling.

## Mobile Responsive

- [ ] Beranda mobile.
- [ ] Availability form mobile.
- [ ] Search result mobile.
- [ ] Room detail mobile.
- [ ] Checkout mobile.
- [ ] Payment status mobile.
- [ ] Login/register mobile.
- [ ] Member dashboard mobile.
- [ ] Admin sidebar usable.
- [ ] Admin calendar scroll usable.
- [ ] CTA mudah ditekan.
- [ ] Validation message terbaca.
- [ ] No accidental horizontal overflow.

## Testing

- [ ] Unit night calculation.
- [ ] Unit overlap boundaries.
- [ ] Unit pricing.
- [ ] Unit promo percentage.
- [ ] Unit promo fixed.
- [ ] Unit point earn.
- [ ] Unit point redeem.
- [ ] Unit 20% cap.
- [ ] Unit expiry.
- [ ] Guest booking feature.
- [ ] Member booking feature.
- [ ] Double booking concurrency.
- [ ] Hold expiry.
- [ ] Google login mock.
- [ ] Webhook signature.
- [ ] Webhook duplicate.
- [ ] Webhook amount mismatch.
- [ ] Webhook late payment.
- [ ] Claim.
- [ ] Check-in.
- [ ] Check-out.
- [ ] Complete.
- [ ] Point award once.
- [ ] Promo quota concurrency.
- [ ] Point overspend concurrency.
- [ ] Full regression green.
- [ ] Frontend production build green.

## Production Readiness

- [ ] Laravel/PHP version verified.
- [ ] Production APP_DEBUG false.
- [ ] HTTPS enabled.
- [ ] Secure cookies configured.
- [ ] Production DB backup.
- [ ] Migration reviewed.
- [ ] Rollback plan.
- [ ] Scheduler cron active.
- [ ] Queue worker active if used.
- [ ] Storage permissions correct.
- [ ] Mail configured.
- [ ] Midtrans Sandbox fully tested.
- [ ] Production keys outside repository.
- [ ] Production notification URL reachable.
- [ ] Google production callback exact.
- [ ] No dev admin password fallback.
- [ ] Logging/monitoring reviewed.
- [ ] Controlled production payment verified.
- [ ] Late payment attention visible.
- [ ] Payment reconciliation scheduled.
- [ ] Point expiry scheduled.
- [ ] Backup and recovery procedure documented.

---

# PERINTAH PENUTUP UNTUK KIRO

Mulai dari **Fase 0**.

Jangan melompat langsung ke Midtrans, loyalty, dashboard, atau implementasi fitur apa pun sebelum audit, Steering, dan persiapan SPEC 01 selesai.

Jangan membuat seluruh aplikasi dalam satu patch atau satu Spec raksasa.

Untuk setiap Spec:

1. baca bagian master requirements yang relevan;
2. baca source code aktual;
3. pastikan Steering masih benar;
4. siapkan/review requirements;
5. siapkan/review design;
6. siapkan/review tasks;
7. implementasikan satu task kecil;
8. jalankan test;
9. perbaiki kegagalan;
10. update progress Spec;
11. lanjut hanya setelah checklist dan exit gate terpenuhi.

Untuk setiap task implementasi:

1. baca file yang diwajibkan;
2. jelaskan kondisi awal berdasarkan project nyata;
3. implementasikan perubahan sekecil yang diperlukan;
4. jangan mengubah scope diam-diam;
5. jalankan test nyata;
6. perbaiki kegagalan;
7. laporkan file, database impact, artifact Spec, Steering impact, dan hasil test;
8. lanjut hanya setelah checklist task terpenuhi.

Prioritas tertinggi proyek ini adalah:

**tidak ada double booking, tidak ada pembayaran palsu, tidak ada poin ganda, tidak ada akses data booking milik orang lain, dan tidak ada secret yang bocor.**

---

# PROMPT PERTAMA YANG HARUS DIKIRIM KE KIRO

Gunakan prompt berikut setelah file ini diletakkan di root project dan workspace dibuka di Kiro:

```text
Baca secara menyeluruh file:

MASTER_REQUIREMENTS_KIRO_PENGINAPAN_KELAPA_SAWIT.md

File tersebut adalah sumber kebenaran utama kebutuhan bisnis, teknis, keamanan, dan roadmap project Penginapan Kelapa Sawit.

Untuk saat ini, JANGAN langsung membangun seluruh aplikasi dan JANGAN mengimplementasikan fitur.

Kerjakan HANYA:

FASE 0 — AUDIT DAN PERSIAPAN

Ikuti secara khusus:
- TASK 0.1 — Audit Project dan Environment;
- TASK 0.2 — Siapkan Branch, Backup, dan Strategi Perubahan;
- TASK 0.3 — Verifikasi Dependency Resmi dan Rencana Versi;
- TASK 0.4 — Buat atau Sesuaikan Kiro Steering;
- TASK 0.5 — Siapkan SPEC 01 — Project Foundation.

Aturan wajib:

1. Baca seluruh master requirements sampai selesai sebelum mengambil keputusan arsitektur.
2. Audit seluruh workspace project yang sedang terbuka.
3. Tentukan apakah project kosong, hanya instalasi framework, atau sudah memiliki implementasi.
4. Baca file konfigurasi, dependency, routes, migrations, models, controllers, services, middleware, auth, views, dan tests yang relevan.
5. Jangan menghapus file.
6. Jangan menjalankan migrate:fresh, migrate:reset, atau command destructive.
7. Jangan mengubah database.
8. Jangan menginstal dependency sebelum hasil audit dan rencana versi jelas.
9. Jangan membuat fitur aplikasi.
10. Jangan membuat seluruh delapan Spec sekaligus.
11. Jangan melanjutkan ke Fase 1.

Setelah audit:

A. Buat laporan kondisi project yang faktual.
B. Catat teknologi dan versi yang benar-benar terdeteksi.
C. Identifikasi fitur existing yang harus dipertahankan.
D. Identifikasi konflik dengan master requirements.
E. Identifikasi risiko teknis utama.
F. Buat atau sesuaikan Steering secara ringkas di .kiro/steering/ untuk product, tech, structure, business rules, critical safety rules, dan workflow.
G. Jangan menyalin seluruh master requirements ke Steering.
H. Siapkan SPEC 01 — Project Foundation dengan scope terbatas pada Fase 1.
I. Pastikan Spec memiliki requirements, design, dan tasks yang dapat direview.
J. Jangan mengimplementasikan task Spec apa pun terlebih dahulu.

Berhenti setelah:
1. audit selesai;
2. Steering selesai atau telah disesuaikan;
3. SPEC 01 siap direview;
4. ringkasan file yang dibuat/diubah telah diberikan.

Jangan lanjut ke coding sampai saya memberikan instruksi berikutnya.
```

Setelah Kiro menyelesaikan tahap tersebut, review hasil audit, Steering, dan SPEC 01 sebelum memberi perintah implementasi pertama.

