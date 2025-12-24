<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('realisasi_headers', function (Blueprint $table) {
            if (!Schema::hasColumn('realisasi_headers', 'coa_item_id')) {
                $table->unsignedBigInteger('coa_item_id')->nullable()->after('id');
                $table->index('coa_item_id');
            }

            // opsional tapi biasanya dibutuhkan juga
            if (!Schema::hasColumn('realisasi_headers', 'tahun_anggaran')) {
                $table->integer('tahun_anggaran')->nullable()->after('coa_item_id');
            }

            if (!Schema::hasColumn('realisasi_headers', 'kode_unik_plo')) {
                $table->string('kode_unik_plo', 100)->nullable()->after('tahun_anggaran');
            }

            if (!Schema::hasColumn('realisasi_headers', 'status_flow')) {
                $table->string('status_flow', 50)->default('DRAFT')->after('kode_unik_plo');
            }

            if (!Schema::hasColumn('realisasi_headers', 'total')) {
                $table->decimal('total', 18, 2)->default(0)->after('status_flow');
            }

            if (!Schema::hasColumn('realisasi_headers', 'finalized_at')) {
                $table->timestamp('finalized_at')->nullable()->after('updated_at');
            }

            if (!Schema::hasColumn('realisasi_headers', 'finalized_by')) {
                $table->unsignedBigInteger('finalized_by')->nullable()->after('finalized_at');
            }
        });

        // FK opsional (kalau semua table InnoDB)
        Schema::table('realisasi_headers', function (Blueprint $table) {
            if (Schema::hasColumn('realisasi_headers', 'coa_item_id')) {
                $table->foreign('coa_item_id')->references('id')->on('coa_items')->nullOnDelete();
            }
            if (Schema::hasColumn('realisasi_headers', 'finalized_by')) {
                $table->foreign('finalized_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('realisasi_headers', function (Blueprint $table) {
            if (Schema::hasColumn('realisasi_headers', 'finalized_by')) {
                $table->dropForeign(['finalized_by']);
                $table->dropColumn('finalized_by');
            }

            if (Schema::hasColumn('realisasi_headers', 'finalized_at')) {
                $table->dropColumn('finalized_at');
            }

            if (Schema::hasColumn('realisasi_headers', 'total')) {
                $table->dropColumn('total');
            }

            if (Schema::hasColumn('realisasi_headers', 'status_flow')) {
                $table->dropColumn('status_flow');
            }

            if (Schema::hasColumn('realisasi_headers', 'kode_unik_plo')) {
                $table->dropColumn('kode_unik_plo');
            }

            if (Schema::hasColumn('realisasi_headers', 'tahun_anggaran')) {
                $table->dropColumn('tahun_anggaran');
            }

            if (Schema::hasColumn('realisasi_headers', 'coa_item_id')) {
                $table->dropForeign(['coa_item_id']);
                $table->dropIndex(['coa_item_id']);
                $table->dropColumn('coa_item_id');
            }
        });
    }
};
