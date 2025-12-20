<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RealisasiHeader extends Model
{
    protected $table = 'realisasi_headers';

    protected $fillable = [
        'satker_id','tahun_anggaran','kode_unik_plo','sumber_anggaran','gup','no_urut_arsip_spby',
        'status_flow','tanggal_penyerahan_berkas','status_berkas','verifikasi_bendahara','status_digitalisasi',
        'created_by','updated_by',
    ];

    protected $casts = [
        'tanggal_penyerahan_berkas' => 'date',
        'status_digitalisasi' => 'boolean',
    ];

    public function satker(): BelongsTo { return $this->belongsTo(Satker::class); }
    public function lines(): HasMany { return $this->hasMany(RealisasiLine::class, 'realisasi_header_id'); }
    public function logs(): HasMany { return $this->hasMany(RealisasiLog::class, 'realisasi_header_id'); }
    public function attachments(): HasMany { return $this->hasMany(RealisasiAttachment::class, 'realisasi_header_id'); }
}
