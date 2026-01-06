<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RealisasiNotification extends Notification
{
    use Queueable;

    protected $realisasi;
    protected $type;
    protected $extraData;

    public function __construct($realisasi, $type = 'create', $extraData = [])
    {
        $this->realisasi = $realisasi;
        $this->type = $type;
        $this->extraData = $extraData;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        if ($this->type === 'revisi') {
            return [
                'title' => 'Perlu Revisi: ' . $this->realisasi->nama_kegiatan,
                'message' => 'Berkas dikembalikan oleh Verifikator. Catatan: ' . ($this->extraData['catatan'] ?? '-'),
                'url' => route('realisasi-v2.edit', $this->realisasi->id),
                'realisasi_id' => $this->realisasi->id,
            ];
        }

        // Tipe Default: Create
        return [
            'title' => 'Realisasi Baru Perlu Verifikasi',
            'message' => "PLO telah membuat realisasi baru: {$this->realisasi->nama_kegiatan}",
            'url' => route('realisasi-v2.index', ['coa_item_id' => $this->realisasi->coa_item_id]),
            'realisasi_id' => $this->realisasi->id,
        ];
    }
}
