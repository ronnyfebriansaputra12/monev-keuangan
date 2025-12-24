<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            $table->decimal('realisasi_total', 18, 2)->default(0)->after('pagu_item');
            $table->decimal('sisa_realisasi', 18, 2)->default(0)->after('realisasi_total');
        });

        // Inisialisasi sisa = pagu_item (kalau ada) atau jumlah
        DB::statement("
            UPDATE coa_items
            SET sisa_realisasi = COALESCE(pagu_item, jumlah, 0) - COALESCE(realisasi_total, 0)
        ");
    }

    public function down(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            $table->dropColumn(['realisasi_total', 'sisa_realisasi']);
        });
    }
};
