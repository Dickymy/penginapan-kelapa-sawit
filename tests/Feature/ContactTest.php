<?php

namespace Tests\Feature;

use App\Mail\ContactAutoReplyMail;
use App\Models\Admin;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_contact_page()
    {
        $response = $this->get(route('contact.create'));
        $response->assertStatus(200);
        $response->assertSee('Hubungi Kami');
    }

    public function test_public_can_submit_contact_message()
    {
        Mail::fake();

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
            'subject' => 'Tanya Kamar',
            'message' => 'Halo, saya ingin bertanya tentang kamar deluxe.',
        ];

        $response = $this->post(route('contact.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'john@example.com',
            'subject' => 'Tanya Kamar',
            'is_read' => false,
        ]);

        Mail::assertQueued(ContactAutoReplyMail::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
    }

    public function test_admin_can_view_contact_messages()
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $message = ContactMessage::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.contact-messages.index'));

        $response->assertStatus(200);
        $response->assertSee($message->subject);
    }

    public function test_admin_can_view_contact_message_detail_and_marks_as_read()
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin2@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $message = ContactMessage::factory()->create(['is_read' => false]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.contact-messages.show', $message));

        $response->assertStatus(200);
        $response->assertSee($message->message);
        
        $this->assertTrue($message->fresh()->is_read);
    }

    public function test_admin_can_delete_contact_message()
    {
        $admin = Admin::create([
            'name' => 'Admin',
            'email' => 'admin3@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $message = ContactMessage::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.contact-messages.destroy', $message));

        $response->assertRedirect(route('admin.contact-messages.index'));
        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }
}
