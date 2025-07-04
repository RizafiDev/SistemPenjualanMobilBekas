<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name/description of the API key');
            $table->string('key', 64)->unique()->comment('The API key hash');
            $table->boolean('is_active')->default(true)->comment('Whether the API key is active');
            $table->json('permissions')->nullable()->comment('Permissions for this API key');
            $table->timestamp('last_used_at')->nullable()->comment('Last time this key was used');
            $table->timestamp('expires_at')->nullable()->comment('Expiration date of the API key');
            $table->timestamps();

            $table->index(['key', 'is_active']);
            $table->index(['expires_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
