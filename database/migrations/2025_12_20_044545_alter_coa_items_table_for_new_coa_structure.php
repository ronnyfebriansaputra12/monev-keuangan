<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {

            // kolom baru sesuai struktur COA
            if (!Schema::hasColumn('coa_items', 'uraian')) {
                $table->string('uraian', 255)->after('mak_id');
            }

            if (!Schema::hasColumn('coa_items', 'volume')) {
                $table->integer('volume')->default(1)->after('uraian');
            }

            if (!Schema::hasColumn('coa_items', 'satuan')) {
                $table->string('satuan', 50)->nullable()->after('volume');
            }

            if (!Schema::hasColumn('coa_items', 'harga_satuan')) {
                $table->decimal('harga_satuan', 18, 2)->default(0)->after('satuan');
            }

            if (!Schema::hasColumn('coa_items', 'jumlah')) {
                $table->decimal('jumlah', 18, 2)->default(0)->after('harga_satuan');
            }

            // kalau kolom tahun_anggaran belum ada
            if (!Schema::hasColumn('coa_items', 'tahun_anggaran')) {
                $table->integer('tahun_anggaran')->after('jumlah');
            }

            /**
             * OPTIONAL: hapus kolom lama kalau memang sudah tidak dipakai
             * NOTE: kalau masih dipakai realisasi/pagu lama, jangan drop dulu.
             */
            // if (Schema::hasColumn('coa_items', 'kode_coa_item')) $table->dropColumn('kode_coa_item');
            // if (Schema::hasColumn('coa_items', 'nama_item')) $table->dropColumn('nama_item');
            // if (Schema::hasColumn('coa_items', 'pagu_item')) $table->dropColumn('pagu_item');
            // if (Schema::hasColumn('coa_items', 'sub_komponen_id')) $table->dropColumn('sub_komponen_id');
        });
    }

    public function down(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {

            // rollback kolom baru
            if (Schema::hasColumn('coa_items', 'jumlah')) $table->dropColumn('jumlah');
            if (Schema::hasColumn('coa_items', 'harga_satuan')) $table->dropColumn('harga_satuan');
            if (Schema::hasColumn('coa_items', 'satuan')) $table->dropColumn('satuan');
            if (Schema::hasColumn('coa_items', 'volume')) $table->dropColumn('volume');
            if (Schema::hasColumn('coa_items', 'uraian')) $table->dropColumn('uraian');

            // kolom tahun_anggaran jangan di drop kalau memang sebelumnya sudah ada
            // kalau tadi kita tambah baru, silakan drop:
            // if (Schema::hasColumn('coa_items', 'tahun_anggaran')) $table->dropColumn('tahun_anggaran');
        });
    }
};
