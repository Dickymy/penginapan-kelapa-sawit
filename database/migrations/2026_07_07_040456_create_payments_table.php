<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->restrictOnDelete();
            $table->string('provider', 30)->default('midtrans');
            $table->string('provider_order_id', 100);
            $table->string('transaction_id', 191)->nullable();
            $table->smallInteger('attempt_no')->unsigned()->default(1);
            $table->text('snap_token')->nullable();
            $table->string('payment_type', 100)->nullable();
            $table->bigInteger('gross_amount')->unsigned();
            $table->string('status', 30)->default('unpaid');
            $table->string('provider_transaction_status', 50)->nullable();
            $table->string('fraud_status', 50)->nullable();
            $table->timestamp('provider_transaction_time')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('last_status_checked_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_order_id']);
            $table->unique(['booking_id', 'attempt_no']);
            $table->index(['booking_id', 'status']);
            $table->index('transaction_id');
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
