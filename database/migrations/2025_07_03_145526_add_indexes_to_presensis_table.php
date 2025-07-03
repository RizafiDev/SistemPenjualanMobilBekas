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
        Schema::table('presensis', function (Blueprint $table) {
            // Add indexes for better performance during export queries
            $table->index(['tanggal', 'karyawan_id'], 'idx_presensis_tanggal_karyawan');
            $table->index(['status', 'tanggal'], 'idx_presensis_status_tanggal');
            $table->index(['karyawan_id', 'tanggal'], 'idx_presensis_karyawan_tanggal');
            $table->index(['created_at'], 'idx_presensis_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            // Drop the indexes
            $table->dropIndex('idx_presensis_tanggal_karyawan');
            $table->dropIndex('idx_presensis_status_tanggal');
            $table->dropIndex('idx_presensis_karyawan_tanggal');
            $table->dropIndex('idx_presensis_created_at');
        });
    }
};
