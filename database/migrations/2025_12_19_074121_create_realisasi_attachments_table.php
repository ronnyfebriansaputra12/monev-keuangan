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
        Schema::create('realisasi_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('realisasi_header_id')->constrained('realisasi_headers')->cascadeOnDelete();
            $table->string('type', 50)->nullable();
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            $table->index(['realisasi_header_id'], 'ix_ratt_header');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasi_attachments');
    }
};
