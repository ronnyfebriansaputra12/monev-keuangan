<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            $table->unique(
                ['sub_komponen_id', 'mak_id', 'tahun_anggaran', 'urutan'],
                'uq_coa_items_context_urutan'
            );
        });
    }

    public function down(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            $table->dropUnique('uq_coa_items_context_urutan');
        });
    }
};
