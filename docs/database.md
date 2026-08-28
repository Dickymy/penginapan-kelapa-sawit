# Database — Penginapan Kelapa Sawit

> **Sumber:** `database/migrations/` — diverifikasi dari file migration aktual.
> **Engine:** MySQL 8.0.30, InnoDB, foreign key constraints aktif.
> **Jumlah migration:** 37 file.

---

## Konvensi

- Semua amount/harga: `BIGINT UNSIGNED` (integer Rupiah, tanpa desimal)
- Tanggal menginap: `DATE` (bukan datetime)
- Interval: half-open `[check_in, check_out)` — check_out tidak termasuk
- Timestamps: `created_at`, `updated_at` standar Laravel
- Soft delete: tabel `reviews` menggunakan `deleted_at`

---

## Tabel Utama

### users

Migration: `0001_01_01_000000_create_users_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| name | varchar | Nama lengkap |
| email | varchar unique | Lowercase, terverifikasi |
| email_verified_at | timestamp nullable | — |
| whatsapp | varchar nullable | Nomor ternormalisasi (format 628xxx) |
| password | varchar nullable | Hash, nullable untuk OAuth-only user |
| avatar_path | varchar nullable | Path lokal |
| avatar_url | varchar nullable | URL dari OAuth provider |
| is_active | boolean default true | — |
| last_login_at | timestamp nullable | — |
| loyalty_balance_cache | integer default 0 | Cache saldo — sumber kebenaran tetap ledger |
| remember_token | varchar nullable | — |
| timestamps | — | — |

### admins

Migration: `0001_01_01_000001_create_admins_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| name | varchar | — |
| email | varchar unique | — |
| password | varchar | Hash |
| role | varchar | Peran admin |
| is_active | boolean default true | — |
| last_login_at | timestamp nullable | — |
| password_changed_at | timestamp nullable | — |
| remember_token | varchar nullable | — |
| timestamps | — | — |

### social_accounts

Migration: `0001_01_01_000002_create_social_accounts_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| user_id | FK users | — |
| provider | varchar | google, dsb |
| provider_user_id | varchar | — |
| provider_email | varchar nullable | — |
| provider_email_verified | boolean default false | — |
| timestamps | — | — |

---

## Tabel Room

### room_types

Migration: `2026_07_07_024221_create_room_types_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| name | varchar | — |
| slug | varchar unique | — |
| short_description | varchar nullable | — |
| description | text nullable | — |
| capacity | tinyint unsigned | Max tamu |
| bed_count | tinyint unsigned | — |
| bed_type | varchar | — |
| base_price | bigint unsigned | Harga dasar per malam (IDR) |
| is_active | boolean default true | — |
| sort_order | smallint unsigned default 0 | — |
| timestamps | — | — |

### rooms

Migration: `2026_07_07_024252_create_rooms_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| room_type_id | FK room_types | — |
| code | varchar | Kode internal kamar |
| name | varchar | Nama display (Twin 01, dst) |
| floor | varchar nullable | — |
| notes | text nullable | Catatan internal |
| status | enum (active/inactive/maintenance) | `RoomStatus` enum |
| is_active | boolean default true | — |
| sort_order | smallint unsigned default 0 | — |
| timestamps | — | — |

### room_type_facility (pivot)

Migration: `2026_07_07_024253_create_room_type_facility_table.php`

| Kolom | Tipe |
|---|---|
| room_type_id | FK room_types |
| facility_id | FK facilities |

Unique constraint pada `(room_type_id, facility_id)`.

### room_images

Migration: `2026_07_07_024254_create_room_images_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| room_type_id | FK room_types | — |
| path | varchar | Path original |
| thumb_path | varchar nullable | — |
| medium_path | varchar nullable | — |
| large_path | varchar nullable | — |
| alt_text | varchar nullable | — |
| is_cover | boolean default false | — |
| sort_order | smallint unsigned default 0 | — |
| timestamps | — | — |

### facilities

Migration: `2026_07_07_024252_create_facilities_table.php`

| Kolom | Tipe |
|---|---|
| id, name, slug unique, icon, description, is_active, sort_order, timestamps | — |

