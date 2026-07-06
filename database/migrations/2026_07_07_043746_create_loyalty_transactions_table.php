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
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->string('type', 30);
            $table->bigInteger('points'); // signed: positive=in, negative=out
            $table->bigInteger('balance_after'); // signed
            $table->unsignedBigInteger('remaining_points')->default(0);
            $table->string('description', 255);
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('source_transaction_id')->nullable()->constrained('loyalty_transactions')->nullOnDelete();
            $table->string('idempotency_key', 150)->unique();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'expires_at', 'remaining_points']);
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
