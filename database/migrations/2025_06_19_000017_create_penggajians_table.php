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
        Schema::create('penggajians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->string('periode'); // Format: YYYY-MM
            $table->date('tanggal_gaji');

            // Komponen Gaji
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('tunjangan', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('lembur', 15, 2)->default(0);
            $table->decimal('insentif', 15, 2)->default(0);

            // Potongan
            $table->decimal('potongan_terlambat', 15, 2)->default(0);
            $table->decimal('potongan_absensi', 15, 2)->default(0);
            $table->decimal('potongan_lainnya', 15, 2)->default(0);

            // Total
            $table->decimal('total_gaji', 15, 2);
            $table->decimal('total_potongan', 15, 2);
            $table->decimal('gaji_bersih', 15, 2);

            // Status
            $table->enum('status', ['draft', 'dibayar', 'batal'])->default('draft');
            $table->text('catatan')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['karyawan_id', 'periode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggajians');
    }
};