---

## Tabel Booking

### bookings

Migration: `2026_07_07_032607_create_bookings_table.php` + `2026_08_19_125640_add_email_tracking_to_bookings_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| booking_code | varchar unique | Format BKG-YYYYMM-XXXX |
| idempotency_key | varchar unique nullable | SHA-256 fingerprint |
| guest_access_token_hash | varchar nullable | Token akses guest (SHA-256) |
| user_id | FK users nullable | Null untuk guest booking |
| room_id | FK rooms | — |
| created_by_admin_id | FK admins nullable | Untuk booking manual |
| source | varchar | `BookingSource` enum value |
| status | varchar | `BookingStatus` enum value |
| payment_status | varchar | `PaymentStatus` enum value |
| check_in | date | — |
| check_out | date | — |
| nights | smallint unsigned | — |
| guest_count | tinyint unsigned | — |
| guest_name | varchar | Snapshot nama tamu |
| guest_email | varchar nullable | Snapshot email tamu |
| guest_whatsapp | varchar | Snapshot WA ternormalisasi |
| arrival_estimate | varchar nullable | Jam estimasi kedatangan |
| special_request | text nullable | — |
| room_type_name_snapshot | varchar | Snapshot nama tipe kamar |
| room_name_snapshot | varchar | Snapshot nama kamar fisik |
| price_per_night_snapshot | bigint unsigned | Base price saat booking dibuat |
| subtotal | bigint unsigned | Harga kamar sebelum diskon |
| promotion_discount | bigint unsigned default 0 | — |
| points_redeemed | int default 0 | Jumlah poin digunakan |
| points_discount | bigint unsigned default 0 | Nilai poin dalam Rupiah |
| total_amount | bigint unsigned | Final yang harus dibayar |
| currency | char(3) default 'IDR' | — |
| eligible_loyalty_amount | bigint unsigned | Amount untuk kalkulasi poin |
| payment_expires_at | timestamp nullable | Batas waktu pembayaran |
| policy_version_id | FK policy_versions nullable | Kebijakan yang disetujui saat booking |
| policy_accepted_at | timestamp nullable | — |
| needs_attention | boolean default false | Payment late setelah expired |
| internal_notes | text nullable | Catatan internal admin |
| claimed_at | timestamp nullable | Waktu member claim booking |
| checked_in_at | timestamp nullable | — |
| checked_out_at | timestamp nullable | — |
| completed_at | timestamp nullable | — |
| cancelled_at | timestamp nullable | — |
| confirmation_email_sent_at | timestamp nullable | (dari migration alter) |
| payment_email_sent_at | timestamp nullable | (dari migration alter) |
| reminder_email_sent_at | timestamp nullable | (dari migration alter) |
| checkout_email_sent_at | timestamp nullable | (dari migration alter) |
| cancellation_email_sent_at | timestamp nullable | (dari migration alter) |
| timestamps | — | — |

### booking_status_histories

Migration: `2026_07_07_032608_create_booking_status_histories_table.php`

| Kolom | Tipe |
|---|---|
| id | bigint unsigned PK |
| booking_id | FK bookings |
| from_status | varchar nullable |
| to_status | varchar |
| reason | text nullable |
| actor_type | varchar (user/admin/system) |
| actor_id | bigint nullable |
| timestamps | — |

### booking_night_prices

Migration: `2026_08_19_161653_create_booking_night_prices_table.php`

Snapshot harga per malam saat booking dibuat — tidak berubah meski rate override diedit.

| Kolom | Tipe |
|---|---|
| id | bigint unsigned PK |
| booking_id | FK bookings |
| date | date |
| price | bigint unsigned |
| label | varchar nullable (mis. "Weekend") |
| timestamps | — |

### booking_change_requests

Migration: `2026_08_19_174909_create_booking_change_requests_table.php`

| Kolom | Tipe |
|---|---|
| id, booking_id (FK), user_id (FK), type, original_data (json), requested_data (json), price_difference (int), status, admin_notes, processed_by_admin_id (FK nullable), processed_at, timestamps | — |

### document_sequences

Migration: `2026_07_07_032609_create_document_sequences_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| type | varchar | booking_code, invoice_number |
| year_month | char(6) | YYYYMM |
| last_sequence | int unsigned default 0 | — |
| Unique | (type, year_month) | — |

