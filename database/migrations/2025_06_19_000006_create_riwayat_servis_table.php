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
        Schema::create('riwayat_servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_mobil_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_servis');
            $table->string('jenis_servis');
            $table->string('tempat_servis');
            $table->text('deskripsi')->nullable();
            $table->decimal('biaya', 15, 2);
            $table->integer('kilometer_servis');
            $table->json('foto_bukti')->nullable();
            $table->json('sparepart')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['stok_mobil_id', 'tanggal_servis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_servis');
    }
};
