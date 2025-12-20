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
        Schema::create('rincian_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klasifikasi_ro_id')->constrained('klasifikasi_ros')->cascadeOnDelete();
            $table->string('kode_ro', 50);
            $table->string('nama_ro');
            $table->unsignedSmallInteger('tahun_anggaran');
            $table->timestamps();
            $table->unique(['klasifikasi_ro_id', 'kode_ro', 'tahun_anggaran'], 'uq_ro_klasifikasi_kode_tahun');
            $table->index(['klasifikasi_ro_id', 'tahun_anggaran'], 'ix_ro_klasifikasi_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_outputs');
    }
};
