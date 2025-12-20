<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiLine extends Model
{
    protected $table = 'realisasi_lines';

    protected $fillable = [
        'realisasi_header_id','coa_item_id','mak_id','no_urut','nama_kegiatan','akun',
        'penerima_penyedia','uraian','jumlah','ppn','pph21','pph22','pph23','npwp','tgl_kuitansi','bidang',
        'status_berkas_line'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph21' => 'decimal:2',
        'pph22' => 'decimal:2',
        'pph23' => 'decimal:2',
        'tgl_kuitansi' => 'date',
    ];

    public function header(): BelongsTo { return $this->belongsTo(RealisasiHeader::class, 'realisasi_header_id'); }
    public function coaItem(): BelongsTo { return $this->belongsTo(CoaItem::class, 'coa_item_id'); }
    public function mak(): BelongsTo { return $this->belongsTo(Mak::class, 'mak_id'); }

    public function getTotalBersihAttribute(): string
    {
        $jumlah = (float)$this->jumlah;
        $ppn = (float)$this->ppn;
        $pph = (float)$this->pph21 + (float)$this->pph22 + (float)$this->pph23;
        return number_format($jumlah + $ppn - $pph, 2, '.', '');
    }
}
