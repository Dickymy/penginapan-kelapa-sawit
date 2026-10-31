<?php

namespace Tests\Feature\Booking;

use App\Enums\PromotionType;
use App\Enums\PromotionUsageStatus;
use App\Models\Booking;
use App\Models\PolicyVersion;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingWithPromotionTest extends TestCase
{
    use RefreshDatabase;

    private RoomType $roomType;
    private Room $room;
    private Promotion $percentagePromo;
    private Promotion $fixedPromo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roomType = RoomType::create([
            'name' => 'Superior Double',
            'slug' => 'superior-double',
            'capacity' => 2,
            'bed_count' => 1,
            'base_price' => 300000,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->room = Room::create([
            'room_type_id' => $this->roomType->id,
            'code' => 'SUP-01',
            'name' => 'Superior 01',
            'status' => 'active',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        PolicyVersion::create([
            'policy_key' => 'guest_policy',
            'version' => '2026-07-v1',
            'title' => 'Kebijakan Tamu',
            'content' => 'Isi kebijakan.',
            'is_current' => true,
            'published_at' => now(),
        ]);

        $this->percentagePromo = Promotion::create([
            'code' => 'DISKON10',
            'name' => 'Diskon 10% Spesial',
            'type' => PromotionType::Percentage,
            'value' => 10,
            'maximum_discount' => 100000,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'minimum_booking_amount' => 100000,
            'is_active' => true,
            'show_in_listing' => true,
        ]);

        $this->fixedPromo = Promotion::create([
            'code' => 'HEMAT50RB',
            'name' => 'Potongan 50 Ribu',
            'type' => PromotionType::Fixed,
            'value' => 50000,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'minimum_booking_amount' => 200000,
            'is_active' => true,
            'show_in_listing' => true,
        ]);
    }

    public function test_checkout_page_displays_active_promotions_and_promo_input(): void
    {
        $response = $this->get(route('booking.checkout', [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
            'guest_count' => 2,
        ]));

        $response->assertStatus(200);
        $response->assertSee('DISKON10');
        $response->assertSee('HEMAT50RB');
        $response->assertSee('Voucher & Kode Promo');
    }

    public function test_apply_promo_endpoint_returns_discount_for_percentage_promo(): void
    {
        // 2 nights x 300,000 = 600,000. 10% = 60,000.
        $response = $this->postJson(route('booking.apply-promo'), [
            'promo_code' => 'DISKON10',
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'code' => 'DISKON10',
            'discount' => 60000,
        ]);
    }

    public function test_apply_promo_endpoint_returns_discount_for_fixed_promo(): void
    {
        $response = $this->postJson(route('booking.apply-promo'), [
            'promo_code' => 'HEMAT50RB',
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'code' => 'HEMAT50RB',
            'discount' => 50000,
        ]);
    }

    public function test_apply_promo_fails_for_non_existent_code(): void
    {
        $response = $this->postJson(route('booking.apply-promo'), [
            'promo_code' => 'TIDAKADA',
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Kode promo tidak ditemukan.',
        ]);
    }

    public function test_apply_promo_fails_if_booking_amount_below_minimum(): void
    {
        $highMinPromo = Promotion::create([
            'code' => 'MIN1JUTA',
            'name' => 'Min 1 Juta',
            'type' => PromotionType::Fixed,
            'value' => 100000,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'minimum_booking_amount' => 1000000,
            'is_active' => true,
            'show_in_listing' => true,
        ]);

        // 1 night = 300,000 < 1,000,000
        $response = $this->postJson(route('booking.apply-promo'), [
            'promo_code' => 'MIN1JUTA',
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Minimum booking Rp1.000.000',
        ]);
    }

    public function test_user_can_create_booking_with_promo_code(): void
    {
        $data = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(3)->format('Y-m-d'),
            'guest_count' => 2,
            'guest_name' => 'Budi Santoso',
            'guest_email' => 'budi@example.com',
            'guest_whatsapp' => '081234567890',
            'policy_accepted' => '1',
            'idempotency_key' => Str::uuid()->toString(),
            'promo_code' => 'DISKON10',
        ];

        $response = $this->post(route('booking.store'), $data);

        $response->assertRedirect();

        $booking = Booking::where('guest_email', 'budi@example.com')->first();
        $this->assertNotNull($booking);
        $this->assertEquals(60000, $booking->promotion_discount);
        $this->assertEquals(540000, $booking->total_amount);

        // Verify promo usage was recorded as reserved
        $this->assertDatabaseHas('promotion_usages', [
            'promotion_id' => $this->percentagePromo->id,
            'booking_id' => $booking->id,
            'status' => PromotionUsageStatus::Reserved->value,
            'discount_amount' => 60000,
        ]);

        // Verify that following redirect to confirmation page renders correctly
        $confirmationResponse = $this->get(route('booking.confirmation', $booking->booking_code));
        $confirmationResponse->assertStatus(200);
        $confirmationResponse->assertSee('Booking Berhasil!');
        $confirmationResponse->assertSee($booking->booking_code);
    }

    public function test_hidden_promo_is_not_displayed_in_checkout_listing_but_applies_when_typed(): void
    {
        $hiddenPromo = Promotion::create([
            'code' => 'RAHASIA50',
            'name' => 'Diskon Rahasia Member VIP',
            'type' => PromotionType::Fixed,
            'value' => 50000,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'minimum_booking_amount' => 100000,
            'is_active' => true,
            'show_in_listing' => false,
        ]);

        // 1. Check checkout page: promo should NOT appear in listing
        $response = $this->get(route('booking.checkout', [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
            'guest_count' => 2,
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('RAHASIA50');
        $response->assertDontSee('Diskon Rahasia Member VIP');
        $response->assertSee('DISKON10');

        // 2. Check apply promo endpoint: typing code manually works!
        $applyResponse = $this->postJson(route('booking.apply-promo'), [
            'promo_code' => 'RAHASIA50',
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $applyResponse->assertStatus(200);
        $applyResponse->assertJson([
            'success' => true,
            'code' => 'RAHASIA50',
            'discount' => 50000,
        ]);

        // 3. User can book with hidden promo
        $data = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(3)->format('Y-m-d'),
            'guest_count' => 2,
            'guest_name' => 'Ahmad Rahasia',
            'guest_email' => 'ahmad@example.com',
            'guest_whatsapp' => '081234567891',
            'policy_accepted' => '1',
            'idempotency_key' => Str::uuid()->toString(),
            'promo_code' => 'RAHASIA50',
        ];

        $storeResponse = $this->post(route('booking.store'), $data);
        $storeResponse->assertRedirect();

        $booking = Booking::where('guest_email', 'ahmad@example.com')->first();
        $this->assertNotNull($booking);
        $this->assertEquals(50000, $booking->promotion_discount);
    }

    public function test_when_checkout_listing_is_disabled_globally_no_promos_appear_in_listing_but_manual_code_works(): void
    {
        \App\Models\Setting::set('booking', 'show_promo_listing', false);

        $response = $this->get(route('booking.checkout', [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
            'guest_count' => 2,
        ]));

        $response->assertStatus(200);
        $response->assertDontSee('DISKON10');
        $response->assertDontSee('HEMAT50RB');
        $response->assertDontSee('Promo Menarik Tersedia');

        // Manual apply still works
        $applyResponse = $this->postJson(route('booking.apply-promo'), [
            'promo_code' => 'DISKON10',
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $applyResponse->assertStatus(200);
        $applyResponse->assertJson([
            'success' => true,
            'code' => 'DISKON10',
        ]);
    }

    public function test_hundred_percent_discount_promo_calculates_full_discount_and_confirms_booking_without_payment(): void
    {
        $freePromo = Promotion::create([
            'code' => 'GRATIS100',
            'name' => 'Diskon 100% Menginap Gratis',
            'type' => PromotionType::Percentage,
            'value' => 100,
            'maximum_discount' => 0, // Even if entered as 0, should not cap at 0
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
            'minimum_booking_amount' => 10000,
            'is_active' => true,
            'show_in_listing' => false,
        ]);

        // 2 nights x 300,000 = 600,000
        $applyResponse = $this->postJson(route('booking.apply-promo'), [
            'promo_code' => 'GRATIS100',
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $applyResponse->assertStatus(200);
        $applyResponse->assertJson([
            'success' => true,
            'code' => 'GRATIS100',
            'discount' => 600000,
        ]);

        // Create booking with 100% discount
        $data = [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDay()->format('Y-m-d'),
            'check_out' => now()->addDays(3)->format('Y-m-d'),
            'guest_count' => 2,
            'guest_name' => 'Siti Free',
            'guest_email' => 'siti@example.com',
            'guest_whatsapp' => '081234567899',
            'policy_accepted' => '1',
            'idempotency_key' => Str::uuid()->toString(),
            'promo_code' => 'GRATIS100',
        ];

        $storeResponse = $this->post(route('booking.store'), $data);
        $storeResponse->assertRedirect();

        $booking = Booking::where('guest_email', 'siti@example.com')->first();
        $this->assertNotNull($booking);
        $this->assertEquals(0, $booking->total_amount);
        $this->assertEquals(600000, $booking->promotion_discount);
        $this->assertEquals(\App\Enums\BookingStatus::Confirmed, $booking->status);
        $this->assertEquals(\App\Enums\PaymentStatus::Paid, $booking->payment_status);

        // Verify promo was consumed
        $this->assertDatabaseHas('promotion_usages', [
            'promotion_id' => $freePromo->id,
            'booking_id' => $booking->id,
            'status' => PromotionUsageStatus::Consumed->value,
            'discount_amount' => 600000,
        ]);

        // Verify confirmation page renders without errors
        $confirmationResponse = $this->get(route('booking.confirmation', $booking->booking_code));
        $confirmationResponse->assertStatus(200);
        $confirmationResponse->assertSee('Booking Berhasil!');
        $confirmationResponse->assertSee('Reservasi Anda telah terkonfirmasi dan tidak memerlukan pembayaran lagi.');
        $confirmationResponse->assertSee('Terkonfirmasi');
    }
}
