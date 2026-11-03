<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoomImageManagementTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_first_image_created_automatically_becomes_cover(): void
    {
        $roomType = RoomType::create([
            'name' => 'Deluxe Suite',
            'slug' => 'deluxe-suite',
            'capacity' => 2,
            'bed_count' => 1,
            'base_price' => 300000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $img1 = $roomType->images()->create([
            'path' => 'room-images/test1.webp',
            'medium_path' => 'room-images/test1_medium.webp',
            'sort_order' => 1,
        ]);

        $img2 = $roomType->images()->create([
            'path' => 'room-images/test2.webp',
            'medium_path' => 'room-images/test2_medium.webp',
            'sort_order' => 2,
        ]);

        $this->assertTrue($img1->fresh()->is_cover);
        $this->assertFalse($img2->fresh()->is_cover);
        $this->assertEquals($img1->id, $roomType->fresh()->cover_image->id);
    }

    public function test_deleting_cover_image_automatically_promotes_next_image(): void
    {
        $roomType = RoomType::create([
            'name' => 'Executive Room',
            'slug' => 'executive-room',
            'capacity' => 2,
            'bed_count' => 1,
            'base_price' => 400000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $img1 = $roomType->images()->create([
            'path' => 'room-images/cover.webp',
            'is_cover' => true,
            'sort_order' => 1,
        ]);

        $img2 = $roomType->images()->create([
            'path' => 'room-images/next.webp',
            'is_cover' => false,
            'sort_order' => 2,
        ]);

        // Delete the cover image via admin endpoint
        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.room-images.destroy', $img1));

        $response->assertRedirect();
        $this->assertDatabaseMissing('room_images', ['id' => $img1->id]);

        // img2 must now be the cover
        $this->assertTrue($img2->fresh()->is_cover);
        $this->assertEquals($img2->id, $roomType->fresh()->cover_image->id);
    }

    public function test_admin_can_set_specific_image_as_cover(): void
    {
        $roomType = RoomType::create([
            'name' => 'Standard Room',
            'slug' => 'standard-room',
            'capacity' => 2,
            'bed_count' => 1,
            'base_price' => 200000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $img1 = $roomType->images()->create([
            'path' => 'room-images/img1.webp',
            'is_cover' => true,
            'sort_order' => 1,
        ]);

        $img2 = $roomType->images()->create([
            'path' => 'room-images/img2.webp',
            'is_cover' => false,
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.room-images.cover', $img2));

        $response->assertRedirect();
        $this->assertFalse($img1->fresh()->is_cover);
        $this->assertTrue($img2->fresh()->is_cover);
        $this->assertEquals($img2->id, $roomType->fresh()->cover_image->id);
    }

    public function test_replacing_photos_via_admin_shows_new_photo_on_homepage(): void
    {
        $roomType = RoomType::create([
            'name' => 'King Bed Test',
            'slug' => 'king-bed-test',
            'capacity' => 2,
            'bed_count' => 1,
            'base_price' => 250000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Step 1: User initially had an old photo
        $oldImg = $roomType->images()->create([
            'path' => 'room-images/old_photo.webp',
            'medium_path' => 'room-images/old_photo_medium.webp',
            'is_cover' => true,
            'sort_order' => 1,
        ]);

        // Step 2: User deleted old photo via admin
        $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.room-images.destroy', $oldImg));

        $this->assertDatabaseMissing('room_images', ['id' => $oldImg->id]);

        // Step 3: User uploaded new photo via room type update
        $newVariantJson = json_encode([
            'original' => 'room-images/new_photo.webp',
            'thumb' => 'room-images/new_photo_thumb.webp',
            'medium' => 'room-images/new_photo_medium.webp',
            'large' => 'room-images/new_photo_large.webp',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.room-types.update', $roomType), [
                'name' => 'King Bed Test',
                'capacity' => 2,
                'bed_count' => 1,
                'base_price' => 250000,
                'is_active' => true,
                'sort_order' => 1,
                'images' => [$newVariantJson],
            ]);

        // Verify the new image exists and is set as cover
        $newImage = $roomType->fresh()->images()->first();
        $this->assertNotNull($newImage);
        $this->assertTrue($newImage->is_cover);
        $this->assertEquals('room-images/new_photo_medium.webp', $newImage->medium_path);

        // Step 4: Verify the public homepage renders the new photo and NOT "Foto belum tersedia"
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee($roomType->name);
        $homeResponse->assertSee($newImage->medium_url);
        $homeResponse->assertDontSee('Foto belum tersedia');
    }

    public function test_admin_can_force_newly_uploaded_image_as_cover_when_images_already_exist(): void
    {
        $roomType = RoomType::create([
            'name' => 'Royal Suite',
            'slug' => 'royal-suite',
            'capacity' => 2,
            'bed_count' => 1,
            'base_price' => 500000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $existingImg1 = $roomType->images()->create([
            'path' => 'room-images/existing1.webp',
            'is_cover' => true,
            'sort_order' => 1,
        ]);

        $existingImg2 = $roomType->images()->create([
            'path' => 'room-images/existing2.webp',
            'is_cover' => false,
            'sort_order' => 2,
        ]);

        $newVariantJson = json_encode([
            'original' => 'room-images/brand_new.webp',
            'thumb' => 'room-images/brand_new_thumb.webp',
            'medium' => 'room-images/brand_new_medium.webp',
            'large' => 'room-images/brand_new_large.webp',
        ]);

        // Upload with set_new_as_cover = true
        $this->actingAs($this->admin, 'admin')
            ->put(route('admin.room-types.update', $roomType), [
                'name' => 'Royal Suite',
                'capacity' => 2,
                'bed_count' => 1,
                'base_price' => 500000,
                'is_active' => true,
                'sort_order' => 1,
                'set_new_as_cover' => 1,
                'images' => [$newVariantJson],
            ]);

        $brandNewImg = $roomType->fresh()->images()->where('path', 'room-images/brand_new.webp')->first();
        $this->assertNotNull($brandNewImg);
        $this->assertTrue($brandNewImg->is_cover);
        $this->assertFalse($existingImg1->fresh()->is_cover);
        $this->assertFalse($existingImg2->fresh()->is_cover);
        $this->assertEquals($brandNewImg->id, $roomType->fresh()->cover_image->id);
    }
}