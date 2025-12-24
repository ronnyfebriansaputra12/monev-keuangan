<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('realisasi_headers', function (Blueprint $table) {
            $table->unsignedBigInteger('coa_item_id')->nullable()->after('id');
            $table->unsignedBigInteger('mak_id')->nullable()->after('coa_item_id');

            $table->string('no_urut', 50)->nullable()->after('sumber_anggaran');
            $table->string('nama_kegiatan', 255)->nullable()->after('no_urut');
            $table->string('akun', 20)->nullable()->after('nama_kegiatan');
            $table->string('bidang', 100)->nullable()->after('akun');

            $table->decimal('total', 18, 2)->default(0)->after('bidang');

            $table->timestamp('finalized_at')->nullable()->after('updated_at');
            $table->unsignedBigInteger('finalized_by')->nullable()->after('finalized_at');

            $table->index(['coa_item_id', 'tahun_anggaran']);
            $table->unique(['coa_item_id', 'tahun_anggaran', 'kode_unik_plo'], 'uniq_realisasi_header_kode_per_coa');
        });

        // Kalau mau FK (opsional, pastikan engine InnoDB semua)
        Schema::table('realisasi_headers', function (Blueprint $table) {
            $table->foreign('coa_item_id')->references('id')->on('coa_items')->nullOnDelete();
            $table->foreign('mak_id')->references('id')->on('maks')->nullOnDelete();
            $table->foreign('finalized_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('realisasi_headers', function (Blueprint $table) {
            $table->dropForeign(['coa_item_id']);
            $table->dropForeign(['mak_id']);
            $table->dropForeign(['finalized_by']);

            $table->dropUnique('uniq_realisasi_header_kode_per_coa');
            $table->dropIndex(['coa_item_id', 'tahun_anggaran']);

            $table->dropColumn([
                'coa_item_id','mak_id','no_urut','nama_kegiatan','akun','bidang',
                'total','finalized_at','finalized_by'
            ]);
        });
    }
};
