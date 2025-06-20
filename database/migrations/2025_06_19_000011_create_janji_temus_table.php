<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('janji_temus', function (Blueprint $table) {
            $table->id();

            // Data pelanggan yang diinput langsung dari form web
            $table->string('nama_pelanggan');
            $table->string('email_pelanggan');
            $table->string('telepon_pelanggan');
            $table->text('alamat_pelanggan')->nullable();

            // Relasi ke mobil yang diminati (opsional)
            $table->foreignId('stok_mobil_id')->nullable()->constrained()->onDelete('set null');

            // Karyawan yang akan menangani (akan di-assign setelah request dibuat)
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->onDelete('set null');

            // Waktu yang diinginkan pelanggan
            $table->datetime('waktu_mulai');
            $table->datetime('waktu_selesai');

            // Jenis dan tujuan janji temu
            $table->enum('jenis', ['test_drive', 'konsultasi', 'negosiasi', 'survey_mobil', 'lainnya']);
            $table->text('tujuan')->nullable();
            $table->text('pesan_tambahan')->nullable(); // Pesan dari pelanggan

            // Status janji temu
            $table->enum('status', ['pending', 'dikonfirmasi', 'terjadwal', 'selesai', 'batal', 'tidak_hadir'])->default('pending');

            // Catatan internal untuk karyawan
            $table->text('catatan_internal')->nullable();

            // Lokasi dan metode
            $table->string('lokasi')->nullable();
            $table->enum('metode', ['offline', 'online'])->default('offline');

            // Preferensi waktu alternatif
            $table->json('waktu_alternatif')->nullable(); // Array waktu alternatif jika waktu utama tidak tersedia

            // Tracking
            $table->timestamp('tanggal_request')->useCurrent(); // Kapan request dibuat
            $table->timestamp('tanggal_konfirmasi')->nullable(); // Kapan dikonfirmasi staff
            $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('karyawans')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();

            // Indexes untuk performa
            $table->index(['email_pelanggan', 'status']);
            $table->index(['status', 'tanggal_request']);
            $table->index(['waktu_mulai', 'waktu_selesai']);
            $table->index(['karyawan_id', 'status']);
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