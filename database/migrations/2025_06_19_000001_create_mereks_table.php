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
        Schema::create('mereks', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('negara_asal')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('tahun_berdiri')->nullable();
            $table->boolean('aktif')->default(true);
            $table->softDeletes(); // Untuk arsip data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mereks');
    }
};
