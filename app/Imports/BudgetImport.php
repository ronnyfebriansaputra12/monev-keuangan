<?php

namespace App\Imports;

use App\Models\{Program, Kegiatan, KlasifikasiRo, RincianOutput, Komponen, SubKomponen, MasterAkun, Mak, CoaItem};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class BudgetImport implements ToCollection, WithHeadingRow
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function collection(Collection $rows)
    {
        $last = [
            'prog' => null,
            'keg' => null,
            'kro' => null,
            'ro' => null,
            'komp' => null,
            'sub' => null,
            'mak' => null,
            'header_l1' => null
        ];

        $counterUrutan = 1;

        foreach ($rows as $row) {
            $type = strtoupper(trim($row['catatan_desk'] ?? ''));
            $uraian = trim($row['uraian'] ?? '');
            $kode = trim($row['kode'] ?? '');

            if (empty($type) || empty($uraian)) continue;

            // Bersihkan format angka
            $jumlahRaw = $row['jumlah'] ?? 0;
            $hargaRaw = $row['harga_satuan'] ?? 0;
            $volRaw = $row['vol'] ?? 0;

            $jumlah = (float) str_replace(['.', ','], ['', '.'], $jumlahRaw);
            $harga  = (float) str_replace(['.', ','], ['', '.'], $hargaRaw);
            $volume = (int) str_replace(['.', ','], ['', ''], $volRaw);

            if ($jumlah <= 0 && $harga > 0 && $volume > 0) {
                $jumlah = $volume * $harga;
            }

            switch ($type) {
                case 'PROGRAM':
                    $satker = \App\Models\Satker::where('tahun_anggaran', $this->tahun)->first();
                    if (!$satker) {
                        $satker = \App\Models\Satker::create([
                            'nama_satker' => 'Pusat Data dan Informasi Obat dan Makanan',
                            'kode_satker' => 'PSDTN001',
                            'tahun_anggaran' => $this->tahun
                        ]);
                    }
                    $item = Program::updateOrCreate(
                        ['kode_program' => $kode, 'tahun_anggaran' => $this->tahun],
                        ['nama_program' => $uraian, 'satker_id' => $satker->id]
                    );
                    $last['prog'] = $item->id;
                    break;

                case 'KEGIATAN':
                    $item = Kegiatan::updateOrCreate(
                        ['kode_kegiatan' => $kode, 'program_id' => $last['prog']],
                        ['nama_kegiatan' => $uraian, 'tahun_anggaran' => $this->tahun]
                    );
                    $last['keg'] = $item->id;
                    break;

                case 'KRO':
                    $item = KlasifikasiRo::updateOrCreate(
                        ['kode_klasifikasi' => $kode, 'kegiatan_id' => $last['keg']],
                        ['nama_klasifikasi' => $uraian, 'tahun_anggaran' => $this->tahun]
                    );
                    $last['kro'] = $item->id;
                    break;

                case 'RO':
                    $item = RincianOutput::updateOrCreate(
                        ['kode_ro' => $kode, 'klasifikasi_ro_id' => $last['kro']],
                        ['nama_ro' => $uraian, 'tahun_anggaran' => $this->tahun]
                    );
                    $last['ro'] = $item->id;
                    break;

                case 'KOM':
                    $item = Komponen::updateOrCreate(
                        ['kode_komponen' => $kode, 'rincian_output_id' => $last['ro']],
                        ['nama_komponen' => $uraian, 'tahun_anggaran' => $this->tahun]
                    );
                    $last['komp'] = $item->id;
                    break;

                case 'SUBKOM':
                    $item = SubKomponen::updateOrCreate(
                        ['kode_subkomponen' => $kode, 'komponen_id' => $last['komp']],
                        ['nama_subkomponen' => $uraian, 'tahun_anggaran' => $this->tahun]
                    );
                    $last['sub'] = $item->id;
                    break;

                case 'MAK':
                    $mAkun = MasterAkun::firstOrCreate(['kode_akun' => $kode], ['nama_akun' => $uraian]);
                    $item = Mak::updateOrCreate(
                        ['akun_id' => $mAkun->id, 'nama_mak' => $uraian],
                        ['nama_mak' => $uraian]
                    );
                    $last['mak'] = $item->id;
                    $last['header_l1'] = null;
                    break;

                // TAMBAHKAN CASE BARU DI SINI
                case 'SUB JUDUL MAK':
                case 'COA':
                    $currentLevel = 0;
                    $uraianClean = $uraian;

                    if (str_starts_with($uraian, '>>')) {
                        $currentLevel = 2;
                        $uraianClean = trim(substr($uraian, 2));
                    } elseif (str_starts_with($uraian, '>')) {
                        $currentLevel = 1;
                        $uraianClean = trim(substr($uraian, 1));
                    } else {
                        $currentLevel = 0;
                        $last['header_l1'] = null;
                    }

                    if ($type === 'SUB JUDUL MAK') {
                        $currentLevel = 1;
                        if (str_starts_with($uraian, '>')) {
                            $uraianClean = trim(substr($uraian, 1));
                        }
                    }

                    $parentId = ($currentLevel == 2) ? $last['header_l1'] : null;

                    // CARA AMAN: Cari data yang sudah ada terlebih dahulu
                    $itemCoa = CoaItem::where([
                        'urutan'          => $counterUrutan,
                        'sub_komponen_id' => $last['sub'],
                        'mak_id'          => $last['mak'],
                        'tahun_anggaran'  => $this->tahun,
                        'level'           => $currentLevel,
                    ])->first();

                    if ($itemCoa) {
                        // Jika ada, UPDATE data yang ada
                        $itemCoa->update([
                            'parent_id'       => $parentId,
                            'uraian'          => $uraianClean,
                            'volume'          => $volume,
                            'satuan'          => $row['sat'] ?? null,
                            'harga_satuan'    => $harga,
                            'jumlah'          => $jumlah,
                            'pagu_item'       => $jumlah,
                            // Hitung sisa realisasi secara manual agar tidak error TypeError
                            'sisa_realisasi'  => $jumlah - $itemCoa->realisasi_total,
                        ]);
                    } else {
                        // Jika tidak ada, BUAT data baru
                        $itemCoa = CoaItem::create([
                            'urutan'          => $counterUrutan,
                            'sub_komponen_id' => $last['sub'],
                            'mak_id'          => $last['mak'],
                            'parent_id'       => $parentId,
                            'level'           => $currentLevel,
                            'uraian'          => $uraianClean,
                            'volume'          => $volume,
                            'satuan'          => $row['sat'] ?? null,
                            'harga_satuan'    => $harga,
                            'jumlah'          => $jumlah,
                            'pagu_item'       => $jumlah,
                            'tahun_anggaran'  => $this->tahun,
                            'realisasi_total' => 0,
                            'sisa_realisasi'  => $jumlah,
                        ]);
                    }

                    // Tambahkan increment counter setelah proses selesai
                    $counterUrutan++;

                    if ($currentLevel == 1) {
                        $last['header_l1'] = $itemCoa->id;
                    }
                    break;
            }
        }
    }
}
