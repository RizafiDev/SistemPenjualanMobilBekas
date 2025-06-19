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
        Schema::create('varians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobil_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->string('kode')->nullable(); // Kode varian seperti "G", "V", dll
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_otr', 15, 2)->nullable(); // Harga baru saat pertama kali
            
            // Spesifikasi Mesin
            $table->enum('tipe_mesin', ['bensin', 'diesel', 'hybrid', 'listrik', 'lpg', 'cng']);
            $table->integer('kapasitas_mesin_cc')->nullable();
            $table->integer('silinder')->nullable();
            $table->enum('transmisi', ['manual', 'automatic', 'cvt', 'amt', 'dct']);
            $table->integer('jumlah_gigi')->nullable();
            $table->integer('daya_hp')->nullable();
            $table->integer('torsi_nm')->nullable();
            $table->string('jenis_bahan_bakar');
            $table->decimal('konsumsi_bahan_bakar_kota', 5, 2)->nullable(); // km/liter
            $table->decimal('konsumsi_bahan_bakar_jalan', 5, 2)->nullable();
            
            // Dimensi
            $table->integer('panjang_mm')->nullable();
            $table->integer('lebar_mm')->nullable();
            $table->integer('tinggi_mm')->nullable();
            $table->integer('jarak_sumbu_roda_mm')->nullable();
            $table->integer('ground_clearance_mm')->nullable();
            $table->integer('berat_kosong_kg')->nullable();
            $table->integer('berat_isi_kg')->nullable();
            $table->integer('kapasitas_bagasi_l')->nullable();
            $table->integer('kapasitas_tangki_l')->nullable();
            
            // Performa
            $table->decimal('akselerasi_0_100_kmh', 5, 2)->nullable(); // detik
            $table->integer('kecepatan_maksimal_kmh')->nullable();
            
            // Fitur Keamanan (JSON)
            $table->json('fitur_keamanan')->nullable();
            
            // Fitur Kenyamanan (JSON)
            $table->json('fitur_kenyamanan')->nullable();
            
            // Fitur Hiburan
            $table->json('fitur_hiburan')->nullable();
            
            $table->boolean('aktif')->default(true);
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['mobil_id', 'aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('varians');
    }
};
