<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->string('thumb_path', 255)->nullable()->after('path');
            $table->string('medium_path', 255)->nullable()->after('thumb_path');
            $table->string('large_path', 255)->nullable()->after('medium_path');
        });

        Schema::table('room_images', function (Blueprint $table) {
            $table->string('thumb_path', 255)->nullable()->after('path');
            $table->string('medium_path', 255)->nullable()->after('thumb_path');
            $table->string('large_path', 255)->nullable()->after('medium_path');
        });
    }

    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn(['thumb_path', 'medium_path', 'large_path']);
        });

        Schema::table('room_images', function (Blueprint $table) {
            $table->dropColumn(['thumb_path', 'medium_path', 'large_path']);
        });
    }
};
