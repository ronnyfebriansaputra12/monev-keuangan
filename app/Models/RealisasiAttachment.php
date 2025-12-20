<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiAttachment extends Model
{
    protected $table = 'realisasi_attachments';
    protected $fillable = ['realisasi_header_id','type','file_path','original_name','uploaded_by'];

    public function header(): BelongsTo { return $this->belongsTo(RealisasiHeader::class, 'realisasi_header_id'); }
}
