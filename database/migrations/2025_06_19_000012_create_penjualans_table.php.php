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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur')->unique();
            $table->foreignId('stok_mobil_id')->constrained()->onDelete('restrict');
            $table->foreignId('pelanggan_id')->constrained()->onDelete('restrict');
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->onDelete('set null'); // Sales

            // Detail Harga
            $table->decimal('harga_jual', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('biaya_tambahan', 15, 2)->default(0);
            $table->decimal('total', 15, 2);

            // Metode Pembayaran
            $table->enum('metode_pembayaran', ['tunai', 'kredit', 'leasing', 'trade_in']);
            $table->string('leasing_bank')->nullable();
            $table->integer('tenor_bulan')->nullable();
            $table->decimal('uang_muka', 15, 2)->nullable();
            $table->decimal('cicilan_bulanan', 15, 2)->nullable();

            // Trade-in (jika ada)
            $table->json('trade_in')->nullable(); // Data mobil trade-in

            // Dokumen
            $table->json('dokumen')->nullable(); // KTP, KK, dll

            $table->date('tanggal_penjualan');
            $table->enum('status', ['draft', 'booking', 'lunas', 'kredit', 'batal'])->default('draft');
            $table->text('catatan')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['stok_mobil_id', 'status']);
            $table->index(['tanggal_penjualan', 'metode_pembayaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
