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
        Schema::create('klasifikasi_ros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->cascadeOnDelete();
            $table->string('kode_klasifikasi', 20);
            $table->string('nama_klasifikasi');
            $table->unsignedSmallInteger('tahun_anggaran');
            $table->timestamps();
            $table->unique(['kegiatan_id', 'kode_klasifikasi', 'tahun_anggaran'], 'uq_klasifikasi_kegiatan_kode_tahun');
            $table->index(['kegiatan_id', 'tahun_anggaran'], 'ix_klasifikasi_kegiatan_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klasifikasi_ros');
    }
};
