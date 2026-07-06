<?php

namespace Tests\Feature\Public;

use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Penginapan Kelapa Sawit');
    }

    public function test_home_shows_active_room_types(): void
    {
        RoomType::create([
            'name' => 'Deluxe',
            'slug' => 'deluxe',
            'capacity' => 2,
            'bed_count' => 1,
            'base_price' => 350000,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        RoomType::create([
            'name' => 'Hidden',
            'slug' => 'hidden',
            'capacity' => 1,
            'bed_count' => 1,
            'base_price' => 100000,
            'is_active' => false,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('home'));
        $response->assertSee('Deluxe');
        $response->assertDontSee('Hidden');
    }

    public function test_rooms_index_page_renders(): void
    {
        $response = $this->get(route('rooms.index'));
        $response->assertStatus(200);
    }

    public function test_room_detail_shows_active_room_type(): void
    {
        RoomType::create([
            'name' => 'Twin',
            'slug' => 'twin',
            'capacity' => 2,
            'bed_count' => 2,
            'base_price' => 250000,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('rooms.show', 'twin'));
        $response->assertStatus(200);
        $response->assertSee('Twin');
    }

    public function test_room_detail_404_for_inactive(): void
    {
        RoomType::create([
            'name' => 'Inactive',
            'slug' => 'inactive-room',
            'capacity' => 1,
            'bed_count' => 1,
            'base_price' => 0,
            'is_active' => false,
            'sort_order' => 0,
        ]);

        $response = $this->get(route('rooms.show', 'inactive-room'));
        $response->assertStatus(404);
    }

    public function test_about_page_renders(): void
    {
        $response = $this->get(route('about'));
        $response->assertStatus(200);
    }

    public function test_location_page_renders(): void
    {
        $response = $this->get(route('location'));
        $response->assertStatus(200);
    }

    public function test_policy_page_renders(): void
    {
        $response = $this->get(route('policy'));
        $response->assertStatus(200);
    }
}
