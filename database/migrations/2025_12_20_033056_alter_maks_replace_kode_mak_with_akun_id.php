<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('maks', function (Blueprint $table) {

            // hapus unique index & kolom lama
            if (Schema::hasColumn('maks', 'kode_mak')) {
                $table->dropUnique('uq_mak_kode');
                $table->dropColumn('kode_mak');
            }

            // tambah akun_id
            $table->foreignId('akun_id')
                ->after('id')
                ->constrained('master_akuns')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maks', function (Blueprint $table) {

            $table->dropForeign(['akun_id']);
            $table->dropColumn('akun_id');

            $table->string('kode_mak', 20);
            $table->unique('kode_mak', 'uq_mak_kode');
        });
    }
};
