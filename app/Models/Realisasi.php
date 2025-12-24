<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Realisasi extends Model
{
    protected $table = 'realisasis';

    protected $fillable = [
        // Fields dari Header
        'satker_id',
        'tahun_anggaran',
        'kode_unik_plo',
        'sumber_anggaran',
        'gup',
        'no_urut_arsip_spby',
        'status_flow',
        'tanggal_penyerahan_berkas',
        'status_berkas',
        'verifikasi_bendahara',
        'status_digitalisasi',
        'nama_kegiatan',
        'total',
        'finalized_at',
        'created_by',
        'updated_by',

        // Fields dari Line
        'coa_item_id',
        'mak_id',
        'no_urut',
        'akun',
        'bidang',
        'penerima_penyedia',
        'uraian',
        'jumlah',
        'ppn',
        'pph21',
        'pph22',
        'pph23',
        'npwp',
        'tgl_kuitansi',
        'status_berkas_line',
        'pph_final',
        'nomor_kuitansi',
        'lampiran'
    ];

    protected $casts = [
        'tanggal_penyerahan_berkas' => 'date',
        'tgl_kuitansi' => 'date',
        'status_digitalisasi' => 'boolean',
        'jumlah' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph21' => 'decimal:2',
        'pph22' => 'decimal:2',
        'pph23' => 'decimal:2',
        'finalized_at' => 'datetime',
        'lampiran' => 'array',
    ];


    public function satker(): BelongsTo
    {
        return $this->belongsTo(Satker::class);
    }

    public function coaItem(): BelongsTo
    {
        return $this->belongsTo(CoaItem::class, 'coa_item_id');
    }

    public function mak(): BelongsTo
    {
        return $this->belongsTo(Mak::class, 'mak_id');
    }

    // Relasi log dan attachment biasanya tetap mengacu ke ID utama model ini
    public function logs(): HasMany
    {
        // Gunakan 'id_realisasi' sebagai foreign key, bukan 'realisasi_id'
        return $this->hasMany(ActivityLog::class, 'realisasi_id')->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(RealisasiAttachment::class, 'realisasi_id');
    }


    // --- Booted Logic ---

    protected static function booted(): void
    {
        static::saving(function (Realisasi $realisasi) {
            // Default nilai pajak agar tidak null
            $realisasi->ppn   = $realisasi->ppn ?? 0;
            $realisasi->pph21 = $realisasi->pph21 ?? 0;
            $realisasi->pph22 = $realisasi->pph22 ?? 0;
            $realisasi->pph23 = $realisasi->pph23 ?? 0;
        });
    }

    // --- Accessors (Logic Perhitungan) ---

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
     * Total bersih = jumlah + ppn - total pph
     */
    public function getTotalBersihAttribute(): string
    {
        $jumlah = (float)$this->jumlah;
        $ppn = (float)$this->ppn;
        $pph = (float)$this->pph21 + (float)$this->pph22 + (float)$this->pph23;

        return number_format($jumlah + $ppn - $pph, 2, '.', '');
    }
}
