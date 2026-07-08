<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminRouteSmokeTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::create([
            'name' => 'Smoke Test Admin',
            'email' => 'smoke@test.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function dashboard_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    /** @test */
    public function bookings_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.bookings.index'))
            ->assertOk();
    }

    /** @test */
    public function bookings_index_with_filters(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.bookings.index', [
                'status' => 'confirmed',
                'source' => 'website',
                'search' => 'test',
            ]))
            ->assertOk();
    }

    /** @test */
    public function bookings_create_loads(): void
    {
        $rt = RoomType::create([
            'name' => 'Twin', 'slug' => 'twin', 'capacity' => 2,
            'bed_count' => 2, 'base_price' => 250000, 'is_active' => true, 'sort_order' => 1,
        ]);
        Room::create([
            'room_type_id' => $rt->id, 'name' => 'Twin 01', 'code' => 'TWIN-01',
            'status' => 'active', 'is_active' => true, 'sort_order' => 1,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.bookings.create'))
            ->assertOk();
    }

    /** @test */
    public function room_blocks_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.room-blocks.index'))
            ->assertOk();
    }

    /** @test */
    public function room_blocks_create_loads(): void
    {
        $rt = RoomType::create([
            'name' => 'Twin', 'slug' => 'twin-rb', 'capacity' => 2,
            'bed_count' => 2, 'base_price' => 250000, 'is_active' => true, 'sort_order' => 1,
        ]);
        Room::create([
            'room_type_id' => $rt->id, 'name' => 'Twin 01', 'code' => 'TWIN-01-RB',
            'status' => 'active', 'is_active' => true, 'sort_order' => 1,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.room-blocks.create'))
            ->assertOk();
    }

    /** @test */
    public function room_types_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.room-types.index'))
            ->assertOk();
    }

    /** @test */
    public function rooms_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.rooms.index'))
            ->assertOk();
    }

    /** @test */
    public function facilities_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.facilities.index'))
            ->assertOk();
    }

    /** @test */
    public function galleries_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.galleries.index'))
            ->assertOk();
    }

    /** @test */
    public function promotions_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.promotions.index'))
            ->assertOk();
    }

    /** @test */
    public function promotions_create_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.promotions.create'))
            ->assertOk();
    }

    /** @test */
    public function loyalty_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.loyalty.index'))
            ->assertOk();
    }

    /** @test */
    public function expenses_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.expenses.index'))
            ->assertOk();
    }

    /** @test */
    public function reports_revenue_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports.revenue'))
            ->assertOk();
    }

    /** @test */
    public function reports_occupancy_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports.occupancy'))
            ->assertOk();
    }

    /** @test */
    public function reports_profit_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports.profit'))
            ->assertOk();
    }

    /** @test */
    public function reports_sources_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.reports.sources'))
            ->assertOk();
    }

    /** @test */
    public function policies_index_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.policies.index'))
            ->assertOk();
    }

    /** @test */
    public function settings_general_loads(): void
    {
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.settings.edit', 'general'))
            ->assertOk();
    }

    /** @test */
    public function dashboard_empty_state_no_errors(): void
    {
        // Dashboard with zero data should not error
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Server Error')
            ->assertDontSee('Undefined variable');
    }
}
