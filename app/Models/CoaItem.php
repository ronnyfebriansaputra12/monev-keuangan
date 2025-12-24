<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoaItem extends Model
{
    protected $table = 'coa_items';

    protected $fillable = [
        'urutan',
        'sub_komponen_id',
        'mak_id',
        'parent_id',
        'level',
        'uraian',
        'volume',
        'satuan',
        'harga_satuan',
        'jumlah',
        'tahun_anggaran',

        // legacy (biar insert aman kalau masih ada kolom lama)
        'kode_coa_item',
        'nama_item',
        'pagu_item',

        // tracking realisasi (baru)
        'realisasi_total',
        'sisa_realisasi',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'sub_komponen_id' => 'integer',
        'mak_id' => 'integer',
        'parent_id' => 'integer',
        'level' => 'integer',
        'volume' => 'integer',
        'harga_satuan' => 'decimal:2',
        'jumlah' => 'decimal:2',
        'tahun_anggaran' => 'integer',
        'pagu_item' => 'decimal:2',

        // tracking realisasi (baru)
        'realisasi_total' => 'decimal:2',
        'sisa_realisasi' => 'decimal:2',
    ];

    public function mak(): BelongsTo
    {
        return $this->belongsTo(Mak::class, 'mak_id');
    }

    public function subKomponen(): BelongsTo
    {
        return $this->belongsTo(SubKomponen::class, 'sub_komponen_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('urutan');
    }

    // ✅ relasi ke realisasi (baru)
    public function realisasiHeaders(): HasMany
    {
        return $this->hasMany(RealisasiHeader::class, 'coa_item_id');
    }

    /**
     * Pagu efektif:
     * - pakai pagu_item kalau ada
     * - kalau kosong, fallback ke jumlah
     */
    public function getPaguAttribute(): float
    {
        return (float) ($this->pagu_item ?? $this->jumlah ?? 0);
    }

    protected static function booted(): void
    {
        static::saving(function (CoaItem $item) {
            $vol = (int) ($item->volume ?? 0);
            $harga = (float) ($item->harga_satuan ?? 0);

            // kalau jumlah kosong, auto hitung
            if ($item->jumlah === null) {
                $item->jumlah = $vol * $harga;
            }

            // ✅ init sisa_realisasi saat pertama kali dibuat (baru)
            // jangan override kalau sudah pernah ada perhitungan realisasi
            if (!$item->exists) {
                $pagu = (float) ($item->pagu_item ?? $item->jumlah ?? 0);
                $item->realisasi_total = (float) ($item->realisasi_total ?? 0);
                $item->sisa_realisasi = $pagu - $item->realisasi_total;
            }
        });
    }
}
