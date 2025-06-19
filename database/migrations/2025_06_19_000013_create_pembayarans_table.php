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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained()->onDelete('cascade');
            $table->string('no_kwitansi')->unique();
            $table->decimal('jumlah', 15, 2);
            $table->enum('jenis', ['dp', 'cicilan', 'pelunasan', 'tambahan']);
            $table->string('keterangan')->nullable();
            
            // Metode Pembayaran
            $table->enum('metode', ['tunai', 'transfer', 'debit', 'kredit', 'ewallet', 'cek']);
            $table->string('bank')->nullable();
            $table->string('no_referensi')->nullable();
            
            $table->date('tanggal_bayar');
            $table->string('bukti_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['penjualan_id', 'tanggal_bayar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
