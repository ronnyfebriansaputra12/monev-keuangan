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
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('kode_kegiatan', 50);
            $table->string('nama_kegiatan');
            $table->unsignedSmallInteger('tahun_anggaran');
            $table->timestamps();
            $table->unique(['program_id', 'kode_kegiatan', 'tahun_anggaran'], 'uq_kegiatan_program_kode_tahun');
            $table->index(['program_id', 'tahun_anggaran'], 'ix_kegiatan_program_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
