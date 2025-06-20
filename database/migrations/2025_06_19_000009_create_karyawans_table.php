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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('password')->password();
            $table->string('no_telepon')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir');
            $table->string('nik')->unique();
            $table->string('jabatan');
            $table->string('departemen');
            $table->decimal('gaji_pokok', 15, 2)->nullable();
            $table->date('tanggal_masuk');
            $table->enum('status', ['tetap', 'kontrak', 'magang'])->default('tetap');
            $table->enum('aktif', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('alamat')->nullable();
            $table->string('foto')->nullable();
            $table->json('data_tambahan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
