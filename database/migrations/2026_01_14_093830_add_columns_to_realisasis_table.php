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
            // Menambahkan enum untuk jenis realisasi
            $table->enum('jenis_realisasi', ['GUP', 'LS'])->nullable()->after('sumber_anggaran');

            // Menambahkan boolean/tinyint untuk checkbox status sp2d
            $table->boolean('status_sp2d')->default(false)->after('status_berkas_line');
        });
    }

    public function down(): void
    {
        Schema::table('realisasis', function (Blueprint $table) {
            $table->dropColumn(['jenis_realisasi', 'status_sp2d']);
        });
    }
};
