<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Facility;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomTypeTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_room_types_index(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.room-types.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_room_type(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.room-types.store'), [
                'name' => 'Deluxe',
                'slug' => 'deluxe',
                'capacity' => 2,
                'bed_count' => 1,
                'base_price' => 350000,
                'is_active' => true,
                'sort_order' => 0,
            ]);

        $response->assertRedirect(route('admin.room-types.index'));
        $this->assertDatabaseHas('room_types', [
            'name' => 'Deluxe',
            'slug' => 'deluxe',
            'base_price' => 350000,
        ]);
    }

    public function test_room_type_requires_name(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.room-types.store'), [
                'name' => '',
                'capacity' => 2,
                'bed_count' => 1,
                'base_price' => 0,
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_toggle_room_type(): void
    {
        $roomType = RoomType::create([
            'name' => 'Test',
            'slug' => 'test',
            'capacity' => 1,
            'bed_count' => 1,
            'base_price' => 0,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.room-types.toggle', $roomType));

        $this->assertFalse($roomType->fresh()->is_active);
    }

    public function test_admin_can_assign_facilities(): void
    {
        $facility = Facility::create([
            'name' => 'AC',
            'slug' => 'ac',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.room-types.store'), [
                'name' => 'Suite',
                'slug' => 'suite',
                'capacity' => 2,
                'bed_count' => 1,
                'base_price' => 500000,
                'is_active' => true,
                'sort_order' => 0,
                'facilities' => [$facility->id],
            ]);

        $response->assertRedirect();
        $roomType = RoomType::where('slug', 'suite')->first();
        $this->assertTrue($roomType->facilities->contains($facility));
    }

    public function test_guest_cannot_access_admin_room_types(): void
    {
        $response = $this->get(route('admin.room-types.index'));
        $response->assertRedirect(route('admin.login'));
    }
}
