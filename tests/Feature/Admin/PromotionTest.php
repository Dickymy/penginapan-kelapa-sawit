<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionTest extends TestCase
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

    public function test_promotions_index_accessible_by_admin(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.promotions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.promotions.index');
    }

    public function test_promotions_create_page_loads_with_defaults(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.promotions.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.promotions.create');
        $response->assertSee('Tambah Promo');
        $response->assertSee('Simpan Promo');
    }

    public function test_admin_can_create_percentage_promotion_successfully(): void
    {
        $data = [
            'code' => 'diskonhemat',
            'name' => 'Diskon Hemat 15%',
            'description' => 'Promo menginap hemat 15%',
            'type' => 'percentage',
            'value' => 15,
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'minimum_booking_amount' => 100000,
            'maximum_discount' => 50000,
            'usage_quota' => 50,
            'max_usage_per_user' => 1,
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.promotions.store'), $data);

        $response->assertRedirect(route('admin.promotions.index'));
        $response->assertSessionHas('success', 'Promo berhasil dibuat.');

        $this->assertDatabaseHas('promotions', [
            'code' => 'DISKONHEMAT',
            'name' => 'Diskon Hemat 15%',
            'type' => 'percentage',
            'value' => 15,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_fixed_amount_promotion_successfully(): void
    {
        $data = [
            'code' => 'potongan20k',
            'name' => 'Potongan 20 Ribu',
            'description' => 'Potongan tetap Rp20.000',
            'type' => 'fixed',
            'value' => 20000,
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDays(14)->format('Y-m-d\TH:i'),
            'minimum_booking_amount' => 150000,
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.promotions.store'), $data);

        $response->assertRedirect(route('admin.promotions.index'));
        $response->assertSessionHas('success', 'Promo berhasil dibuat.');

        $this->assertDatabaseHas('promotions', [
            'code' => 'POTONGAN20K',
            'type' => 'fixed',
            'value' => 20000,
            'is_active' => true,
        ]);
    }

    public function test_percentage_promo_cannot_exceed_100_percent(): void
    {
        $data = [
            'code' => 'diskoninvalid',
            'name' => 'Diskon 120%',
            'type' => 'percentage',
            'value' => 120,
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.promotions.store'), $data);

        $response->assertSessionHasErrors('value');
        $this->assertDatabaseMissing('promotions', [
            'code' => 'DISKONINVALID',
        ]);
    }

    public function test_ends_at_must_be_after_starts_at(): void
    {
        $data = [
            'code' => 'diskondate',
            'name' => 'Diskon Salah Tanggal',
            'type' => 'percentage',
            'value' => 10,
            'starts_at' => now()->addDays(5)->format('Y-m-d\TH:i'),
            'ends_at' => now()->format('Y-m-d\TH:i'),
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.promotions.store'), $data);

        $response->assertSessionHasErrors('ends_at');
    }

    public function test_admin_can_update_promotion(): void
    {
        $promo = Promotion::create([
            'code' => 'PROMOAWAL',
            'name' => 'Promo Awal',
            'type' => 'fixed',
            'value' => 10000,
            'starts_at' => now(),
            'ends_at' => now()->addDays(10),
            'minimum_booking_amount' => 50000,
            'is_active' => true,
        ]);

        $updateData = [
            'code' => 'PROMOBARU',
            'name' => 'Promo Baru Diubah',
            'type' => 'fixed',
            'value' => 25000,
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDays(15)->format('Y-m-d\TH:i'),
            'minimum_booking_amount' => 100000,
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.promotions.update', $promo), $updateData);

        $response->assertRedirect(route('admin.promotions.index'));
        $response->assertSessionHas('success', 'Promo berhasil diperbarui.');

        $this->assertDatabaseHas('promotions', [
            'id' => $promo->id,
            'code' => 'PROMOBARU',
            'name' => 'Promo Baru Diubah',
            'value' => 25000,
        ]);
    }

    public function test_admin_can_create_hidden_promotion_with_show_in_listing_false(): void
    {
        $data = [
            'code' => 'KODERAHASIA',
            'name' => 'Voucher Rahasia VIP',
            'type' => 'percentage',
            'value' => 20,
            'starts_at' => now()->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDays(30)->format('Y-m-d\TH:i'),
            'is_active' => '1',
            'show_in_listing' => '0',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.promotions.store'), $data);

        $response->assertRedirect(route('admin.promotions.index'));
        $this->assertDatabaseHas('promotions', [
            'code' => 'KODERAHASIA',
            'show_in_listing' => false,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_toggle_checkout_listing_setting(): void
    {
        // Toggle off
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.promotions.toggle-checkout-listing'), [
                'show_promo_listing' => '0',
            ]);

        $response->assertRedirect(route('admin.promotions.index'));
        $this->assertFalse((bool) \App\Models\Setting::get('booking', 'show_promo_listing'));

        // Toggle back on
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.promotions.toggle-checkout-listing'), [
                'show_promo_listing' => '1',
            ]);

        $response->assertRedirect(route('admin.promotions.index'));
        $this->assertTrue((bool) \App\Models\Setting::get('booking', 'show_promo_listing'));
    }
}
