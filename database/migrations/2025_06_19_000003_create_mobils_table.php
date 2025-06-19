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
        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->foreignId('merek_id')->constrained()->onDelete('restrict');
            $table->foreignId('kategori_id')->constrained()->onDelete('restrict');
            $table->year('tahun_mulai');
            $table->year('tahun_akhir')->nullable();
            $table->integer('kapasitas_penumpang');
            $table->enum('tipe_bodi', ['sedan', 'hatchback', 'suv', 'mpv', 'pickup', 'coupe', 'convertible', 'wagon']);
            $table->enum('status', ['aktif', 'dihentikan', 'akan_datang'])->default('aktif');
            $table->text('deskripsi')->nullable();
            $table->text('fitur_unggulan')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['merek_id', 'tahun_mulai']);
            $table->index(['kategori_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobils');
    }
};
