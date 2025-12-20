<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class SubKomponen extends Model
{
    protected $table = 'sub_komponens';
    protected $fillable = ['komponen_id','kode_subkomponen','nama_subkomponen','tahun_anggaran'];

    public function komponen(): BelongsTo { return $this->belongsTo(Komponen::class); }
    public function coaItems(): HasMany { return $this->hasMany(CoaItem::class); }
    public function paguLines(): HasMany { return $this->hasMany(PaguLine::class); }

    public function realisasiLines(): HasManyThrough
    {
        return $this->hasManyThrough(
            RealisasiLine::class,
            CoaItem::class,
            'sub_komponen_id', // FK pada coa_items
            'coa_item_id',     // FK pada realisasi_lines
            'id',
            'id'
        );
    }
}
