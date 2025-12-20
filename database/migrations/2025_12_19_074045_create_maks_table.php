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
        Schema::create('maks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mak', 20);
            $table->string('nama_mak');
            $table->string('jenis_belanja', 50)->nullable();
            $table->timestamps();
            $table->unique('kode_mak', 'uq_mak_kode');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maks');
    }
};
