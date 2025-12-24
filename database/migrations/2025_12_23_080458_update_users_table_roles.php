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
            // Menambahkan role dengan pilihan yang sudah ditentukan
            $table->enum('role', ['PLO', 'Verifikator', 'Bendahara'])
                ->default('PLO')
                ->after('password');

            // Menambahkan kode unit untuk PLO (DMS, YND, KMR, GHE)
            // Nullable karena Verifikator dan Bendahara tidak memerlukan ini
            $table->string('plo_code', 10)->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'plo_code']);
        });
    }
};
