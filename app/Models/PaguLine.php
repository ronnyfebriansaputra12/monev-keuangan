<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaguLine extends Model
{
    protected $table = 'pagu_lines';
    protected $fillable = ['sub_komponen_id','mak_id','pagu_mak','tahun_anggaran'];
    protected $casts = ['pagu_mak' => 'decimal:2'];

    public function subKomponen(): BelongsTo { return $this->belongsTo(SubKomponen::class); }
    public function mak(): BelongsTo { return $this->belongsTo(Mak::class); }
}
