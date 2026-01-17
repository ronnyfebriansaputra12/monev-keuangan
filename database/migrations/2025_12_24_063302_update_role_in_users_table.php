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
            // Menambahkan 'PPBJ' ke dalam list enum
            $table->enum('role', [
                'PLO',
                'Verifikator',
                'Bendahara',
                'PPK',
                'PPSPM',
                'PPBJ', // Role baru ditambahkan di sini
                'Superadmin'
            ])->change();

            // Jika field plo_code belum ada, biarkan baris ini. 
            // Jika sudah ada dari migration sebelumnya, baris ini bisa dihapus agar tidak error.
            if (!Schema::hasColumn('users', 'plo_code')) {
                $table->string('plo_code', 10)->nullable()->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kembalikan ke list awal tanpa PPBJ jika di-rollback
            $table->enum('role', ['PLO', 'Verifikator', 'Bendahara', 'PPK', 'PPSPM', 'Superadmin'])->change();
        });
    }
};
