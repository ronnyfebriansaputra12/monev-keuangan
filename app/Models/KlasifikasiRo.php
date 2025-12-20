<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KlasifikasiRo extends Model
{
    protected $table = 'klasifikasi_ros';
    protected $fillable = ['kegiatan_id','kode_klasifikasi','nama_klasifikasi','tahun_anggaran'];

    public function kegiatan(): BelongsTo { return $this->belongsTo(Kegiatan::class); }
    public function rincianOutputs(): HasMany { return $this->hasMany(RincianOutput::class); }
}
