<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            // urutan untuk menjaga urutan seperti excel
            $table->unsignedInteger('urutan')->default(0)->after('id');

            // hirarki (sub COA) dalam 1 tabel
            $table->foreignId('parent_id')
                ->nullable()
                ->after('mak_id')
                ->constrained('coa_items')
                ->nullOnDelete();

            // 0 = induk, 1 = anak, 2 = cucu, dst
            $table->unsignedTinyInteger('level')->default(0)->after('parent_id');

            // index biar query cepat
            $table->index(['mak_id', 'tahun_anggaran']);
            $table->index(['parent_id', 'level']);
            $table->index(['mak_id', 'tahun_anggaran', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            $table->dropIndex(['mak_id', 'tahun_anggaran']);
            $table->dropIndex(['parent_id', 'level']);
            $table->dropIndex(['mak_id', 'tahun_anggaran', 'urutan']);

            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['urutan', 'level']);
        });
    }
};
