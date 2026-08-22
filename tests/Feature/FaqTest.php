<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Admin;
use App\Models\Faq;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin_test@test.com',
            'username' => 'admin_test',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
    }

    public function test_public_can_view_active_faqs_only()
    {
        $activeFaq = Faq::factory()->create(['is_active' => true, 'sort_order' => 1]);
        $inactiveFaq = Faq::factory()->create(['is_active' => false, 'sort_order' => 2]);

        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertSee($activeFaq->question);
        $response->assertDontSee($inactiveFaq->question);
    }

    public function test_admin_can_view_faqs_index()
    {
        Faq::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.faqs.index'));

        $response->assertStatus(200);
        $response->assertViewHas('faqs');
    }

    public function test_admin_can_store_faq()
    {
        $data = [
            'question' => 'Tanya apa saja?',
            'answer' => 'Terserah Anda.',
            'category' => 'Umum',
            'sort_order' => 5,
            'is_active' => 1
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.faqs.store'), $data);

        $response->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseHas('faqs', ['question' => 'Tanya apa saja?']);
    }

    public function test_admin_can_update_faq()
    {
        $faq = Faq::factory()->create();

        $data = [
            'question' => 'Update Pertanyaan',
            'answer' => 'Update Jawaban',
            'category' => 'Umum',
            'sort_order' => 1,
            'is_active' => 0
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.faqs.update', $faq), $data);

        $response->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'question' => 'Update Pertanyaan',
            'is_active' => 0
        ]);
    }

    public function test_admin_can_delete_faq()
    {
        $faq = Faq::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.faqs.destroy', $faq));

        $response->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }
}
