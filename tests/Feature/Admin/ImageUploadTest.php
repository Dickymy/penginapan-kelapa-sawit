<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_upload_image()
    {
        $response = $this->postJson(route('admin.upload.image'), [
            'file' => UploadedFile::fake()->image('test.jpg'),
            'directory' => 'nearby-places',
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_can_upload_image_to_nearby_places_with_compression_metadata()
    {
        Storage::fake('public');
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin_upload_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $fakePath = 'nearby-places/compressed_sample.webp';
        Storage::disk('public')->put($fakePath, 'fake-image-bytes-compressed');

        $this->mock(ImageUploadService::class, function ($mock) use ($fakePath) {
            $mock->shouldReceive('upload')->once()->andReturn($fakePath);
        });

        $file = UploadedFile::fake()->image('sembako-product.jpg', 800, 600)->size(1200); // 1200 KB

        $response = $this->actingAs($admin, 'admin')->postJson(route('admin.upload.image'), [
            'file' => $file,
            'directory' => 'nearby-places',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'path',
            'url',
            'size',
            'size_formatted',
            'original_name',
            'original_size',
            'original_size_formatted',
            'savings_percent',
        ]);

        $response->assertJson([
            'success' => true,
            'path' => $fakePath,
            'original_name' => 'sembako-product.jpg',
        ]);
    }

    public function test_admin_upload_rejects_invalid_directory()
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin_upload_bad_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->actingAs($admin, 'admin')->postJson(route('admin.upload.image'), [
            'file' => $file,
            'directory' => 'malicious_directory',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['directory']);
    }

    public function test_admin_upload_rejects_non_image_file()
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin_upload_txt_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($admin, 'admin')->postJson(route('admin.upload.image'), [
            'file' => $file,
            'directory' => 'nearby-places',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }
}
