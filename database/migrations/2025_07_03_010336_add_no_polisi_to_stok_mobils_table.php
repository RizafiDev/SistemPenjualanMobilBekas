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
        Schema::table('stok_mobils', function (Blueprint $table) {
            $table->string('no_polisi', 15)->nullable()->after('no_mesin');
            $table->index('no_polisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_mobils', function (Blueprint $table) {
            $table->dropIndex(['no_polisi']);
            $table->dropColumn('no_polisi');
        });
    }
};