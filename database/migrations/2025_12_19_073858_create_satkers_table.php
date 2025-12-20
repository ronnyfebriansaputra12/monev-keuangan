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
        Schema::create('satkers', function (Blueprint $table) {
            $table->id();
            $table->string('kode_satker', 50)->nullable();
            $table->string('nama_satker');
            $table->unsignedSmallInteger('tahun_anggaran');
            $table->timestamps();
            $table->unique(['kode_satker', 'tahun_anggaran'], 'uq_satker_kode_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satkers');
    }
};
