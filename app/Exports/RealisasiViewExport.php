<?php

namespace App\Exports;

use App\Models\Realisasi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RealisasiViewExport implements FromView, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        // Memuat relasi lengkap untuk mendapatkan kode RO, Komponen, dan Subkomponen
        $q = Realisasi::with([
            'coaItem.mak.akun',
            'coaItem.subKomponen.komponen.rincianOutput'
        ]);

        // Filter tetap sama
        if ($this->request->status_berkas) $q->where('status_berkas', $this->request->status_berkas);
        if ($this->request->filter_gup) $q->where('gup', $this->request->filter_gup);
        if ($this->request->tgl_awal && $this->request->tgl_akhir) {
            $q->whereBetween('tgl_kuitansi', [$this->request->tgl_awal, $this->request->tgl_akhir]);
        }
        if ($this->request->filter_ro) {
            $q->whereHas('coaItem.subKomponen.komponen.rincianOutput', function ($query) {
                $query->where('kode_ro', $this->request->filter_ro);
            });
        }
        if ($this->request->filter_akun) {
            $q->whereHas('coaItem.mak.akun', function ($query) {
                $query->where('kode_akun', $this->request->filter_akun);
            });
        }

        $items = $q->orderBy('no_urut', 'asc')->get();

        $groupedItems = $items->groupBy(function ($item) {
            return $item->coaItem->mak->akun->kode_akun ?? 'Lainnya';
        });

        return view('exports.realisasi_excel', [
            'groupedItems' => $groupedItems,
            'request' => $this->request
        ]);
    }
}
