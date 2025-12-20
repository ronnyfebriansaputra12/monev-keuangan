<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Satker extends Model
{
    protected $fillable = ['kode_satker','nama_satker','tahun_anggaran'];

    public function programs(): HasMany { return $this->hasMany(Program::class); }
    public function realisasiHeaders(): HasMany { return $this->hasMany(RealisasiHeader::class); }
}
