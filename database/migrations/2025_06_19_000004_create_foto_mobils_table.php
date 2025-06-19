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
        Schema::create('foto_mobils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobil_id')->constrained()->onDelete('cascade');
            $table->string('path_file');
            $table->enum('jenis_media', ['gambar', 'video', 'brosur']);
            $table->enum('jenis_gambar', ['eksterior', 'interior', 'fitur', 'thumbnail', 'galeri'])->nullable();
            $table->integer('urutan_tampil')->default(0);
            $table->string('teks_alternatif')->nullable();
            $table->string('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['mobil_id', 'jenis_media']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_mobils');
    }
};
