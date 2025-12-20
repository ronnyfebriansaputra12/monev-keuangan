<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            $table->string('kode_coa_item', 50)->nullable()->change();
            $table->string('nama_item', 255)->nullable()->change();
            $table->decimal('pagu_item', 18, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('coa_items', function (Blueprint $table) {
            $table->string('kode_coa_item', 50)->nullable(false)->change();
            $table->string('nama_item', 255)->nullable(false)->change();
            $table->decimal('pagu_item', 18, 2)->nullable(false)->change();
        });
    }
};
