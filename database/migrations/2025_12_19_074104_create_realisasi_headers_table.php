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
        Schema::create('realisasi_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satker_id')->nullable()->constrained('satkers')->nullOnDelete();
            $table->unsignedSmallInteger('tahun_anggaran');

            $table->string('kode_unik_plo', 100)->nullable();
            $table->string('sumber_anggaran', 50)->nullable();     // GUP 1 / GUP 2
            $table->string('gup', 50)->nullable();                 // diisi bendahara
            $table->string('no_urut_arsip_spby', 100)->nullable(); // diisi bendahara

            $table->string('status_flow', 50)->default('DRAFT');   // DRAFT/DIAJUKAN_PLO/DIKEMBALIKAN/...
            $table->date('tanggal_penyerahan_berkas')->nullable();

            $table->string('status_berkas', 100)->nullable();
            $table->string('verifikasi_bendahara', 100)->nullable();
            $table->boolean('status_digitalisasi')->default(false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index(['tahun_anggaran', 'status_flow'], 'ix_rh_tahun_status');
            $table->index(['satker_id', 'tahun_anggaran'], 'ix_rh_satker_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi_headers');
    }
};
