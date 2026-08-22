<?php

namespace Tests\Feature\Admin;

use App\Enums\BookingStatus;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarTest extends TestCase
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

    public function test_calendar_index_accessible_by_admin()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.calendar.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.calendar.index');
    }

    public function test_calendar_index_redirects_guest()
    {
        $response = $this->get(route('admin.calendar.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_calendar_data_returns_json_with_correct_structure_and_overlap()
    {
        // Set dates
        $startDate = Carbon::today()->startOfMonth();
        $endDate = Carbon::today()->endOfMonth();

        // Create a room
        $room = Room::factory()->create([
            'is_active' => true,
        ]);

        // Create booking inside range
        $bookingIn = Booking::factory()->create([
            'room_id' => $room->id,
            'status' => BookingStatus::Confirmed,
            'check_in' => $startDate->copy()->addDays(2),
            'check_out' => $startDate->copy()->addDays(5),
        ]);

        // Create booking outside range (last month)
        $bookingOut = Booking::factory()->create([
            'room_id' => $room->id,
            'status' => BookingStatus::Confirmed,
            'check_in' => $startDate->copy()->subDays(10),
            'check_out' => $startDate->copy()->subDays(5),
        ]);

        // Create room block inside range
        $blockIn = RoomBlock::create([
            'room_id' => $room->id,
            'start_date' => $startDate->copy()->addDays(10),
            'end_date' => $startDate->copy()->addDays(12),
            'reason' => 'Maintenance',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.calendar.data', [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'rooms' => [
                '*' => ['id', 'name', 'room_type']
            ],
            'bookings' => [
                '*' => ['id', 'booking_code', 'room_id', 'guest_name', 'check_in', 'check_out', 'status', 'status_label']
            ],
            'room_blocks' => [
                '*' => ['id', 'room_id', 'start_date', 'end_date', 'reason']
            ]
        ]);

        // Assert only inside items are returned
        $response->assertJsonCount(1, 'rooms');
        $response->assertJsonCount(1, 'bookings');
        $response->assertJsonCount(1, 'room_blocks');

        $this->assertEquals($bookingIn->id, $response->json('bookings.0.id'));
        $this->assertEquals($blockIn->id, $response->json('room_blocks.0.id'));
    }
}
