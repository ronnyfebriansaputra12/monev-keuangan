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
        Schema::create('sub_komponens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('komponen_id')->constrained('komponens')->cascadeOnDelete();
            $table->string('kode_subkomponen', 50);
            $table->string('nama_subkomponen');
            $table->unsignedSmallInteger('tahun_anggaran');
            $table->timestamps();
            $table->unique(['komponen_id', 'kode_subkomponen', 'tahun_anggaran'], 'uq_subkomponen_komponen_kode_tahun');
            $table->index(['komponen_id', 'tahun_anggaran'], 'ix_subkomponen_komponen_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_komponens');
    }
};
