<?php

namespace Tests\Feature\Booking;

use App\Enums\BookingStatus;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingAccessTest extends TestCase
{
    use RefreshDatabase;

    private Booking $booking;
    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();

        $roomType = RoomType::create([
            'name' => 'Twin',
            'slug' => 'twin',
            'capacity' => 2,
            'bed_count' => 2,
            'base_price' => 250000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $room = Room::create([
            'room_type_id' => $roomType->id,
            'code' => 'TWIN-01',
            'name' => 'Twin 01',
            'status' => 'active',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->booking = Booking::create([
            'booking_code' => 'BKG-TEST001',
            'user_id' => $this->owner->id,
            'room_id' => $room->id,
            'source' => 'website',
            'status' => BookingStatus::PendingPayment->value,
            'payment_status' => 'unpaid',
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'nights' => 2,
            'guest_count' => 1,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
            'guest_whatsapp' => '628123456789',
            'room_type_name_snapshot' => 'Twin',
            'room_name_snapshot' => 'Twin 01',
            'price_per_night_snapshot' => 250000,
            'subtotal' => 500000,
            'total_amount' => 500000,
            'guest_access_token_hash' => hash('sha256', 'valid-token-123'),
            'payment_expires_at' => now()->addMinutes(30),
        ]);
    }

    public function test_booking_code_alone_cannot_access_payment(): void
    {
        // Not logged in, no session grant
        $response = $this->get(route('booking.pay', $this->booking->booking_code));
        $response->assertStatus(403);
    }

    public function test_member_owner_can_access_payment(): void
    {
        $response = $this->actingAs($this->owner)
            ->get(route('booking.pay', $this->booking->booking_code));

        // Should get payment page or RuntimeException (no Midtrans in test)
        // The point is it shouldn't be 403
        $this->assertNotEquals(403, $response->status());
    }

    public function test_non_owner_member_cannot_access_payment(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get(route('booking.pay', $this->booking->booking_code));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_booking_detail(): void
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.bookings.show', $this->booking));

        $response->assertStatus(200);
    }

    public function test_verified_guest_can_access_booking(): void
    {
        // Verify by WhatsApp (grants session access and redirects to detail)
        $response = $this->post(route('booking.verify'), [
            'booking_code' => $this->booking->booking_code,
            'guest_whatsapp' => '08123456789',
        ]);

        $response->assertRedirect(route('booking.guest.detail', $this->booking->booking_code));

        // Now they should be able to access payment page with session grant
        $response = $this->get(route('booking.pay', $this->booking->booking_code));
        $this->assertNotEquals(403, $response->status());
    }

    public function test_invalid_whatsapp_denied_access(): void
    {
        $response = $this->post(route('booking.verify'), [
            'booking_code' => $this->booking->booking_code,
            'guest_whatsapp' => '08999999999',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_valid_token_grants_access_via_url(): void
    {
        // Token-based access via URL query parameter on guest detail page
        $response = $this->get(route('booking.guest.detail', [
            'bookingCode' => $this->booking->booking_code,
            'access' => 'valid-token-123',
        ]));

        $response->assertStatus(200);
    }

    public function test_valid_whatsapp_grants_access(): void
    {
        $response = $this->post(route('booking.verify'), [
            'booking_code' => $this->booking->booking_code,
            'guest_whatsapp' => '08123456789',
        ]);

        // Should redirect to detail page (successful verification)
        $response->assertRedirect(route('booking.guest.detail', $this->booking->booking_code));
    }

    public function test_wrong_whatsapp_denied(): void
    {
        $response = $this->post(route('booking.verify'), [
            'booking_code' => $this->booking->booking_code,
            'guest_whatsapp' => '08111111111',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_wrong_booking_code_denied(): void
    {
        $response = $this->post(route('booking.verify'), [
            'booking_code' => 'BKG-NONEXIST',
            'guest_whatsapp' => '08123456789',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_whatsapp_normalization_works(): void
    {
        // The booking has guest_whatsapp '628123456789'
        // All these formats should match:
        $formats = ['08123456789', '628123456789', '+628123456789', '0812 345 6789', '0812-345-6789'];

        foreach ($formats as $format) {
            $response = $this->post(route('booking.verify'), [
                'booking_code' => $this->booking->booking_code,
                'guest_whatsapp' => $format,
            ]);

            $response->assertRedirect(route('booking.guest.detail', $this->booking->booking_code));
        }
    }
}
