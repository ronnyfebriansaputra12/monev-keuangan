<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mak extends Model
{
    protected $table = 'maks';

    // ❗ ganti kode_mak -> akun_id
    protected $fillable = [
        'akun_id',
        'nama_mak',
        'jenis_belanja',
    ];

    // ✅ Relasi ke Master Akun
    public function akun(): BelongsTo
    {
        return $this->belongsTo(MasterAkun::class, 'akun_id');
    }

    // Relasi lama tetap
    public function coaItems(): HasMany
    {
        return $this->hasMany(CoaItem::class);
    }

    public function paguLines(): HasMany
    {
        return $this->hasMany(PaguLine::class);
    }

    public function realisasiLines(): HasMany
    {
        return $this->hasMany(RealisasiLine::class);
    }
}