---

## Tabel Payment

### payments

Migration: `2026_07_07_040456_create_payments_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| booking_id | FK bookings | — |
| provider | varchar | midtrans |
| provider_order_id | varchar unique nullable | Order ID ke Midtrans |
| transaction_id | varchar nullable | Transaction ID dari Midtrans |
| attempt_no | tinyint unsigned default 1 | — |
| snap_token | varchar nullable | — |
| snap_token_expires_at | timestamp nullable | — |
| payment_type | varchar nullable | Metode pembayaran |
| gross_amount | bigint unsigned | Harus cocok dengan booking.total_amount |
| status | varchar | `PaymentStatus` enum value |
| provider_transaction_status | varchar nullable | Status raw dari Midtrans |
| fraud_status | varchar nullable | — |
| paid_at | timestamp nullable | — |
| expired_at | timestamp nullable | — |
| raw_response | json nullable | Response raw Midtrans |
| timestamps | — | — |

### payment_webhook_events

Migration: `2026_07_07_040457_create_payment_webhook_events_table.php`

Log semua webhook masuk untuk audit dan debugging.

| Kolom | Tipe |
|---|---|
| id, order_id, transaction_status, fraud_status, raw_payload (json), processed_at nullable, timestamps | — |

---

## Tabel Loyalty

### loyalty_transactions

Migration: `2026_07_07_043746_create_loyalty_transactions_table.php`

Ledger — sumber kebenaran saldo poin. Tidak ada hard-delete.

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| user_id | FK users | — |
| booking_id | FK bookings nullable | — |
| type | varchar | `LoyaltyTransactionType` enum value |
| points | integer | Positif untuk earn/adjustment, negatif untuk redeem/expire |
| balance_after | integer | Saldo setelah transaksi ini |
| remaining_points | integer default 0 | Sisa poin belum digunakan (untuk lot FIFO) |
| description | varchar | — |
| expires_at | timestamp nullable | Khusus lot earn/adjustment |
| source_transaction_id | FK loyalty_transactions nullable | Untuk reversal/expire |
| idempotency_key | varchar unique nullable | Cegah duplikat |
| created_by_admin_id | FK admins nullable | Untuk adjustment manual |
| metadata | json nullable | — |
| timestamps | — | — |

### loyalty_point_allocations

Migration: `2026_07_07_043747_create_loyalty_point_allocations_table.php`

Penghubung FIFO antara transaksi debit dan kredit.

| Kolom | Tipe |
|---|---|
| id, debit_transaction_id (FK loyalty_transactions), credit_transaction_id (FK loyalty_transactions), points (integer), timestamps | — |

---

## Tabel Promotion

### promotions

Migration: `2026_07_07_043746_create_promotions_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint unsigned PK | — |
| code | varchar unique | Kode promo (case-insensitive) |
| name, description | varchar/text | — |
| type | varchar | `PromotionType` enum (percentage/fixed) |
| value | bigint unsigned | Nilai diskon (% atau nominal IDR) |
| starts_at, ends_at | timestamp nullable | — |
| minimum_booking_amount | bigint unsigned default 0 | — |
| maximum_discount | bigint unsigned nullable | Cap diskon (untuk tipe percentage) |
| usage_quota | integer nullable | Total penggunaan maksimum |
| max_usage_per_user | integer nullable | Batas per user |
| is_active | boolean default true | — |
| timestamps | — | — |

### promotion_usages

