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
        Schema::create('komponens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rincian_output_id')->constrained('rincian_outputs')->cascadeOnDelete();
            $table->string('kode_komponen', 50);
            $table->string('nama_komponen');
            $table->unsignedSmallInteger('tahun_anggaran');
            $table->timestamps();
            $table->unique(['rincian_output_id', 'kode_komponen', 'tahun_anggaran'], 'uq_komponen_ro_kode_tahun');
            $table->index(['rincian_output_id', 'tahun_anggaran'], 'ix_komponen_ro_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komponens');
    }
};
