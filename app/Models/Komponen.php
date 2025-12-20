<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Komponen extends Model
{
    protected $table = 'komponens';
    protected $fillable = ['rincian_output_id','kode_komponen','nama_komponen','tahun_anggaran'];

    public function rincianOutput(): BelongsTo { return $this->belongsTo(RincianOutput::class); }
    public function subKomponens(): HasMany { return $this->hasMany(SubKomponen::class); }
}
