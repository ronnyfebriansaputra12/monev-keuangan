<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = ['satker_id','kode_program','nama_program','tahun_anggaran'];

    public function satker(): BelongsTo { return $this->belongsTo(Satker::class); }
    public function kegiatans(): HasMany { return $this->hasMany(Kegiatan::class); }
}
