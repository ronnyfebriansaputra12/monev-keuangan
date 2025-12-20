<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kegiatan extends Model
{
    protected $fillable = ['program_id','kode_kegiatan','nama_kegiatan','tahun_anggaran'];

    public function program(): BelongsTo { return $this->belongsTo(Program::class); }
    public function klasifikasiRos(): HasMany { return $this->hasMany(KlasifikasiRo::class); }
}
