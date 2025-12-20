<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiLog extends Model
{
    protected $table = 'realisasi_logs';
    protected $fillable = ['realisasi_header_id','actor_role','status','catatan','created_by'];

    public function header(): BelongsTo { return $this->belongsTo(RealisasiHeader::class, 'realisasi_header_id'); }
}
