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
        Schema::create('coa_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_komponen_id')->constrained('sub_komponens')->cascadeOnDelete();
            $table->foreignId('mak_id')->constrained('maks')->restrictOnDelete();
            $table->string('kode_coa_item', 50);
            $table->string('nama_item');
            $table->decimal('pagu_item', 18, 2)->default(0);
            $table->unsignedSmallInteger('tahun_anggaran');
            $table->timestamps();

            $table->unique(['sub_komponen_id', 'mak_id', 'kode_coa_item', 'tahun_anggaran'], 'uq_coa_sub_mak_kode_tahun');
            $table->index(['sub_komponen_id', 'tahun_anggaran'], 'ix_coa_sub_tahun');
            $table->index(['mak_id', 'tahun_anggaran'], 'ix_coa_mak_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_items');
    }
};
