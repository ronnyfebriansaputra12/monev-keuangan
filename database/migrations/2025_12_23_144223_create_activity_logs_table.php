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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // Menghubungkan log ke tabel users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity');     // Contoh: 'Tambah Realisasi', 'Finalisasi'
            $table->text('description');   // Detail: 'User mengupdate lampiran pada ID #5'
            $table->string('role');         // Role user saat melakukan aksi (PLO/Bendahara/dll)
            $table->string('status_awal')->nullable(); // Status berkas sebelum berubah
            $table->string('status_akhir')->nullable(); // Status berkas setelah berubah
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
