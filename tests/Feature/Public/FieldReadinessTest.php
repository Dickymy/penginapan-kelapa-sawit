<?php

namespace Tests\Feature\Public;

use App\Models\Setting;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldReadinessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function homepage_shows_search_form_with_defaults(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Cari Kamar Tersedia');
        $response->assertSee(date('Y-m-d'));
    }

    /** @test */
    public function homepage_shows_booking_options_section(): void
    {
        $response = $this->get(route('home'));
        $response->assertSee('Pilih cara yang paling nyaman');
        $response->assertSee('Booking Langsung');
        $response->assertSee('Tidak perlu membuat akun');
    }

    /** @test */
    public function homepage_shows_property_info_with_active_room_count(): void
    {
        $roomType = RoomType::create([
            'name' => 'Twin', 'slug' => 'twin', 'capacity' => 2,
            'bed_count' => 2, 'base_price' => 250000, 'is_active' => true, 'sort_order' => 0,
        ]);

        Room::create(['room_type_id' => $roomType->id, 'code' => 'TW01', 'name' => 'Twin 01', 'is_active' => true, 'sort_order' => 1]);
        Room::create(['room_type_id' => $roomType->id, 'code' => 'TW02', 'name' => 'Twin 02', 'is_active' => true, 'sort_order' => 2]);

        $response = $this->get(route('home'));
        $response->assertSee('2 kamar aktif');
        $response->assertSee('satu lantai');
    }

    /** @test */
    public function homepage_does_not_hardcode_multi_story(): void
    {
        $response = $this->get(route('home'));
        $content = $response->getContent();
        $this->assertStringNotContainsString('2 lantai', $content);
        $this->assertStringNotContainsString('3 lantai', $content);
        $this->assertStringNotContainsString('gedung', $content);
        $this->assertStringNotContainsString('tower', $content);
    }

    /** @test */
    public function homepage_shows_location_kota_bangun_ii(): void
    {
        $response = $this->get(route('home'));
        $response->assertSee('Kota Bangun II');
        $response->assertSee('Kutai Kartanegara');
    }

    /** @test */
    public function homepage_whatsapp_uses_normalizer(): void
    {
        Setting::set('contact', 'whatsapp', '081256971234');

        $response = $this->get(route('home'));
        $response->assertSee('wa.me/6281256971234');
        $response->assertDontSee('wa.me/081256971234');
    }

    /** @test */
    public function location_page_renders_properly(): void
    {
        Setting::set('contact', 'whatsapp', '081256971234');

        $response = $this->get(route('location'));
        $response->assertStatus(200);
        $response->assertSee('Lokasi Penginapan');
        $response->assertSee('Kota Bangun II');
        $response->assertSee('wa.me/6281256971234');
    }

    /** @test */
    public function guest_booking_remains_accessible(): void
    {
        $roomType = RoomType::create([
            'name' => 'Twin', 'slug' => 'twin', 'capacity' => 2,
            'bed_count' => 2, 'base_price' => 250000, 'is_active' => true, 'sort_order' => 0,
        ]);

        Room::create(['room_type_id' => $roomType->id, 'code' => 'TW01', 'name' => 'Twin 01', 'is_active' => true, 'sort_order' => 1]);

        $response = $this->get(route('booking.checkout', [
            'room_type_id' => $roomType->id,
            'check_in' => now()->format('Y-m-d'),
            'check_out' => now()->addDay()->format('Y-m-d'),
            'guest_count' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tidak perlu akun untuk memesan');
    }

    /** @test */
    public function welcome_choice_only_shown_to_guests(): void
    {
        $response = $this->get(route('home'));
        $response->assertSee('Selamat datang di Penginapan Kelapa Sawit');
    }

    /** @test */
    public function welcome_choice_not_shown_to_authenticated(): void
    {
        $user = \App\Models\User::factory()->create();
        $response = $this->actingAs($user)->get(route('home'));
        $response->assertDontSee('Selamat datang di Penginapan Kelapa Sawit');
    }
}
