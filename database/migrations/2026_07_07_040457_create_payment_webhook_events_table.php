<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 30)->default('midtrans');
            $table->char('deduplication_key', 64);
            $table->string('provider_order_id', 100)->nullable();
            $table->string('transaction_id', 191)->nullable();
            $table->string('event_status', 50)->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->boolean('amount_valid')->default(false);
            $table->string('processing_status', 30)->default('received');
            $table->json('payload');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'deduplication_key']);
            $table->index(['provider_order_id', 'created_at']);
            $table->index(['processing_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');
    }
};
