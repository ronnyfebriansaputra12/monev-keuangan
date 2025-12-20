<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            // drop legacy columns (kalau memang masih ada)
            if (Schema::hasColumn('coa_items', 'kode_coa_item')) {
                $table->dropColumn('kode_coa_item');
            }
            if (Schema::hasColumn('coa_items', 'nama_item')) {
                $table->dropColumn('nama_item');
            }
            if (Schema::hasColumn('coa_items', 'pagu_item')) {
                $table->dropColumn('pagu_item');
            }
        });
    }

    public function down(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            // kalau butuh rollback
            if (!Schema::hasColumn('coa_items', 'kode_coa_item')) {
                $table->string('kode_coa_item', 50)->nullable();
            }
            if (!Schema::hasColumn('coa_items', 'nama_item')) {
                $table->string('nama_item', 255)->nullable();
            }
            if (!Schema::hasColumn('coa_items', 'pagu_item')) {
                $table->decimal('pagu_item', 18, 2)->default(0);
            }
        });
    }
};
