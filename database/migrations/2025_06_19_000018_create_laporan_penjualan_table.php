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
        Schema::create('laporan_penjualans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->integer('minggu');
            
            // Statistik Penjualan
            $table->integer('total_penjualan')->default(0);
            $table->decimal('total_nilai_penjualan', 15, 2)->default(0);
            $table->decimal('rata_rata_penjualan', 15, 2)->default(0);
            $table->integer('penjualan_tunai')->default(0);
            $table->integer('penjualan_kredit')->default(0);
            
            // Statistik Kendaraan
            $table->json('top_merek')->nullable();
            $table->json('top_model')->nullable();
            $table->json('top_kategori')->nullable();
            
            // Statistik Karyawan
            $table->json('top_sales')->nullable();
            
            $table->timestamps();
            
            $table->index(['tahun', 'bulan']);
            $table->index(['tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_penjualans');
    }
};
