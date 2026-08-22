<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\NearbyPlace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NearbyPlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_nearby_places_index()
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        NearbyPlace::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.nearby-places.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.nearby-places.index');
        $response->assertViewHas('places');
    }

    public function test_admin_can_create_nearby_place_with_image()
    {
        Storage::fake('public');
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->mock(\App\Services\ImageUploadService::class, function ($mock) {
            $mock->shouldReceive('upload')->andReturn('nearby-places/fake-image.jpg');
        });

        $file = UploadedFile::fake()->image('place.jpg');

        $response = $this->actingAs($admin, 'admin')->post(route('admin.nearby-places.store'), [
            'name' => 'New Place',
            'category' => 'Wisata',
            'distance' => '1 km',
            'description' => 'A nice place',
            'image' => $file,
            'map_link' => 'https://maps.example.com',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.nearby-places.index'));
        
        $place = NearbyPlace::first();
        $this->assertEquals('New Place', $place->name);
        $this->assertEquals('nearby-places/fake-image.jpg', $place->image);
    }

    public function test_admin_can_update_nearby_place()
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        $place = NearbyPlace::factory()->create(['name' => 'Old Name', 'is_active' => true]);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.nearby-places.update', $place), [
            'name' => 'Updated Name',
            'category' => 'Wisata',
            'sort_order' => 2,
            // Omit is_active to simulate unchecked checkbox
        ]);

        $response->assertRedirect(route('admin.nearby-places.index'));
        $this->assertEquals('Updated Name', $place->fresh()->name);
        $this->assertFalse($place->fresh()->is_active);
    }

    public function test_admin_can_delete_nearby_place()
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        $place = NearbyPlace::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.nearby-places.destroy', $place));

        $response->assertRedirect(route('admin.nearby-places.index'));
        $this->assertDatabaseMissing('nearby_places', ['id' => $place->id]);
    }

    public function test_public_can_view_active_nearby_places()
    {
        NearbyPlace::factory()->create(['name' => 'Active Place', 'is_active' => true, 'sort_order' => 1]);
        NearbyPlace::factory()->create(['name' => 'Inactive Place', 'is_active' => false, 'sort_order' => 2]);

        $response = $this->get(route('nearby-places'));

        $response->assertStatus(200);
        $response->assertSee('Active Place');
        $response->assertDontSee('Inactive Place');
    }
}
