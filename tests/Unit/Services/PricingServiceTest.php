<?php

namespace Tests\Unit\Services;

use App\Models\RoomType;
use App\Services\PricingService;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;
    private PricingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PricingService::class);
    }

    public function test_calculate_nights_one_night(): void
    {
        $checkIn = Carbon::parse('2026-07-10');
        $checkOut = Carbon::parse('2026-07-11');

        $this->assertEquals(1, $this->service->calculateNights($checkIn, $checkOut));
    }

    public function test_calculate_nights_seven_nights(): void
    {
        $checkIn = Carbon::parse('2026-07-10');
        $checkOut = Carbon::parse('2026-07-17');

        $this->assertEquals(7, $this->service->calculateNights($checkIn, $checkOut));
    }

    public function test_calculate_nights_boundary(): void
    {
        $checkIn = Carbon::parse('2026-07-31');
        $checkOut = Carbon::parse('2026-08-02');

        $this->assertEquals(2, $this->service->calculateNights($checkIn, $checkOut));
    }

    public function test_calculate_quote(): void
    {
        $roomType = \App\Models\RoomType::factory()->create([
            'base_price' => 350000,
        ]);

        $checkIn = Carbon::parse('2026-07-10');
        $checkOut = Carbon::parse('2026-07-12');

        $quote = $this->service->calculateQuote($roomType, $checkIn, $checkOut);

        $this->assertEquals(2, $quote['nights']);
        $this->assertEquals(350000, $quote['price_per_night']);
        $this->assertEquals(700000, $quote['subtotal']);
        $this->assertEquals(0, $quote['promotion_discount']);
        $this->assertEquals(0, $quote['points_discount']);
        $this->assertEquals(700000, $quote['total_amount']);
        $this->assertEquals(700000, $quote['eligible_loyalty_amount']);
    }

    public function test_calculate_quote_with_overrides(): void
    {
        $roomType = \App\Models\RoomType::factory()->create([
            'base_price' => 300000,
        ]);

        \App\Models\RateOverride::create([
            'room_type_id' => $roomType->id,
            'date' => '2026-07-11', // Malam kedua
            'price' => 450000,
            'label' => 'Weekend',
        ]);

        $checkIn = Carbon::parse('2026-07-10');
        $checkOut = Carbon::parse('2026-07-12'); // 2 malam: 10 & 11

        $quote = $this->service->calculateQuote($roomType, $checkIn, $checkOut);

        $this->assertEquals(2, $quote['nights']);
        $this->assertEquals(300000, $quote['price_per_night']); // base
        $this->assertEquals(750000, $quote['subtotal']); // 300k + 450k
        $this->assertEquals(2, count($quote['night_prices']));
        $this->assertEquals(300000, $quote['night_prices'][0]['price']);
        $this->assertNull($quote['night_prices'][0]['label']);
        $this->assertEquals(450000, $quote['night_prices'][1]['price']);
        $this->assertEquals('Weekend', $quote['night_prices'][1]['label']);
    }
}
