# SPEC 02 — Room Management & Public Website: Design

> **Referensi:** requirements.md SPEC 02, Master Requirements Fase 2–3

---

## 1. Database Schema

### 1.1 `room_types`

```sql
CREATE TABLE room_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    short_description VARCHAR(255) NULL,
    description TEXT NULL,
    capacity SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    bed_count SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    bed_type VARCHAR(100) NULL,
    base_price BIGINT UNSIGNED NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    INDEX (is_active, sort_order)
) ENGINE=InnoDB;
```

### 1.2 `rooms`

```sql
CREATE TABLE rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_type_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(120) NOT NULL UNIQUE,
    floor VARCHAR(50) NULL,
    notes TEXT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    INDEX (room_type_id, is_active, status),
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
```

### 1.3 `facilities`

```sql
CREATE TABLE facilities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    icon VARCHAR(100) NULL,
    description VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    INDEX (is_active, sort_order)
) ENGINE=InnoDB;
```

### 1.4 `room_type_facility`

```sql
CREATE TABLE room_type_facility (
    room_type_id BIGINT UNSIGNED NOT NULL,
    facility_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (room_type_id, facility_id),
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
```

### 1.5 `room_images`

```sql
CREATE TABLE room_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_type_id BIGINT UNSIGNED NOT NULL,
    path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) NULL,
    is_cover BOOLEAN NOT NULL DEFAULT FALSE,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    INDEX (room_type_id, sort_order),
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### 1.6 `settings`

```sql
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `group` VARCHAR(100) NOT NULL DEFAULT 'general',
    `key` VARCHAR(150) NOT NULL,
    value LONGTEXT NULL,
    type VARCHAR(30) NOT NULL DEFAULT 'string',
    is_public BOOLEAN NOT NULL DEFAULT FALSE,
    updated_by_admin_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    UNIQUE KEY settings_group_key_unique (`group`, `key`),
    INDEX (is_public),
    FOREIGN KEY (updated_by_admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

### 1.7 `policy_versions`

```sql
CREATE TABLE policy_versions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    policy_key VARCHAR(100) NOT NULL DEFAULT 'guest_policy',
    version VARCHAR(50) NOT NULL,
    title VARCHAR(191) NOT NULL,
    content LONGTEXT NOT NULL,
    is_current BOOLEAN NOT NULL DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_by_admin_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    UNIQUE KEY policy_key_version_unique (policy_key, version),
    INDEX (policy_key, is_current),
    FOREIGN KEY (created_by_admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

### 1.8 `galleries`

```sql
CREATE TABLE galleries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(191) NULL,
    path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_by_admin_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    INDEX (is_active, sort_order),
    FOREIGN KEY (created_by_admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;
```

### 1.9 Migration Order

1. create_room_types_table
2. create_rooms_table
3. create_facilities_table
4. create_room_type_facility_table
5. create_room_images_table
6. create_settings_table
7. create_policy_versions_table
8. create_galleries_table

---

## 2. Models & Relations

### RoomType
- `hasMany(Room)`
- `belongsToMany(Facility)` via `room_type_facility`
- `hasMany(RoomImage)`
- Scopes: `active()`, `ordered()`
- Accessor: `cover_image` (first is_cover or first by sort)

### Room
- `belongsTo(RoomType)`
- Cast: `status` → `RoomStatus` enum
- Scopes: `active()`, `sellable()` (active + room_type active)

### Facility
- `belongsToMany(RoomType)`
- Scopes: `active()`, `ordered()`

### RoomImage
- `belongsTo(RoomType)`

### Setting
- Static helper: `Setting::get($group, $key, $default)`
- Cache layer with invalidation on save

### PolicyVersion
- Scope: `current($policyKey)`
- Business rule: only one current per key

### Gallery
- Scopes: `active()`, `ordered()`

---

## 3. Service / Helper

### SettingService
- `get(string $group, string $key, mixed $default = null): mixed`
- `getPublic(string $group, string $key): mixed`
- `set(string $group, string $key, mixed $value, Admin $admin): void`
- Uses cache (`settings.{group}.{key}`) with tag/flush on write.

### ImageUploadService
- `upload(UploadedFile $file, string $directory): string` → returns stored path
- Validates MIME (jpeg, png, webp), max 2MB.
- Generates random filename.
- `delete(string $path): void` → removes from storage.

---

## 4. Admin Controllers

### Admin\RoomTypeController
- `index()` — list all room types with counts
- `create()` / `store(StoreRoomTypeRequest)` — create with facility sync + images
- `edit($id)` / `update(UpdateRoomTypeRequest, $id)` — edit, re-sync facilities
- `toggleActive($id)` — activate/deactivate

### Admin\RoomController
- `index()` — list rooms grouped by type
- `create()` / `store(StoreRoomRequest)`
- `edit($id)` / `update(UpdateRoomRequest, $id)`
- `toggleActive($id)`

### Admin\FacilityController
- `index()` / `create()` / `store()` / `edit()` / `update()` / `destroy()`
- Destroy blocked if facility in use.

### Admin\RoomImageController
- `store(StoreRoomImageRequest)` — upload multiple
- `setCover($id)` — set as cover, unset previous
- `updateOrder(Request)` — batch sort_order
- `destroy($id)` — delete image + file

### Admin\SettingsController
- `edit($group)` — show form for group
- `update(UpdateSettingsRequest, $group)` — save group settings

### Admin\PolicyVersionController
- `index()` / `create()` / `store()` / `show()` / `publish($id)`

### Admin\GalleryController
- `index()` / `store()` / `updateOrder()` / `toggleActive($id)` / `destroy($id)`

---

## 5. Public Controllers

### Public\HomeController
- Query: active room types with cover images, cheapest price for "mulai dari"
- Query: settings for contact/about/WhatsApp

### Public\RoomController
- `index()` — active room types, ordered
- `show($slug)` — room type by slug, with images + facilities; 404 if inactive

### Public\PageController
- `about()` — from settings
- `location()` — from settings
- `policy()` — current policy version

### Public\GalleryController (optional)
- `index()` — active galleries

---

## 6. Route Design

```php
// Admin routes (auth:admin)
Route::prefix('admin')->middleware('auth:admin')->name('admin.')->group(function () {
    // Room Types
    Route::resource('room-types', RoomTypeController::class)->except('show', 'destroy');
    Route::patch('room-types/{roomType}/toggle', [RoomTypeController::class, 'toggleActive'])->name('room-types.toggle');

    // Room Images (nested or standalone)
    Route::post('room-types/{roomType}/images', [RoomImageController::class, 'store'])->name('room-images.store');
    Route::patch('room-images/{image}/cover', [RoomImageController::class, 'setCover'])->name('room-images.cover');
    Route::patch('room-images/order', [RoomImageController::class, 'updateOrder'])->name('room-images.order');
    Route::delete('room-images/{image}', [RoomImageController::class, 'destroy'])->name('room-images.destroy');

    // Rooms
    Route::resource('rooms', RoomController::class)->except('show', 'destroy');
    Route::patch('rooms/{room}/toggle', [RoomController::class, 'toggleActive'])->name('rooms.toggle');

    // Facilities
    Route::resource('facilities', FacilityController::class)->except('show');

    // Settings
    Route::get('settings/{group}', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings/{group}', [SettingsController::class, 'update'])->name('settings.update');

    // Policy
    Route::resource('policies', PolicyVersionController::class)->except('edit', 'update', 'destroy');
    Route::patch('policies/{policy}/publish', [PolicyVersionController::class, 'publish'])->name('policies.publish');

    // Gallery
    Route::resource('galleries', GalleryController::class)->except('show', 'edit', 'update');
    Route::patch('galleries/{gallery}/toggle', [GalleryController::class, 'toggleActive'])->name('galleries.toggle');
    Route::patch('galleries/order', [GalleryController::class, 'updateOrder'])->name('galleries.order');
});

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kamar', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/kamar/{slug}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('/tentang', [PageController::class, 'about'])->name('about');
Route::get('/lokasi', [PageController::class, 'location'])->name('location');
Route::get('/kebijakan', [PageController::class, 'policy'])->name('policy');
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery');
```

---

## 7. Upload & Storage

- Disk: `public` (local)
- Path: `room-images/{random}.{ext}`, `galleries/{random}.{ext}`
- `php artisan storage:link` required for public access.
- Max size: 2MB per image.
- Allowed MIME: image/jpeg, image/png, image/webp.
- Random filename: `Str::random(40).'.'.extension`

---

## 8. SEO Strategy

- Each public page sets `$title`, `$description` via view data or section.
- Layout includes `<meta>` for title, description, OG.
- Images have alt text.
- Room type detail uses slug-based canonical URL.
- Structured heading: h1 per page, h2/h3 semantic.

---

## 9. Test Strategy

| Test | Type | Coverage |
|---|---|---|
| Room type CRUD | Feature | Create, edit, toggle, validation |
| Room CRUD | Feature | Create, edit, toggle, delete protection |
| Facility CRUD | Feature | Create, edit, delete, restrict if in use |
| Image upload | Feature | Valid upload, invalid MIME rejected, cover set |
| Settings | Feature | Edit, read public, cache |
| Policy version | Feature | Create, publish, current switch |
| Public home | Feature | Renders, shows active types, hides inactive |
| Public room list/detail | Feature | Shows active, 404 inactive slug |
| Seeder | Feature | Idempotent, creates Twin data |

---

## 10. Decisions

| Decision | Rationale |
|---|---|
| Settings table, bukan .env | Data bisnis yang sering berubah (alamat, WA) dikelola admin tanpa deploy |
| Policy versioning | Booking mengacu versi; versi lama immutable |
| Cover via boolean + service | Sederhana, satu cover per room type dijamin service |
| Gallery terpisah dari room images | Gallery umum penginapan, bukan spesifik per tipe |
| Seeder Twin inactive | Harga/kapasitas belum dikonfirmasi, admin harus activate setelah isi data |
