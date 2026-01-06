<?php

namespace App\Exports;

use App\Models\Realisasi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RealisasiExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $q = Realisasi::with(['coaItem.mak', 'coaItem.subKomponen.komponen.rincianOutput']);

        // Samakan Logika Filter dengan Controller Index
        if ($this->request->status_berkas) {
            $q->where('status_berkas', $this->request->status_berkas);
        }
        if ($this->request->filter_gup) {
            $q->where('gup', $this->request->filter_gup);
        }
        if ($this->request->filter_ro) {
            $q->whereHas('coaItem.subKomponen.komponen.rincianOutput', function ($query) {
                $query->where('nama_ro', $this->request->filter_ro);
            });
        }
        if ($this->request->filter_akun) {
            $q->whereHas('coaItem.mak', function ($query) {
                $query->where('nama_mak', $this->request->filter_akun);
            });
        }

        return $q->orderBy('no_urut', 'desc');
    }

    public function headings(): array
    {
        return ['Kode PLO', 'No Urut', 'Tgl Kuitansi', 'Penerima', 'Uraian', 'Bruto', 'Status', 'GUP'];
    }

    public function map($item): array
    {
        return [
            $item->kode_unik_plo,
            $item->no_urut,
            $item->tgl_kuitansi->format('d/m/Y'),
            $item->penerima_penyedia,
            $item->uraian,
            $item->jumlah,
            $item->status_berkas,
            $item->gup,
        ];
    }
}
