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
        'pph_final', // Pastikan ini ada
        'npwp',
        'tgl_kuitansi',
        'status_berkas_line',
        'nomor_kuitansi',
        'lampiran',
        'status_sp2d',
        'jenis_realisasi'
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
        'pph_final' => 'decimal:2',
        'finalized_at' => 'datetime',
        'lampiran' => 'array', // Krusial untuk menyimpan list berkas
    ];

    // --- Relasi ---

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

    public function logs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'realisasi_id')->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(RealisasiAttachment::class, 'realisasi_id');
    }
    // Tambahkan ini di Model Realisasi.php
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // --- Booted Logic ---

    protected static function booted(): void
    {
        static::saving(function (Realisasi $realisasi) {
            // Default nilai pajak agar tidak null saat perhitungan
            $realisasi->ppn       = $realisasi->ppn ?? 0;
            $realisasi->pph21     = $realisasi->pph21 ?? 0;
            $realisasi->pph22     = $realisasi->pph22 ?? 0;
            $realisasi->pph23     = $realisasi->pph23 ?? 0;
            $realisasi->pph_final = $realisasi->pph_final ?? 0;

            // Otomatis update field 'total' dari 'jumlah' bruto jika tidak diisi manual
            if (empty($realisasi->total)) {
                $realisasi->total = $realisasi->jumlah;
            }
        });
    }

    // --- Accessors (Logic Perhitungan Pajak Lengkap) ---

    /**
     * Total semua jenis PPh
     */
    public function getPphTotalAttribute(): string
    {
        $pph = (float)$this->pph21 + (float)$this->pph22 + (float)$this->pph23 + (float)$this->pph_final;
        return number_format($pph, 2, '.', '');
    }

    /**
     * Jumlah Bruto + PPN
     */
    public function getJumlahKotorAttribute(): string
    {
        $jumlah = (float)$this->jumlah;
        $ppn = (float)$this->ppn;
        return number_format($jumlah + $ppn, 2, '.', '');
    }

    /**
     * Total bersih yang diterima (Bruto + PPN - Semua PPh)
     */
    public function getTotalBersihAttribute(): string
    {
        $jumlah = (float)$this->jumlah;
        $ppn = (float)$this->ppn;
        $totalPph = (float)$this->pph_total;

        return number_format($jumlah + $ppn - $totalPph, 2, '.', '');
    }

    /**
     * Helper untuk mengecek apakah berkas tertentu sudah diupload
     * Contoh penggunaan: $realisasi->has_file('Kwitansi Hotel')
     */
    public function hasFile($namaBerkas): bool
    {
        if (!$this->lampiran || !is_array($this->lampiran)) return false;

        return collect($this->lampiran)->contains('nama_berkas', $namaBerkas);
    }
}
