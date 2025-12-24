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
        Schema::create('realisasis', function (Blueprint $table) {
            $table->id();

            // --- DATA HEADER (Identitas Kegiatan/Dokumen) ---
            $table->foreignId('satker_id')->nullable()->constrained('satkers')->nullOnDelete();
            $table->year('tahun_anggaran');
            $table->string('kode_unik_plo')->nullable();
            $table->string('sumber_anggaran')->nullable();
            $table->string('gup')->nullable();
            $table->string('no_urut_arsip_spby')->nullable();
            $table->string('status_flow')->nullable();
            $table->date('tanggal_penyerahan_berkas')->nullable();
            $table->string('status_berkas')->nullable();
            $table->boolean('verifikasi_bendahara')->default(false);
            $table->boolean('status_digitalisasi')->default(false);
            $table->string('nama_kegiatan')->nullable();
            $table->decimal('total', 15, 2)->default(0); // Total keseluruhan kuitansi
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            // --- DATA LINE (Detail Transaksi) ---
            $table->foreignId('coa_item_id')->nullable()->constrained('coa_items')->nullOnDelete();
            $table->foreignId('mak_id')->nullable()->constrained('maks')->nullOnDelete();
            $table->integer('no_urut')->nullable();
            $table->string('akun')->nullable();
            $table->string('bidang')->nullable();
            $table->string('penerima_penyedia')->nullable();
            $table->text('uraian')->nullable();

            // Kolom Finansial
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('pph21', 15, 2)->default(0);
            $table->decimal('pph22', 15, 2)->default(0);
            $table->decimal('pph23', 15, 2)->default(0);

            $table->string('npwp')->nullable();
            $table->date('tgl_kuitansi')->nullable();
            $table->string('status_berkas_line')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasis');
    }
};
