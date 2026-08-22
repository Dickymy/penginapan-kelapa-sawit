<?php

namespace Tests\Feature;

use App\Console\Commands\SendCheckinReminders;
use App\Console\Commands\SendPostCheckoutEmails;
use App\Events\BookingCancelled;
use App\Events\BookingCreated;
use App\Events\PaymentConfirmed;
use App\Listeners\SendBookingCancelledListener;
use App\Listeners\SendBookingConfirmationListener;
use App\Listeners\SendPaymentSuccessListener;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmationMail;
use App\Mail\CheckinReminderMail;
use App\Mail\PaymentSuccessMail;
use App\Mail\PostCheckoutMail;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\RoomType;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_service_dispatches_booking_created_event()
    {
        Event::fake([BookingCreated::class]);

        $roomType = RoomType::factory()->create();
        Room::factory()->create(['room_type_id' => $roomType->id]);

        $service = app(BookingService::class);
        $service->createGuestBooking([
            'room_type_id' => $roomType->id,
            'check_in' => today()->addDays(2)->toDateString(),
            'check_out' => today()->addDays(4)->toDateString(),
            'guest_name' => 'John Doe',
            'guest_email' => 'john@example.com',
            'guest_whatsapp' => '081234567890',
        ]);

        Event::assertDispatched(BookingCreated::class);
    }

    public function test_listener_sends_booking_confirmation_mail()
    {
        Mail::fake();

        $booking = Booking::factory()->create([
            'guest_email' => 'john@example.com',
            'confirmation_email_sent_at' => null,
        ]);

        $event = new BookingCreated($booking);
        $listener = new SendBookingConfirmationListener();
        $listener->handle($event);

        Mail::assertQueued(BookingConfirmationMail::class, function ($mail) use ($booking) {
            return $mail->hasTo('john@example.com') && $mail->booking->id === $booking->id;
        });

        $this->assertNotNull($booking->fresh()->confirmation_email_sent_at);
    }

    public function test_idempotency_prevents_duplicate_emails()
    {
        Mail::fake();

        $booking = Booking::factory()->create([
            'guest_email' => 'john@example.com',
            'confirmation_email_sent_at' => now(), // Already sent
        ]);

        $event = new BookingCreated($booking);
        $listener = new SendBookingConfirmationListener();
        $listener->handle($event);

        Mail::assertNothingSent();
    }

    public function test_payment_success_sends_email()
    {
        Mail::fake();

        $booking = Booking::factory()->create([
            'guest_email' => 'john@example.com',
            'payment_email_sent_at' => null,
        ]);
        $payment = Payment::factory()->create(['booking_id' => $booking->id]);

        $event = new PaymentConfirmed($booking, $payment);
        $listener = new SendPaymentSuccessListener();
        $listener->handle($event);

        Mail::assertQueued(PaymentSuccessMail::class);
        $this->assertNotNull($booking->fresh()->payment_email_sent_at);
    }

    public function test_booking_cancelled_sends_email()
    {
        Mail::fake();

        $booking = Booking::factory()->create([
            'guest_email' => 'john@example.com',
            'cancellation_email_sent_at' => null,
        ]);

        $event = new BookingCancelled($booking);
        $listener = new SendBookingCancelledListener();
        $listener->handle($event);

        Mail::assertQueued(BookingCancelledMail::class);
        $this->assertNotNull($booking->fresh()->cancellation_email_sent_at);
    }

    public function test_send_checkin_reminders_command()
    {
        Mail::fake();

        // Checkin tomorrow
        $booking1 = Booking::factory()->create([
            'status' => 'confirmed',
            'check_in' => today()->addDay()->toDateString(),
            'guest_email' => 'john1@example.com',
            'reminder_email_sent_at' => null,
        ]);

        // Checkin today (should not get reminder)
        $booking2 = Booking::factory()->create([
            'status' => 'confirmed',
            'check_in' => today()->toDateString(),
            'guest_email' => 'john2@example.com',
            'reminder_email_sent_at' => null,
        ]);

        // Checkin tomorrow but not confirmed
        $booking3 = Booking::factory()->create([
            'status' => 'pending_payment',
            'check_in' => today()->addDay()->toDateString(),
            'guest_email' => 'john3@example.com',
            'reminder_email_sent_at' => null,
        ]);

        $this->artisan('booking:send-checkin-reminders')->assertExitCode(0);

        Mail::assertQueued(CheckinReminderMail::class, 1);
        Mail::assertQueued(CheckinReminderMail::class, function ($mail) use ($booking1) {
            return $mail->hasTo('john1@example.com');
        });

        $this->assertNotNull($booking1->fresh()->reminder_email_sent_at);
    }

    public function test_send_post_checkout_emails_command()
    {
        Mail::fake();

        // Checked out yesterday
        $booking1 = Booking::factory()->create([
            'status' => 'checked_out',
            'check_out' => today()->subDay()->toDateString(),
            'guest_email' => 'jane1@example.com',
            'checkout_email_sent_at' => null,
        ]);

        // Checked out today (too early)
        $booking2 = Booking::factory()->create([
            'status' => 'checked_out',
            'check_out' => today()->toDateString(),
            'guest_email' => 'jane2@example.com',
            'checkout_email_sent_at' => null,
        ]);

        $this->artisan('booking:send-post-checkout-emails')->assertExitCode(0);

        Mail::assertQueued(PostCheckoutMail::class, 1);
        Mail::assertQueued(PostCheckoutMail::class, function ($mail) use ($booking1) {
            return $mail->hasTo('jane1@example.com');
        });

        $this->assertNotNull($booking1->fresh()->checkout_email_sent_at);
    }
}
