<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiLine extends Model
{
    protected $table = 'realisasi_lines';

    protected $fillable = [
        'realisasi_header_id',
        'coa_item_id',
        'mak_id',

        // legacy / optional (kalau masih ada dari import lama)
        'no_urut',
        'nama_kegiatan',
        'akun',
        'bidang',

        // inti transaksi baris
        'penerima_penyedia',
        'uraian',
        'jumlah',
        'ppn',
        'pph21',
        'pph22',
        'pph23',
        'npwp',
        'tgl_kuitansi',

        'status_berkas_line'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph21' => 'decimal:2',
        'pph22' => 'decimal:2',
        'pph23' => 'decimal:2',
        'tgl_kuitansi' => 'date',
    ];

    public function header(): BelongsTo
    {
        return $this->belongsTo(RealisasiHeader::class, 'realisasi_header_id');
    }

    public function coaItem(): BelongsTo
    {
        return $this->belongsTo(CoaItem::class, 'coa_item_id');
    }

    public function mak(): BelongsTo
    {
        return $this->belongsTo(Mak::class, 'mak_id');
    }

    /**
     * Pastikan line selalu ikut data header (biar konsisten).
     * - 1 header = 1 kegiatan
     * - kalau header berubah, line ikut
     */
    protected static function booted(): void
    {
        static::saving(function (RealisasiLine $line) {
            // kalau header belum ada, skip
            if (!$line->realisasi_header_id) {
                return;
            }

            // load header kalau belum ada di relation
            $header = $line->relationLoaded('header')
                ? $line->header
                : RealisasiHeader::query()->find($line->realisasi_header_id);

            if (!$header) {
                return;
            }

            // paksa konsisten ikut header
            $line->coa_item_id = $header->coa_item_id ?? $line->coa_item_id;
            $line->mak_id      = $header->mak_id ?? $line->mak_id;

            // kolom-kolom ini "legacy" tapi kita isi otomatis biar seragam
            $line->nama_kegiatan = $header->nama_kegiatan ?? $line->nama_kegiatan;
            $line->akun          = $header->akun ?? $line->akun;
            $line->bidang        = $header->bidang ?? $line->bidang;

            // default nilai pajak biar tidak null
            $line->ppn  = $line->ppn  ?? 0;
            $line->pph21 = $line->pph21 ?? 0;
            $line->pph22 = $line->pph22 ?? 0;
            $line->pph23 = $line->pph23 ?? 0;
        });
    }

    // ===== Accessor tambahan (biar mudah di view/report) =====

    public function getPphTotalAttribute(): string
    {
        $pph = (float)$this->pph21 + (float)$this->pph22 + (float)$this->pph23;
        return number_format($pph, 2, '.', '');
    }

    public function getJumlahKotorAttribute(): string
    {
        $jumlah = (float)$this->jumlah;
        $ppn = (float)$this->ppn;
        return number_format($jumlah + $ppn, 2, '.', '');
    }

    /**
     * Total bersih = jumlah + ppn - (pph21+pph22+pph23)
     */
    public function getTotalBersihAttribute(): string
    {
        $jumlah = (float)$this->jumlah;
        $ppn = (float)$this->ppn;
        $pph = (float)$this->pph21 + (float)$this->pph22 + (float)$this->pph23;

        return number_format($jumlah + $ppn - $pph, 2, '.', '');
    }
}
