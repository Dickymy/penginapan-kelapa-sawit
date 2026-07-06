<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 150)->unique();
            $table->string('short_description', 255)->nullable();
            $table->text('description')->nullable();
            $table->smallInteger('capacity')->unsigned()->default(1);
            $table->smallInteger('bed_count')->unsigned()->default(1);
            $table->string('bed_type', 100)->nullable();
            $table->bigInteger('base_price')->unsigned()->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