Migration: `2026_07_07_043747_create_promotion_usages_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id, promotion_id (FK), booking_id (FK), user_id (FK nullable), status (`PromotionUsageStatus`), discount_amount, reserved_at, consumed_at, released_at, timestamps | — | Lifecycle: reserved → consumed atau released |

---

## Tabel Operasional

### refunds

Migration: `2026_07_07_045036_create_refunds_table.php`

| Kolom | Tipe |
|---|---|
| id, booking_id (FK), payment_id (FK), requested_by_admin_id (FK), processed_by_admin_id (FK nullable), amount (bigint unsigned), reason (text), notes (text nullable), status (`RefundStatus`), requested_at, processed_at nullable, provider_refund_id nullable, provider_response (json nullable), timestamps | — |

### expenses

Migration: `2026_07_07_050000_create_expenses_table.php`

| Kolom | Tipe |
|---|---|
| id, expense_date (date), category (varchar), amount (bigint unsigned), description (text), receipt_path (varchar nullable), created_by_admin_id (FK admins), timestamps | — |

### room_blocks

Migration: `2026_07_07_035308_create_room_blocks_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id, room_id (FK rooms), start_date (date), end_date (date), reason_type (varchar), notes (text nullable), created_by_admin_id (FK admins), timestamps | — | Blokir kamar manual oleh admin |

### rate_overrides

Migration: `2026_08_19_161646_create_rate_overrides_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id, room_type_id (FK), date (date), price (bigint unsigned), label (varchar nullable), timestamps | — | Unique: (room_type_id, date) |

---

## Tabel Konten

### reviews

Migration: `2026_08_19_152406_create_reviews_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id, user_id (FK), booking_id (FK, unique), rating (tinyint unsigned 1-5), title (varchar nullable), comment (text), is_published (boolean default false), admin_reply (text nullable), replied_at (timestamp nullable), timestamps, deleted_at | — | SoftDeletes. Unique booking_id = 1 review per booking |

### faqs

Migration: `2026_08_19_154824_create_faqs_table.php`

| Kolom | Tipe |
|---|---|
| id, question (varchar), answer (text), category (varchar nullable), sort_order (int unsigned default 0), is_active (boolean default true), timestamps | — |

### contact_messages

Migration: `2026_08_19_165504_create_contact_messages_table.php`

| Kolom | Tipe |
|---|---|
| id, name, email, phone (nullable), subject, message (text), is_read (boolean default false), admin_notes (text nullable), replied_at (timestamp nullable), timestamps | — |

### nearby_places

Migration: `2026_08_19_171217_create_nearby_places_table.php`

| Kolom | Tipe |
|---|---|
| id, name, category, distance, description (nullable), image (nullable), map_link (nullable), sort_order, is_active, timestamps | — |

### galleries

Migration: `2026_07_07_024256_create_galleries_table.php` + `2026_07_11_000001_add_image_variants_to_galleries_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id, title (nullable), path, thumb_path (nullable), medium_path (nullable), large_path (nullable), alt_text (nullable), is_active, sort_order, created_by_admin_id (FK), timestamps | — | Varian gambar ditambahkan di migration alter |

---

## Tabel System

### settings

Migration: `2026_07_07_024254_create_settings_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id, group, key, value (text nullable), type (varchar), is_public (boolean), updated_by_admin_id (FK nullable), timestamps | — | Unique: (group, key). Hanya config UI — bukan secret |

### policy_versions

Migration: `2026_07_07_024255_create_policy_versions_table.php`

| Kolom | Tipe |
|---|---|
| id, policy_key, version, title, content (longtext), is_current (boolean), published_at (timestamp nullable), created_by_admin_id (FK nullable), timestamps | — |

### audit_logs

Migration: `2026_07_07_045037_create_audit_logs_table.php`

| Kolom | Tipe |
|---|---|
| id, actor_type, actor_id (nullable), action, subject_type, subject_id (nullable), metadata (json nullable), ip_address, user_agent (nullable), timestamps | — |

### addons

Migration: `2026_08_19_184204_create_addons_table.php`

| Kolom | Tipe |
|---|---|
| id, name, description (nullable), price (bigint unsigned), is_active, sort_order, timestamps | — |

### booking_addons

Migration: `2026_08_19_184204_create_booking_addons_table.php`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id, booking_id (FK), addon_id (FK), quantity (smallint unsigned), unit_price (bigint unsigned), subtotal (bigint unsigned), timestamps | — | unit_price adalah snapshot saat booking dibuat |

### cache, jobs, password_reset_tokens

Tabel Laravel default untuk cache driver database, queue database, dan reset password.
