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
        Schema::create('realisasi_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('realisasi_header_id')->constrained('realisasi_headers')->cascadeOnDelete();
            $table->foreignId('coa_item_id')->constrained('coa_items')->restrictOnDelete();
            $table->foreignId('mak_id')->nullable()->constrained('maks')->nullOnDelete();

            $table->string('no_urut', 50)->nullable();
            $table->string('nama_kegiatan')->nullable();
            $table->string('akun', 20)->nullable();

            $table->string('penerima_penyedia')->nullable();
            $table->text('uraian')->nullable();

            $table->decimal('jumlah', 18, 2)->default(0);
            $table->decimal('ppn', 18, 2)->default(0);
            $table->decimal('pph21', 18, 2)->default(0);
            $table->decimal('pph22', 18, 2)->default(0);
            $table->decimal('pph23', 18, 2)->default(0);

            $table->string('npwp', 50)->nullable();
            $table->date('tgl_kuitansi')->nullable();
            $table->string('bidang', 100)->nullable();

            $table->string('status_berkas_line', 100)->nullable();

            $table->timestamps();

            $table->index(['coa_item_id'], 'ix_rl_coa');
            $table->index(['realisasi_header_id'], 'ix_rl_header');
            $table->index(['tgl_kuitansi'], 'ix_rl_tgl_kuitansi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi_lines');
    }
};
