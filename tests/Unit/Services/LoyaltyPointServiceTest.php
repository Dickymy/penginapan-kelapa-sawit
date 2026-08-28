<?php

namespace Tests\Unit\Services;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\LoyaltyTransactionType;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\LoyaltyPointAllocation;
use App\Models\LoyaltyTransaction;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use App\Services\LoyaltyPointService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoyaltyPointServiceTest extends TestCase
{
    use RefreshDatabase;

    private LoyaltyPointService $service;
    private User $user;
    private RoomType $roomType;
    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(LoyaltyPointService::class);

        $this->user = User::factory()->create();

        $this->roomType = RoomType::factory()->create([
            'base_price' => 300_000,
        ]);

        $this->room = Room::factory()->create([
            'room_type_id' => $this->roomType->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    /**
     * Buat booking dengan snapshot dan status tertentu.
     */
    private function makeBooking(array $overrides = []): Booking
    {
        return Booking::factory()
            ->for($this->user)
            ->for($this->room)
            ->create(array_merge([
                'room_type_name_snapshot' => $this->roomType->name,
                'room_name_snapshot' => $this->room->name,
                'price_per_night_snapshot' => 300_000,
                'subtotal' => 300_000,
                'total_amount' => 300_000,
                'eligible_loyalty_amount' => 300_000,
                'source' => BookingSource::Website,
                'status' => BookingStatus::Completed,
            ], $overrides));
    }

    // -------------------------------------------------------------------------
    // getBalance
    // -------------------------------------------------------------------------

    public function test_get_balance_returns_zero_for_new_user(): void
    {
        $this->assertEquals(0, $this->service->getBalance($this->user));
    }

    public function test_get_balance_sums_all_transactions(): void
    {
        // Tambah earn
        LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => 500,
            'balance_after' => 500,
            'remaining_points' => 500,
            'description' => 'Earn test',
            'idempotency_key' => 'test:earn:balance-sum',
        ]);

        // Tambah redeem
        LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Redeem->value,
            'points' => -200,
            'balance_after' => 300,
            'remaining_points' => 0,
            'description' => 'Redeem test',
            'idempotency_key' => 'test:redeem:balance-sum',
        ]);

        $this->assertEquals(300, $this->service->getBalance($this->user));
    }

    // -------------------------------------------------------------------------
    // awardForCompletedBooking — earn
    // -------------------------------------------------------------------------

    public function test_earn_poin_setelah_booking_completed(): void
    {
        // 300.000 / 1.000 = 300 poin
        $booking = $this->makeBooking([
            'eligible_loyalty_amount' => 300_000,
        ]);

        $this->service->awardForCompletedBooking($booking);

        $this->assertEquals(300, $this->service->getBalance($this->user));

        $tx = LoyaltyTransaction::where('user_id', $this->user->id)
            ->where('type', LoyaltyTransactionType::Earn->value)
            ->first();

        $this->assertNotNull($tx);
        $this->assertEquals(300, $tx->points);
        $this->assertEquals(300, $tx->remaining_points);
        $this->assertNotNull($tx->expires_at);
        $this->assertEquals($booking->id, $tx->booking_id);
    }

    public function test_earn_sesuai_formula_floor(): void
    {
        // 1.599 / 1.000 = floor(1.599) = 1 poin
        $booking = $this->makeBooking(['eligible_loyalty_amount' => 1_599]);
        $this->service->awardForCompletedBooking($booking);
        $this->assertEquals(1, $this->service->getBalance($this->user));
    }

    public function test_earn_tidak_diberikan_jika_bukan_completed(): void
    {
        $booking = $this->makeBooking(['status' => BookingStatus::CheckedOut]);
        $this->service->awardForCompletedBooking($booking);
        $this->assertEquals(0, $this->service->getBalance($this->user));
    }

    public function test_earn_tidak_diberikan_untuk_guest_booking(): void
    {
        // Booking tanpa user_id
        $booking = $this->makeBooking(['user_id' => null]);
        $this->service->awardForCompletedBooking($booking);

        $this->assertEquals(0,
            LoyaltyTransaction::where('type', LoyaltyTransactionType::Earn->value)->count()
        );
    }

    public function test_earn_tidak_diberikan_untuk_sumber_ota(): void
    {
        $booking = $this->makeBooking(['source' => BookingSource::BookingCom]);
        $this->service->awardForCompletedBooking($booking);
        $this->assertEquals(0, $this->service->getBalance($this->user));
    }

    public function test_earn_tidak_diberikan_untuk_agoda(): void
    {
        $booking = $this->makeBooking(['source' => BookingSource::Agoda]);
        $this->service->awardForCompletedBooking($booking);
        $this->assertEquals(0, $this->service->getBalance($this->user));
    }

    public function test_earn_tidak_diberikan_untuk_traveloka(): void
    {
        $booking = $this->makeBooking(['source' => BookingSource::Traveloka]);
        $this->service->awardForCompletedBooking($booking);
        $this->assertEquals(0, $this->service->getBalance($this->user));
    }

    public function test_earn_diberikan_untuk_sumber_whatsapp(): void
    {
        $booking = $this->makeBooking(['source' => BookingSource::Whatsapp]);
        $this->service->awardForCompletedBooking($booking);
        $this->assertGreaterThan(0, $this->service->getBalance($this->user));
    }

    public function test_earn_diberikan_untuk_sumber_walk_in(): void
    {
        $booking = $this->makeBooking(['source' => BookingSource::WalkIn]);
        $this->service->awardForCompletedBooking($booking);
        $this->assertGreaterThan(0, $this->service->getBalance($this->user));
    }

    public function test_earn_tidak_diberikan_jika_jumlah_nol(): void
    {
        // eligible_loyalty_amount 500 < earn_divisor 1.000 → floor(0.5) = 0 poin
        $booking = $this->makeBooking(['eligible_loyalty_amount' => 500]);
        $this->service->awardForCompletedBooking($booking);
        $this->assertEquals(0, $this->service->getBalance($this->user));
    }

    // -------------------------------------------------------------------------
    // awardForCompletedBooking — idempotency
    // -------------------------------------------------------------------------

    public function test_earn_idempotency_tidak_ganda_jika_dipanggil_dua_kali(): void
    {
        $booking = $this->makeBooking(['eligible_loyalty_amount' => 300_000]);

        $this->service->awardForCompletedBooking($booking);
        $this->service->awardForCompletedBooking($booking); // panggil kedua kali

        // Hanya 1 transaksi earn yang terbuat
        $count = LoyaltyTransaction::where('user_id', $this->user->id)
            ->where('type', LoyaltyTransactionType::Earn->value)
            ->where('booking_id', $booking->id)
            ->count();

        $this->assertEquals(1, $count);
        $this->assertEquals(300, $this->service->getBalance($this->user));
    }

    public function test_earn_idempotency_setelah_cache_diupdate(): void
    {
        $booking = $this->makeBooking(['eligible_loyalty_amount' => 1_000_000]);

        // Panggil 3x — tetap 1 transaksi
        for ($i = 0; $i < 3; $i++) {
            $this->service->awardForCompletedBooking($booking);
        }

        $this->assertEquals(1_000, $this->service->getBalance($this->user));
        $this->assertEquals(
            1,
            LoyaltyTransaction::where('booking_id', $booking->id)->count()
        );
    }

    // -------------------------------------------------------------------------
    // redeemForBooking
    // -------------------------------------------------------------------------

    private function seedPoints(int $points, ?Carbon $expiresAt = null): LoyaltyTransaction
    {
        $tx = LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => $points,
            'balance_after' => $points,
            'remaining_points' => $points,
            'description' => 'Seed points',
            'expires_at' => $expiresAt ?? now()->addMonths(18),
            'idempotency_key' => 'seed:' . \Illuminate\Support\Str::uuid(),
        ]);

        $this->user->update(['loyalty_balance_cache' => $points]);

        return $tx;
    }

    public function test_redeem_mengurangi_saldo(): void
    {
        $this->seedPoints(500);

        // Booking subtotal 1.000.000 → cap 20% = 200.000 = 4000 poin
        // Kita redeem 200 poin = 200 * 50 = Rp10.000 (lebih kecil dari cap)
        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $this->service->redeemForBooking($this->user, $booking, 200);

        $this->assertEquals(300, $this->service->getBalance($this->user));
    }

    public function test_redeem_menghasilkan_diskon_yang_benar(): void
    {
        $this->seedPoints(500);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        // 200 poin * Rp50 = Rp10.000
        $discount = $this->service->redeemForBooking($this->user, $booking, 200);

        $this->assertEquals(10_000, $discount);
    }

    public function test_redeem_dibatasi_cap_20_persen_subtotal(): void
    {
        $this->seedPoints(10_000);
        $this->user->update(['loyalty_balance_cache' => 10_000]);

        // subtotal 100.000 → max 20% = 20.000 / 50 = 400 poin max
        // Kita minta 1.000 poin, tapi di-cap ke 400
        $booking = $this->makeBooking([
            'subtotal' => 100_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $discount = $this->service->redeemForBooking($this->user, $booking, 1_000);

        // Discount di-cap ke Rp20.000 (20% dari 100.000)
        $this->assertEquals(20_000, $discount);

        // Poin yang terpotong = ceil(20.000 / 50) = 400
        $redeemTx = LoyaltyTransaction::where('type', LoyaltyTransactionType::Redeem->value)->first();
        $this->assertEquals(-400, $redeemTx->points);
    }

    public function test_redeem_ditolak_jika_kurang_dari_minimum(): void
    {
        $this->seedPoints(500);

        $booking = $this->makeBooking(['status' => BookingStatus::PendingPayment]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/minimum/i');

        $this->service->redeemForBooking($this->user, $booking, 50); // min 100
    }

    public function test_redeem_ditolak_jika_saldo_tidak_cukup(): void
    {
        $this->seedPoints(50); // saldo hanya 50 poin
        $this->user->update(['loyalty_balance_cache' => 50]);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/tidak mencukupi/i');

        $this->service->redeemForBooking($this->user, $booking, 200); // butuh 200, punya 50
    }

    // -------------------------------------------------------------------------
    // redeemForBooking — FIFO
    // -------------------------------------------------------------------------

    public function test_redeem_fifo_menggunakan_lot_expiry_terdekat_duluan(): void
    {
        // Lot A: expiry jauh (18 bulan) — dibuat PERTAMA
        $lotA = LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => 300,
            'balance_after' => 300,
            'remaining_points' => 300,
            'description' => 'Lot A - expiry jauh',
            'expires_at' => now()->addMonths(18),
            'idempotency_key' => 'test:fifo:lot-a',
        ]);

        // Lot B: expiry dekat (1 bulan) — dibuat KEDUA
        $lotB = LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => 300,
            'balance_after' => 600,
            'remaining_points' => 300,
            'description' => 'Lot B - expiry dekat',
            'expires_at' => now()->addMonth(1),
            'idempotency_key' => 'test:fifo:lot-b',
        ]);

        $this->user->update(['loyalty_balance_cache' => 600]);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        // Redeem 200 poin — harus ambil dari lot B dulu (expiry terdekat)
        $this->service->redeemForBooking($this->user, $booking, 200);

        $lotB->refresh();
        $lotA->refresh();

        $this->assertEquals(100, $lotB->remaining_points); // 300 - 200 = 100
        $this->assertEquals(300, $lotA->remaining_points); // tidak tersentuh
    }

    public function test_redeem_fifo_melintasi_dua_lot_jika_perlu(): void
    {
        // Lot A: expiry dekat, hanya 100 poin
        $lotA = LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => 100,
            'balance_after' => 100,
            'remaining_points' => 100,
            'description' => 'Lot A - expiry dekat',
            'expires_at' => now()->addMonth(1),
            'idempotency_key' => 'test:two-lots:lot-a',
        ]);

        // Lot B: expiry jauh, 300 poin
        $lotB = LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => 300,
            'balance_after' => 400,
            'remaining_points' => 300,
            'description' => 'Lot B - expiry jauh',
            'expires_at' => now()->addMonths(18),
            'idempotency_key' => 'test:two-lots:lot-b',
        ]);

        $this->user->update(['loyalty_balance_cache' => 400]);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        // Redeem 250 poin → ambil semua 100 dari lotA, sisanya 150 dari lotB
        $this->service->redeemForBooking($this->user, $booking, 250);

        $lotA->refresh();
        $lotB->refresh();

        $this->assertEquals(0, $lotA->remaining_points);   // habis
        $this->assertEquals(150, $lotB->remaining_points); // 300 - 150

        // Harus ada 2 alokasi
        $allocationCount = LoyaltyPointAllocation::count();
        $this->assertEquals(2, $allocationCount);
    }

    // -------------------------------------------------------------------------
    // redeemForBooking — idempotency
    // -------------------------------------------------------------------------

    public function test_redeem_idempotency_tidak_ganda(): void
    {
        $this->seedPoints(500);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $this->service->redeemForBooking($this->user, $booking, 200);
        // Panggil kedua kali — harus diabaikan (idempotency key sama)
        $this->service->redeemForBooking($this->user, $booking, 200);

        $count = LoyaltyTransaction::where('user_id', $this->user->id)
            ->where('type', LoyaltyTransactionType::Redeem->value)
            ->count();

        $this->assertEquals(1, $count);
        $this->assertEquals(300, $this->service->getBalance($this->user));
    }

    // -------------------------------------------------------------------------
    // reverseRedemptionForBooking
    // -------------------------------------------------------------------------

    public function test_reversal_mengembalikan_poin_ke_saldo(): void
    {
        $lot = $this->seedPoints(500);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $this->service->redeemForBooking($this->user, $booking, 200);
        $this->assertEquals(300, $this->service->getBalance($this->user));

        $this->service->reverseRedemptionForBooking($booking);

        $this->assertEquals(500, $this->service->getBalance($this->user));
    }

    public function test_reversal_mengembalikan_remaining_points_ke_lot(): void
    {
        $lot = $this->seedPoints(500);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $this->service->redeemForBooking($this->user, $booking, 200);

        $lot->refresh();
        $this->assertEquals(300, $lot->remaining_points); // 500 - 200

        $this->service->reverseRedemptionForBooking($booking);

        $lot->refresh();
        $this->assertEquals(500, $lot->remaining_points); // dikembalikan penuh
    }

    public function test_reversal_membuat_transaksi_bertipe_reversal(): void
    {
        $this->seedPoints(500);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $this->service->redeemForBooking($this->user, $booking, 200);
        $this->service->reverseRedemptionForBooking($booking);

        $reversalTx = LoyaltyTransaction::where('user_id', $this->user->id)
            ->where('type', LoyaltyTransactionType::Reversal->value)
            ->where('booking_id', $booking->id)
            ->first();

        $this->assertNotNull($reversalTx);
        $this->assertEquals(200, $reversalTx->points);
    }

    public function test_reversal_idempotency_tidak_ganda(): void
    {
        $this->seedPoints(500);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $this->service->redeemForBooking($this->user, $booking, 200);

        $this->service->reverseRedemptionForBooking($booking);
        $this->service->reverseRedemptionForBooking($booking); // duplikat

        $count = LoyaltyTransaction::where('type', LoyaltyTransactionType::Reversal->value)->count();
        $this->assertEquals(1, $count);
        $this->assertEquals(500, $this->service->getBalance($this->user));
    }

    public function test_reversal_diabaikan_jika_tidak_ada_redeem(): void
    {
        // Booking yang tidak pernah diredeem
        $booking = $this->makeBooking(['status' => BookingStatus::PendingPayment]);

        $this->service->reverseRedemptionForBooking($booking); // harus tidak error

        $this->assertEquals(
            0,
            LoyaltyTransaction::where('type', LoyaltyTransactionType::Reversal->value)->count()
        );
    }

    // -------------------------------------------------------------------------
    // expirePointsForUser
    // -------------------------------------------------------------------------

    public function test_expire_menghapus_lot_yang_kadaluarsa(): void
    {
        $lot = LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => 500,
            'balance_after' => 500,
            'remaining_points' => 500,
            'description' => 'Poin kadaluarsa',
            'expires_at' => now()->subDay(),
            'idempotency_key' => 'test:expire:single',
        ]);

        $this->user->update(['loyalty_balance_cache' => 500]);

        $this->service->expirePointsForUser($this->user);

        $lot->refresh();
        $this->assertEquals(0, $lot->remaining_points);
        $this->assertEquals(0, $this->service->getBalance($this->user));
    }

    public function test_expire_tidak_menyentuh_lot_yang_belum_kadaluarsa(): void
    {
        $lotValid = LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => 300,
            'balance_after' => 300,
            'remaining_points' => 300,
            'description' => 'Poin masih valid',
            'expires_at' => now()->addMonths(6),
            'idempotency_key' => 'test:expire:valid-lot',
        ]);

        $lotExpired = LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => 200,
            'balance_after' => 500,
            'remaining_points' => 200,
            'description' => 'Poin kadaluarsa',
            'expires_at' => now()->subDay(),
            'idempotency_key' => 'test:expire:expired-lot',
        ]);

        $this->user->update(['loyalty_balance_cache' => 500]);

        $this->service->expirePointsForUser($this->user);

        $lotValid->refresh();
        $lotExpired->refresh();

        $this->assertEquals(300, $lotValid->remaining_points); // tidak tersentuh
        $this->assertEquals(0, $lotExpired->remaining_points);  // dikosongkan
    }

    public function test_expire_idempotency_tidak_ganda(): void
    {
        $lot = LoyaltyTransaction::create([
            'user_id' => $this->user->id,
            'type' => LoyaltyTransactionType::Earn->value,
            'points' => 500,
            'balance_after' => 500,
            'remaining_points' => 500,
            'description' => 'Poin kadaluarsa',
            'expires_at' => now()->subDay(),
            'idempotency_key' => 'test:expire:idempotency',
        ]);

        $this->user->update(['loyalty_balance_cache' => 500]);

        $this->service->expirePointsForUser($this->user);
        $this->service->expirePointsForUser($this->user); // duplikat

        $expireCount = LoyaltyTransaction::where('type', LoyaltyTransactionType::Expire->value)->count();
        $this->assertEquals(1, $expireCount);
    }

    // -------------------------------------------------------------------------
    // adjustPoints
    // -------------------------------------------------------------------------

    public function test_adjust_positif_menambah_saldo(): void
    {
        $admin = Admin::factory()->create();

        $this->service->adjustPoints($this->user, 250, 'Kompensasi delay', $admin);

        $this->assertEquals(250, $this->service->getBalance($this->user));
    }

    public function test_adjust_negatif_mengurangi_saldo(): void
    {
        $this->seedPoints(500);

        $admin = Admin::factory()->create();

        $this->service->adjustPoints($this->user, -100, 'Koreksi kesalahan', $admin);

        $this->assertEquals(400, $this->service->getBalance($this->user));
    }

    public function test_adjust_mencatat_created_by_admin_id(): void
    {
        $admin = Admin::factory()->create();

        $this->service->adjustPoints($this->user, 100, 'Test admin', $admin);

        $tx = LoyaltyTransaction::where('type', LoyaltyTransactionType::Adjustment->value)->first();
        $this->assertEquals($admin->id, $tx->created_by_admin_id);
    }

    public function test_adjust_positif_memberi_expiry_18_bulan(): void
    {
        $admin = Admin::factory()->create();

        $this->service->adjustPoints($this->user, 100, 'Bonus', $admin);

        $tx = LoyaltyTransaction::where('type', LoyaltyTransactionType::Adjustment->value)->first();
        $this->assertNotNull($tx->expires_at);
        $this->assertTrue($tx->expires_at->greaterThan(now()->addMonths(17)));
    }

    public function test_adjust_negatif_tidak_memberi_expiry(): void
    {
        $this->seedPoints(500);

        $admin = Admin::factory()->create();
        $this->service->adjustPoints($this->user, -100, 'Koreksi', $admin);

        $tx = LoyaltyTransaction::where('type', LoyaltyTransactionType::Adjustment->value)->first();
        $this->assertNull($tx->expires_at);
    }

    // -------------------------------------------------------------------------
    // balance_cache konsistensi
    // -------------------------------------------------------------------------

    public function test_balance_cache_diupdate_setelah_earn(): void
    {
        // Pastikan cache dimulai dari 0
        $this->user->update(['loyalty_balance_cache' => 0]);

        $booking = $this->makeBooking(['eligible_loyalty_amount' => 500_000]);
        $this->service->awardForCompletedBooking($booking);

        // Fresh query untuk bypass instance cache
        $fresh = $this->user->fresh();
        $this->assertEquals(500, $fresh->loyalty_balance_cache);
    }

    public function test_balance_cache_diupdate_setelah_redeem(): void
    {
        $this->seedPoints(500);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $this->service->redeemForBooking($this->user, $booking, 200);

        $fresh = $this->user->fresh();
        $this->assertEquals(300, $fresh->loyalty_balance_cache);
    }

    public function test_balance_cache_diupdate_setelah_reversal(): void
    {
        $this->seedPoints(500);

        $booking = $this->makeBooking([
            'subtotal' => 1_000_000,
            'status' => BookingStatus::PendingPayment,
        ]);

        $this->service->redeemForBooking($this->user, $booking, 200);
        $this->service->reverseRedemptionForBooking($booking);

        $fresh = $this->user->fresh();
        $this->assertEquals(500, $fresh->loyalty_balance_cache);
    }
}
