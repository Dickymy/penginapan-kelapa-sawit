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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->restrictOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->restrictOnDelete();
            $table->foreignId('requested_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('processed_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('reason', 255);
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('requested');
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->string('provider_refund_id', 191)->nullable();
            $table->json('provider_response')->nullable();
            $table->timestamps();
            $table->index(['booking_id', 'status']);
            $table->index('payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
