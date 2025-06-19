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
        Schema::create('janji_temus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('stok_mobil_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->onDelete('set null'); // Sales
            $table->datetime('waktu_mulai');
            $table->datetime('waktu_selesai');
            $table->enum('jenis', ['test_drive', 'konsultasi', 'negosiasi', 'lainnya']);
            $table->text('tujuan')->nullable();
            $table->enum('status', ['terjadwal', 'selesai', 'batal', 'tidak_hadir'])->default('terjadwal');
            $table->text('catatan')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('metode', 20)->default('offline'); // offline/online
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['pelanggan_id', 'status']);
            $table->index(['waktu_mulai', 'waktu_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('janji_temus');
    }
};
