# Workflow Kiro — Penginapan Kelapa Sawit

## Model Kerja

```text
MASTER REQUIREMENTS (sumber kebenaran bisnis)
        ↓
PROJECT AUDIT (kondisi nyata project)
        ↓
KIRO STEERING (persistent context ringkas)
        ↓
FEATURE SPEC (scope terbatas)
        ↓
requirements.md → design.md → tasks.md
        ↓
IMPLEMENTASI SATU TASK
        ↓
TEST + REVIEW + UPDATE PROGRESS
        ↓
TASK BERIKUTNYA (hanya setelah verifikasi)
```

## Aturan Umum

1. Baca master requirements sebagai sumber kebutuhan utama.
2. Baca source code terkait sebelum mengubah apa pun.
3. Identifikasi Spec aktif dan task yang sedang dikerjakan.
4. Implementasikan SATU task kecil pada satu waktu.
5. Jalankan test dan checklist sebelum lanjut ke task berikutnya.
6. Jangan otomatis melanjutkan ke Spec berikutnya.
7. Jika kebutuhan berubah, perbarui artifact Spec terlebih dahulu.

## Sebelum Coding

1. Baca struktur project.
2. Baca file terkait task.
3. Cari implementasi serupa yang sudah ada.
4. Pahami naming convention project.
5. Cek test existing yang mungkin terpengaruh.
6. Buat rencana perubahan.

## Saat Coding

- Konsisten dengan convention project.
- Tidak duplikasi logic — reuse service/helper.
- Transaction untuk operasi kritis.
- Error handling yang bermakna.
- Form Request untuk validasi.
- Authorization untuk setiap endpoint.

## Setelah Coding

1. Jalankan test target.
2. Jalankan regression relevan.
3. Cek migration status.
4. Cek route list.
5. Frontend build.
6. Ringkas file yang berubah.
7. Catat risiko/remaining issue.

## Pembagian Spec

| Spec | Scope |
|---|---|
| SPEC 01 | Project Foundation (audit, env, auth, enum, layout) |
| SPEC 02 | Room Management & Public Website |
| SPEC 03 | Availability & Guest Booking Engine |
| SPEC 04 | Midtrans Payment |
| SPEC 05 | Admin Reservation & Member |
| SPEC 06 | Loyalty & Promotion |
| SPEC 07 | Property Operations |
| SPEC 08 | Reports, Security & Release |

Buat Spec hanya yang segera akan dikerjakan. Jangan membuat semua sekaligus.

## Larangan Workflow

- Jangan membuat satu Spec raksasa untuk seluruh website.
- Jangan mengerjakan task yang belum masuk scope Spec aktif.
- Jangan mengubah aturan bisnis diam-diam agar kode lebih mudah.
- Jangan menggunakan paralel work untuk area kritis yang sama.
- Jangan menganggap Steering sebagai pengganti membaca source code.
