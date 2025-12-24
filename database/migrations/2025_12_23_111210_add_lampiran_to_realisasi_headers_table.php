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
            // Menggunakan longText agar aman menampung banyak file dalam JSON
            $table->longText('lampiran')->nullable()->after('status_digitalisasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasis', function (Blueprint $table) {
            $table->dropColumn('lampiran');
        });
    }
};
