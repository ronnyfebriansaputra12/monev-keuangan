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
        Schema::table('users', function (Blueprint $table) {
            // Kita mengubah kolom role menggunakan change() 
            // Pastikan Anda sudah menginstal paket: composer require doctrine/dbal
            $table->enum('role', ['PLO', 'Verifikator', 'Bendahara', 'PPK', 'PPSPM'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['PLO', 'Verifikator', 'Bendahara'])->change();
        });
    }
};
