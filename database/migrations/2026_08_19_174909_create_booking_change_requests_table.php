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
        Schema::create('booking_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'reschedule', 'room_change', 'guest_update'
            $table->json('original_data');
            $table->json('requested_data');
            $table->bigInteger('price_difference')->default(0); // positif = tamu perlu bayar tambahan, negatif = tamu dapat refund
            $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected', 'cancelled'
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_change_requests');
    }
};
