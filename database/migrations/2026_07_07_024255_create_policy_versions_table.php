<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_versions', function (Blueprint $table) {
            $table->id();
            $table->string('policy_key', 100)->default('guest_policy');
            $table->string('version', 50);
            $table->string('title', 191);
            $table->longText('content');
            $table->boolean('is_current')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['policy_key', 'version'], 'policy_key_version_unique');
            $table->index(['policy_key', 'is_current']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_versions');
    }
};
