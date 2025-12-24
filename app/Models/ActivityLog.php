<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity',
        'description',
        'role',
        'status_awal',
        'status_akhir',
        'ip_address',
        'realisasi_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function realisasi(): BelongsTo
    {
        return $this->belongsTo(Realisasi::class, 'realisasi_id');
    }
}
