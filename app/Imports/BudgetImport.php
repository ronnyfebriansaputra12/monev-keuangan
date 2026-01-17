<?php

namespace App\Imports;

use App\Models\{Program, Kegiatan, KlasifikasiRo, RincianOutput, Komponen, SubKomponen, MasterAkun, Mak, CoaItem, Satker};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class BudgetImport implements ToCollection, WithHeadingRow
{
    protected $tahun;
    // Tambahkan properti untuk menyimpan total sum
    public $totalImportedAmount = 0;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
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
            $tempTotal = 0; // Variabel sementara untuk sum

            foreach ($rows as $row) {
                $type = strtoupper(trim($row['catatan_desk'] ?? ''));
                $uraian = trim($row['uraian'] ?? '');
                $kode = trim($row['kode'] ?? '');

                if (empty($type) || empty($uraian)) continue;

                // Format Angka - Membersihkan format ribuan titik/koma
                $jumlahRaw = $row['jumlah'] ?? 0; // Ini merujuk pada Kolom G jika header excelnya bernama 'jumlah'
                $hargaRaw = $row['harga_satuan'] ?? 0;
                $volRaw = $row['vol'] ?? 0;

                $jumlah = is_numeric($jumlahRaw) ? (float)$jumlahRaw : (float) str_replace(['.', ','], ['', '.'], $jumlahRaw);
                $harga  = is_numeric($hargaRaw) ? (float)$hargaRaw : (float) str_replace(['.', ','], ['', '.'], $hargaRaw);
                $volume = is_numeric($volRaw) ? (float)$volRaw : (float) str_replace(['.', ','], ['', '.'], $volRaw);

                // Hitung otomatis jika jumlah kosong tapi vol & harga ada
                if ($jumlah <= 0 && $harga > 0 && $volume > 0) {
                    $jumlah = $volume * $harga;
                }

                switch ($type) {
                    case 'PROGRAM':
                        $satker = Satker::updateOrCreate(
                            ['tahun_anggaran' => $this->tahun, 'kode_satker' => 'PSDTN001'],
                            ['nama_satker' => 'Pusat Data dan Informasi Obat dan Makanan']
                        );
                        $item = Program::updateOrCreate(
                            ['kode_program' => $kode, 'tahun_anggaran' => $this->tahun],
                            ['nama_program' => $uraian, 'satker_id' => $satker->id]
                        );
                        $last['prog'] = $item->id;
                        break;

                    case 'KEGIATAN':
                        $item = Kegiatan::updateOrCreate(
                            ['kode_kegiatan' => $kode, 'program_id' => $last['prog'], 'tahun_anggaran' => $this->tahun],
                            ['nama_kegiatan' => $uraian]
                        );
                        $last['keg'] = $item->id;
                        break;

                    case 'KRO':
                        $item = KlasifikasiRo::updateOrCreate(
                            ['kode_klasifikasi' => $kode, 'kegiatan_id' => $last['keg'], 'tahun_anggaran' => $this->tahun],
                            ['nama_klasifikasi' => $uraian]
                        );
                        $last['kro'] = $item->id;
                        break;

                    case 'RO':
                        $item = RincianOutput::updateOrCreate(
                            ['kode_ro' => $kode, 'klasifikasi_ro_id' => $last['kro'], 'tahun_anggaran' => $this->tahun],
                            ['nama_ro' => $uraian]
                        );
                        $last['ro'] = $item->id;
                        break;

                    case 'KOM':
                        $item = Komponen::updateOrCreate(
                            ['kode_komponen' => $kode, 'rincian_output_id' => $last['ro'], 'tahun_anggaran' => $this->tahun],
                            ['nama_komponen' => $uraian]
                        );
                        $last['komp'] = $item->id;
                        break;

                    case 'SUBKOM':
                        $item = SubKomponen::updateOrCreate(
                            ['kode_subkomponen' => $kode, 'komponen_id' => $last['komp'], 'tahun_anggaran' => $this->tahun],
                            ['nama_subkomponen' => $uraian]
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
                        }

                        if ($type === 'SUB JUDUL MAK') $currentLevel = 1;
                        
                        $parentId = ($currentLevel == 2) ? $last['header_l1'] : null;

                        $itemCoa = CoaItem::updateOrCreate(
                            [
                                'urutan'          => $counterUrutan,
                                'sub_komponen_id' => $last['sub'],
                                'mak_id'          => $last['mak'],
                                'tahun_anggaran'  => $this->tahun,
                            ],
                            [
                                'parent_id'       => $parentId,
                                'level'           => $currentLevel,
                                'uraian'          => $uraianClean,
                                'volume'          => $volume,
                                'satuan'          => $row['sat'] ?? null,
                                'harga_satuan'    => $harga,
                                'jumlah'          => $jumlah,
                                'pagu_item'       => $jumlah,
                            ]
                        );

                        // --- LOGIKA SUM KOLOM G (JUMLAH) ---
                        // Kita hanya menjumlahkan jika levelnya adalah item akhir (biasanya level 0 atau 2 
                        // tergantung struktur anda) agar tidak double count dengan level Header/Sub Judul.
                        // Jika anda ingin total kasar semua baris, hapus kondisi if dibawah.
                        if ($currentLevel == 0 || $currentLevel == 2) {
                            $tempTotal += $jumlah;
                        }

                        if ($jumlah > 0) {
                            $realisasi = $itemCoa->realisasi_total ?? 0;
                            $itemCoa->update([
                                'sisa_realisasi' => $jumlah - $realisasi
                            ]);
                        }

                        if ($currentLevel == 1) {
                            $last['header_l1'] = $itemCoa->id;
                        }

                        $counterUrutan++;
                        break;
                }
            }
            // Simpan total ke properti class
            $this->totalImportedAmount = $tempTotal;
        });
    }
}