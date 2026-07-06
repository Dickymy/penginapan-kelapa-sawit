<?php

namespace Tests\Unit\Services;

use App\Models\RoomType;
use App\Services\PricingService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class PricingServiceTest extends TestCase
{
    private PricingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PricingService();
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
        $roomType = new RoomType();
        $roomType->base_price = 350000;

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
}
