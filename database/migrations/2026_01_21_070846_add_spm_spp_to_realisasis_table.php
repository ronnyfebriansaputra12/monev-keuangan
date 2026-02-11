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
        Schema::table('realisasis', function (Blueprint $table) {
            // Kolom SPM
            $table->string('no_spm')->nullable()->after('status_sp2d');
            $table->date('tgl_spm')->nullable()->after('no_spm');

            // Kolom Faktur Pajak
            $table->string('no_faktur_pajak')->nullable()->after('tgl_spm');
            $table->date('tgl_faktur_pajak')->nullable()->after('no_faktur_pajak');

            // Kolom SPP
            $table->string('no_spp')->nullable()->after('tgl_faktur_pajak');
            $table->date('tgl_spp')->nullable()->after('no_spp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasis', function (Blueprint $table) {
            $table->dropColumn([
                'no_spm',
                'tgl_spm',
                'no_faktur_pajak',
                'tgl_faktur_pajak',
                'no_spp',
                'tgl_spp'
            ]);
        });
    }
};
