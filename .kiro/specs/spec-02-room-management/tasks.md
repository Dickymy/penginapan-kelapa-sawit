# SPEC 02 — Room Management & Public Website: Tasks

> **Status:** Ready for implementation  
> **Dependency:** SPEC 01 complete

---

## Task 1: Migrations — Room Inventory + Settings + Policy + Gallery

- [ ] Create migration `create_room_types_table`
- [ ] Create migration `create_rooms_table` (FK to room_types RESTRICT)
- [ ] Create migration `create_facilities_table`
- [ ] Create migration `create_room_type_facility_table` (composite PK, FKs)
- [ ] Create migration `create_room_images_table` (FK CASCADE)
- [ ] Create migration `create_settings_table`
- [ ] Create migration `create_policy_versions_table`
- [ ] Create migration `create_galleries_table`
- [ ] Run `php artisan migrate`
- [ ] Verify all tables created with correct indexes and FKs
- [ ] Commit: "feat(spec02): add room inventory, settings, policy, gallery migrations"

**Depends on:** SPEC 01 complete  
**REQ traceability:** REQ-1.1, REQ-4.1, REQ-4.2, REQ-6.1  
**Verification:** Migrations run cleanly, foreign keys active

---

## Task 2: Models & Relations

- [ ] Create `app/Models/RoomType.php` — fillable, casts, relations (rooms, facilities, images), scopes (active, ordered), cover accessor
- [ ] Create `app/Models/Room.php` — fillable, casts (status → RoomStatus), relations (roomType), scopes (active, sellable)
- [ ] Create `app/Models/Facility.php` — fillable, relations (roomTypes), scopes (active, ordered)
- [ ] Create `app/Models/RoomImage.php` — fillable, casts, relation (roomType)
- [ ] Create `app/Models/Setting.php` — fillable, casts, static helper get/set
- [ ] Create `app/Models/PolicyVersion.php` — fillable, casts, scope (current)
- [ ] Create `app/Models/Gallery.php` — fillable, scopes (active, ordered)
- [ ] Create model factories for RoomType, Room, Facility
- [ ] Write unit test: RoomType-Room relation, RoomType-Facility pivot, Room sellable scope
- [ ] Commit: "feat(spec02): add room inventory models with relations and scopes"

**Depends on:** Task 1  
**REQ traceability:** REQ-1.1, REQ-1.2  
**Verification:** Model tests pass, relations work

---

## Task 3: Setting Service & Image Upload Service

- [ ] Create `app/Services/SettingService.php` — get, getPublic, set (with cache)
- [ ] Create `app/Services/ImageUploadService.php` — upload (validate, random name, store), delete
- [ ] Register SettingService in container if needed
- [ ] Run `php artisan storage:link`
- [ ] Write unit test: SettingService get/set with cache, ImageUploadService validation
- [ ] Commit: "feat(spec02): add SettingService and ImageUploadService"

**Depends on:** Task 2  
**REQ traceability:** REQ-4.1, REQ-2.3  
**Verification:** Tests pass, storage link works

---

## Task 4: Admin Room Type CRUD (Controller + Views)

- [ ] Create `app/Http/Controllers/Admin/RoomTypeController.php` — index, create, store, edit, update, toggleActive
- [ ] Create `app/Http/Requests/Admin/StoreRoomTypeRequest.php`
- [ ] Create `app/Http/Requests/Admin/UpdateRoomTypeRequest.php`
- [ ] Create admin views: `admin/room-types/index.blade.php`, `create.blade.php`, `edit.blade.php`
- [ ] Integrate facility multi-select in create/edit form
- [ ] Integrate image upload in create/edit form
- [ ] Add routes for room-types resource + toggle
- [ ] Update admin sidebar: enable "Kamar" link
- [ ] Write feature test: create room type, edit, toggle, validation errors
- [ ] Commit: "feat(spec02): admin room type CRUD with facilities and images"

**Depends on:** Task 3  
**REQ traceability:** REQ-2.1, REQ-2.2, REQ-2.3  
**Verification:** Feature tests pass, admin can manage room types

---

## Task 5: Admin Room Image Management

- [ ] Create `app/Http/Controllers/Admin/RoomImageController.php` — store, setCover, updateOrder, destroy
- [ ] Create `app/Http/Requests/Admin/StoreRoomImageRequest.php` (validate images.* mime, max size)
- [ ] Add routes for room images
- [ ] Implement: upload stores with random filename, setCover ensures only one cover, destroy removes file after DB commit
- [ ] Write feature test: upload valid image, reject invalid MIME, set cover, delete image
- [ ] Commit: "feat(spec02): admin room image upload and management"

**Depends on:** Task 4  
**REQ traceability:** REQ-2.3  
**Verification:** Upload tests pass, files stored correctly

---

## Task 6: Admin Room CRUD + Seeder

- [ ] Create `app/Http/Controllers/Admin/RoomController.php` — index, create, store, edit, update, toggleActive
- [ ] Create `app/Http/Requests/Admin/StoreRoomRequest.php`
- [ ] Create `app/Http/Requests/Admin/UpdateRoomRequest.php`
- [ ] Create admin views: `admin/rooms/index.blade.php`, `create.blade.php`, `edit.blade.php`
- [ ] Add routes for rooms resource + toggle
- [ ] Create `database/seeders/RoomSeeder.php` — Twin type + Twin 01 + Twin 02 (idempotent, inactive placeholder)
- [ ] Register RoomSeeder in DatabaseSeeder
- [ ] Run seeder, verify data
- [ ] Write feature test: create room, edit, toggle, delete restriction (application level)
- [ ] Commit: "feat(spec02): admin room CRUD with Twin seeder"

