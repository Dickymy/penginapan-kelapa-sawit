<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_submit_review_for_completed_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => BookingStatus::Completed,
        ]);

        $response = $this->actingAs($user)
            ->post(route('member.reviews.store'), [
                'booking_id' => $booking->id,
                'rating' => 5,
                'title' => 'Great Stay',
                'comment' => 'It was a wonderful experience.',
            ]);

        $response->assertRedirect(route('member.dashboard'));
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'rating' => 5,
            'is_published' => false,
        ]);
    }

    public function test_member_cannot_review_pending_booking()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => BookingStatus::Confirmed,
        ]);

        $response = $this->actingAs($user)
            ->get(route('member.reviews.create', $booking));

        $response->assertStatus(403);
    }

    public function test_admin_can_publish_review()
    {
        $admin = \App\Models\Admin::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $review = Review::factory()->create(['is_published' => false]);

        $response = $this->actingAs($admin, 'admin')
            ->patch(route('admin.reviews.publish', $review));

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_published' => true,
        ]);
    }

    public function test_admin_can_reply_to_review()
    {
        $admin = \App\Models\Admin::create([
            'name' => 'Admin2',
            'email' => 'admin2@test.com',
            'password' => bcrypt('password'),
        ]);
        $review = Review::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.reviews.reply', $review), [
                'admin_reply' => 'Terima kasih atas kunjungannya.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'admin_reply' => 'Terima kasih atas kunjungannya.',
        ]);
    }
}
