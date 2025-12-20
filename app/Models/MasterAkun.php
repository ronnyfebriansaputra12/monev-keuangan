<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAkun extends Model
{
    protected $table = 'master_akuns';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
    ];

    public function maks()
    {
        return $this->hasMany(Mak::class, 'akun_id');
    }
}