**Depends on:** Task 4  
**REQ traceability:** REQ-3.1, REQ-3.2  
**Verification:** Feature tests pass, Twin 01/02 seeded

---

## Task 7: Admin Facility CRUD

- [ ] Create `app/Http/Controllers/Admin/FacilityController.php` — index, create, store, edit, update, destroy
- [ ] Create `app/Http/Requests/Admin/StoreFacilityRequest.php`
- [ ] Create `app/Http/Requests/Admin/UpdateFacilityRequest.php`
- [ ] Create admin views: `admin/facilities/index.blade.php`, `create.blade.php`, `edit.blade.php`
- [ ] Add routes for facilities resource
- [ ] Destroy checks if facility in use (pivot) — block with error message
- [ ] Write feature test: CRUD facility, delete restriction when in use
- [ ] Commit: "feat(spec02): admin facility CRUD with delete protection"

**Depends on:** Task 2  
**REQ traceability:** REQ-2.2  
**Verification:** Feature tests pass

---

## Task 8: Admin Settings & Policy Management

- [ ] Create `app/Http/Controllers/Admin/SettingsController.php` — edit (by group), update
- [ ] Create `app/Http/Requests/Admin/UpdateSettingsRequest.php`
- [ ] Create admin views: `admin/settings/edit.blade.php` (dynamic per group)
- [ ] Create `app/Http/Controllers/Admin/PolicyVersionController.php` — index, create, store, show, publish
- [ ] Create `app/Http/Requests/Admin/StorePolicyRequest.php`
- [ ] Create admin views: `admin/policies/index.blade.php`, `create.blade.php`, `show.blade.php`
- [ ] Add routes for settings and policies
- [ ] Publish logic: transaction, unset previous current, set new current
- [ ] Seed initial settings groups (general, contact, booking, whatsapp, seo) with placeholder values
- [ ] Write feature test: edit settings, create policy, publish policy
- [ ] Commit: "feat(spec02): admin settings and policy version management"

**Depends on:** Task 3  
**REQ traceability:** REQ-4.1, REQ-4.2  
**Verification:** Feature tests pass, settings cached, policy current works

---

## Task 9: Admin Gallery

- [ ] Create `app/Http/Controllers/Admin/GalleryController.php` — index, store, toggleActive, updateOrder, destroy
- [ ] Create `app/Http/Requests/Admin/StoreGalleryRequest.php`
- [ ] Create admin views: `admin/galleries/index.blade.php`
- [ ] Add routes for galleries
- [ ] Write feature test: upload gallery, toggle, reorder, delete
- [ ] Commit: "feat(spec02): admin gallery management"

**Depends on:** Task 3  
**REQ traceability:** REQ-6.1  
**Verification:** Feature tests pass

---

## Task 10: Public Website Pages

- [ ] Update `app/Http/Controllers/Public/HomeController.php` — query active room types, cheapest price, settings
- [ ] Create `app/Http/Controllers/Public/RoomController.php` — index (active types), show (by slug, 404 if inactive)
- [ ] Create `app/Http/Controllers/Public/PageController.php` — about, location, policy
- [ ] Update public routes: /kamar, /kamar/{slug}, /tentang, /lokasi, /kebijakan
- [ ] Update `resources/views/public/home.blade.php` — real data from DB
- [ ] Create `resources/views/public/rooms/index.blade.php` — room type cards
- [ ] Create `resources/views/public/rooms/show.blade.php` — detail with gallery, facilities
- [ ] Create `resources/views/public/about.blade.php`
- [ ] Create `resources/views/public/location.blade.php`
- [ ] Create `resources/views/public/policy.blade.php`
- [ ] Update public navigation links to real routes
- [ ] Add meta/SEO component in layout (title, description, OG)
- [ ] Write feature test: home renders, room list shows active only, room detail 404 for inactive, about/location/policy pages render
- [ ] Commit: "feat(spec02): public website pages with real data"

**Depends on:** Task 4, Task 6, Task 8  
**REQ traceability:** REQ-5.1, REQ-5.2, REQ-5.3, REQ-5.4  
**Verification:** Feature tests pass, pages render with DB data

---

## Task 11: Final Verification & Cleanup

- [ ] Run full test suite `php artisan test`
- [ ] Run `npm run build`
- [ ] Run `php artisan route:list` — verify no conflicts
- [ ] Run `php artisan migrate:status` — all ran
- [ ] Verify admin sidebar links for Kamar, Galeri, Kebijakan, Pengaturan are active
- [ ] Verify public pages render correctly (manual browser check)
- [ ] Clean up any unused files
- [ ] Final commit: "chore(spec02): final verification and cleanup"

**Depends on:** All previous tasks  
**REQ traceability:** All REQs  
**Verification:** All tests pass, build passes, routes clean

---

## Summary

| Task | Scope | Dependencies |
|---|---|---|
| 1 | Migrations | SPEC 01 |
| 2 | Models + Relations | Task 1 |
| 3 | Services (Setting, Image) | Task 2 |
| 4 | Admin Room Type CRUD | Task 3 |
| 5 | Admin Room Images | Task 4 |
| 6 | Admin Room CRUD + Seeder | Task 4 |
| 7 | Admin Facility CRUD | Task 2 |
| 8 | Admin Settings + Policy | Task 3 |
| 9 | Admin Gallery | Task 3 |
| 10 | Public Website Pages | Task 4, 6, 8 |
| 11 | Final Verification | All |

**Parallelizable:** Task 5, 6, 7 dapat paralel setelah Task 4. Task 8, 9 dapat paralel setelah Task 3.  
**Sequential:** Task 10 butuh Task 4+6+8. Task 11 butuh semua.
