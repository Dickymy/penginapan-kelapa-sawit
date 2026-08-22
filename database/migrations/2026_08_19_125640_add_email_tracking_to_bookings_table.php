<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('confirmation_email_sent_at')->nullable();
            $table->timestamp('payment_email_sent_at')->nullable();
            $table->timestamp('reminder_email_sent_at')->nullable();
            $table->timestamp('checkout_email_sent_at')->nullable();
            $table->timestamp('cancellation_email_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'confirmation_email_sent_at',
                'payment_email_sent_at',
                'reminder_email_sent_at',
                'checkout_email_sent_at',
                'cancellation_email_sent_at',
            ]);
        });
    }
};
