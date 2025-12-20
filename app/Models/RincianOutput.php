<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RincianOutput extends Model
{
    protected $table = 'rincian_outputs';
    protected $fillable = ['klasifikasi_ro_id','kode_ro','nama_ro','tahun_anggaran'];

    public function klasifikasiRo(): BelongsTo { return $this->belongsTo(KlasifikasiRo::class); }
    public function komponens(): HasMany { return $this->hasMany(Komponen::class); }
}
