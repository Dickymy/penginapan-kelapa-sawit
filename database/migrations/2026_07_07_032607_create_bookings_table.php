<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 40)->unique();
            $table->string('invoice_number', 40)->nullable()->unique();
            $table->string('idempotency_key', 100)->nullable()->unique();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->restrictOnDelete();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->string('source', 30)->default('website');
            $table->string('status', 30)->default('pending_payment');
            $table->string('payment_status', 30)->default('unpaid');

            $table->date('check_in');
            $table->date('check_out');
            $table->smallInteger('nights')->unsigned();
            $table->smallInteger('guest_count')->unsigned()->default(1);

            $table->string('guest_name', 150);
            $table->string('guest_email', 191)->nullable();
            $table->string('guest_whatsapp', 32);
            $table->string('arrival_estimate', 100)->nullable();
            $table->text('special_request')->nullable();

            // Snapshots
            $table->string('room_type_name_snapshot', 120);
            $table->string('room_name_snapshot', 120);
            $table->bigInteger('price_per_night_snapshot')->unsigned();
            $table->bigInteger('subtotal')->unsigned();

            // Promo (future — FK nullable, table created as placeholder)
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->string('promotion_code_snapshot', 100)->nullable();
            $table->bigInteger('promotion_discount')->unsigned()->default(0);

            // Points (future)
            $table->bigInteger('points_redeemed')->unsigned()->default(0);
            $table->bigInteger('points_discount')->unsigned()->default(0);

            // Total
            $table->bigInteger('total_amount')->unsigned();
            $table->char('currency', 3)->default('IDR');
            $table->bigInteger('eligible_loyalty_amount')->unsigned()->default(0);

            // Payment
            $table->timestamp('payment_expires_at')->nullable();

            // Policy
            $table->foreignId('policy_version_id')->nullable()->constrained('policy_versions')->nullOnDelete();
            $table->timestamp('policy_accepted_at')->nullable();

            // Guest access
            $table->char('guest_access_token_hash', 64)->nullable();

            // Claim
            $table->timestamp('claimed_at')->nullable();
            $table->string('claim_method', 50)->nullable();

            // Operations
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Cancellation
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason', 255)->nullable();
            $table->text('cancellation_notes')->nullable();
            $table->foreignId('cancelled_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();

            // Attention
            $table->boolean('needs_attention')->default(false);
            $table->string('attention_reason', 191)->nullable();
            $table->text('internal_notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['room_id', 'check_in', 'check_out', 'status'], 'idx_room_dates');
            $table->index(['status', 'payment_expires_at'], 'idx_expiry');
            $table->index(['user_id', 'status', 'check_in'], 'idx_user');
            $table->index(['source', 'created_at'], 'idx_source');
            $table->index(['payment_status', 'created_at'], 'idx_payment');
            $table->index(['needs_attention', 'created_at'], 'idx_attention');
            $table->index('guest_email', 'idx_guest_email');
            $table->index('guest_whatsapp', 'idx_guest_whatsapp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
