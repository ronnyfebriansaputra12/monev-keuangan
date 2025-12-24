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
        Schema::table('realisasis', function (Blueprint $table) {
            // Menambahkan field finansial yang mungkin tertinggal
            $table->decimal('pph_final', 15, 2)->default(0)->after('pph23');

            // Menambahkan identitas kuitansi fisik
            $table->string('nomor_kuitansi')->nullable()->after('tgl_kuitansi');

            // Menambahkan SoftDeletes agar data aman jika terhapus tidak sengaja
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasis', function (Blueprint $table) {
            $table->dropColumn(['pph_final', 'nomor_kuitansi']);
            $table->dropSoftDeletes();
        });
    }
};
