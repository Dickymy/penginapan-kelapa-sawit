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
        Schema::create('loyalty_point_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debit_transaction_id')->constrained('loyalty_transactions')->restrictOnDelete();
            $table->foreignId('credit_transaction_id')->constrained('loyalty_transactions')->restrictOnDelete();
            $table->unsignedBigInteger('points');
            $table->timestamps();

            $table->unique(['debit_transaction_id', 'credit_transaction_id'], 'lpa_debit_credit_unique');
            $table->index('credit_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_allocations');
    }
};
