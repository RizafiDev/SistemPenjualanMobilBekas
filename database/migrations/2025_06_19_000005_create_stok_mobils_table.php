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
        Schema::create('stok_mobils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobil_id')->constrained()->onDelete('restrict');
            $table->foreignId('varian_id')->nullable()->constrained()->onDelete('set null');
            $table->string('warna');
            $table->string('no_rangka')->unique();
            $table->string('no_mesin')->unique();
            $table->year('tahun');
            $table->integer('kilometer');
            $table->enum('kondisi', ['sangat_baik', 'baik', 'cukup', 'butuh_perbaikan', 'project']);
            $table->enum('status', ['tersedia', 'terjual', 'booking', 'indent', 'dalam_perbaikan']);
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->decimal('laba_kotor', 15, 2)->virtualAs('harga_jual - harga_beli');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('catatan')->nullable();
            $table->json('kelengkapan')->nullable();
            $table->json('riwayat_perbaikan')->nullable();
            $table->json('dokumen')->nullable(); // BPKB, STNK, dll
            $table->softDeletes();
            $table->timestamps();

            $table->index(['mobil_id', 'status']);
            $table->index(['tahun', 'kondisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_mobils');
    }
};
