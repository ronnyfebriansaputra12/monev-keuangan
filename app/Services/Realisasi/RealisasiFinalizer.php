<?php

namespace App\Services\Realisasi;

use App\Models\RealisasiHeader;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RealisasiFinalizer
{
    /**
     * Finalisasi Realisasi oleh Bendahara.
     * Mengupdate status header dan memotong saldo sisa_realisasi pada CoaItem.
     */
    public function finalize(RealisasiHeader $header, int $userId = 1): RealisasiHeader
    {
        return DB::transaction(function () use ($header, $userId) {
            // 1. Load relasi coaItem (pastikan relasi ini ada di model RealisasiHeader)
            $header->load(['lines', 'coaItem']);
            if (!$header->coaItem) {
                throw ValidationException::withMessages([
                    'coa_item_id' => 'COA item tidak ditemukan pada transaksi realisasi ini.'
                ]);
            }

            // 2. Cek status flow agar tidak terjadi double finalization
            if (in_array($header->status_flow, ['FINAL_BENDAHARA', 'SELESAI'])) {
                throw ValidationException::withMessages([
                    'status_flow' => 'Realisasi sudah dalam status Final atau Selesai.'
                ]);
            }

            // 3. Hitung total penggunaan dari lines (field 'jumlah' pada lines)
            $totalPenggunaan = (float) $header->lines()->sum('jumlah');

            if ($totalPenggunaan <= 0) {
                throw ValidationException::withMessages([
                    'total' => 'Total realisasi harus lebih dari 0 berdasarkan rincian (lines).'
                ]);
            }

            // 4. Lock COA Item untuk update guna menghindari race condition saldo
            $coa = $header->coaItem()->lockForUpdate()->first();

            // Ambil data pagu dan realisasi saat ini
            $pagu = (float) ($coa->pagu_item ?? $coa->jumlah ?? 0);
            $realisasiSaatIni = (float) ($coa->realisasi_total ?? 0);
            $sisaSaatIni = (float) ($coa->sisa_realisasi ?? ($pagu - $realisasiSaatIni));

            // 5. Validasi Kecukupan Anggaran
            if ($totalPenggunaan > $sisaSaatIni) {
                throw ValidationException::withMessages([
                    'total' => "Gagal Finalisasi: Total penggunaan ({$totalPenggunaan}) melebihi sisa anggaran COA ({$sisaSaatIni})."
                ]);
            }

            // 6. Update Header Realisasi
            $header->update([
                'total' => $totalPenggunaan,
                'status_flow' => 'FINAL_BENDAHARA',
                'finalized_at' => now(),
                'finalized_by' => $userId,
            ]);

            // 7. Update Saldo pada Tabel COA (Update langsung ke field database)
            $coa->update([
                'realisasi_total' => $realisasiSaatIni + $totalPenggunaan,
                'sisa_realisasi' => $sisaSaatIni - $totalPenggunaan,
            ]);

            // 8. Catat History Log (Opsional jika tabel logs tersedia)
            if (method_exists($header, 'logs')) {
                $header->logs()->create([
                    'actor_role' => 'BENDAHARA',
                    'status' => 'FINAL_BENDAHARA',
                    'catatan' => 'Finalisasi realisasi oleh bendahara. Saldo COA terpotong otomatis.',
                    'created_by' => $userId,
                ]);
            }

            return $header->fresh();
        });
    }
}
