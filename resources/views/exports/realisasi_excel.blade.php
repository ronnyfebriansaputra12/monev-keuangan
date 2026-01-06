<table>
    <thead>
        <tr>
            <th colspan="13" style="text-align: center; font-weight: bold; font-size: 14pt;">SURAT PERNYATAAN TANGGUNG JAWAB BELANJA</th>
        </tr>
        <tr>
            <th colspan="13" style="text-align: center; font-weight: bold;">NOMOR : </th>
        </tr>
        <tr>
            <th colspan="13" style="text-align: center; font-weight: bold;">GUP : {{ $request->filter_gup ?? '-' }}</th>
        </tr>
        <tr>
            <th colspan="13" style="text-align: center; font-weight: bold;">
                Rancangan Output :
                @php
                // Mengambil data pertama untuk menampilkan header kode yang lengkap jika filter_ro dipilih
                $firstItem = null;
                foreach($groupedItems as $group) {
                $firstItem = $group->first();
                break;
                }
                $kodeRo = $firstItem->coaItem->subKomponen->komponen->rincianOutput->kode_ro ?? '';
                $kodeKmp = $firstItem->coaItem->subKomponen->komponen->kode_komponen ?? '';
                $kodeSub = $firstItem->coaItem->subKomponen->kode_subkomponen ?? '';
                @endphp
                {{ $kodeRo }}{{ $kodeKmp ? '.'.$kodeKmp : '' }}{{ $kodeSub ? '.'.$kodeSub : '' }}
            </th>
        </tr>
        <tr>
            <th colspan="13"></th>
        </tr>
        <tr style="background-color: #f2f2f2;">
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NO</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">AKUN</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">PENERIMA</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">URAIAN</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">JUMLAH</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">PPN</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">PPh 21</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">PPh 22</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">PPh 23</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">NPWP</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">MAK</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">TGL_KUITANSI</th>
            <th style="border: 1px solid #000; font-weight: bold; text-align: center;">BIDANG</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($groupedItems as $kodeAkun => $items)
        @php $subTotal = 0; @endphp
        @foreach($items as $item)
        @php
        $subTotal += $item->jumlah;
        // Ambil detail kode untuk kolom MAK
        $cRo = $item->coaItem->subKomponen->komponen->rincianOutput->kode_ro ?? '';
        $cKmp = $item->coaItem->subKomponen->komponen->kode_komponen ?? '';
        $cSub = $item->coaItem->subKomponen->kode_subkomponen ?? '';
        $cAkn = $item->coaItem->mak->akun->kode_akun ?? '';
        @endphp
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $no++ }}</td>
            <td style="border: 1px solid #000; background-color: #ffff00; text-align: center;">{{ $kodeAkun }}</td>
            <td style="border: 1px solid #000; background-color: #92d050;">{{ $item->penerima_penyedia }}</td>
            <td style="border: 1px solid #000;">{{ $item->uraian }}</td>
            <td style="border: 1px solid #000; background-color: #ffff00; text-align: right;">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: center;">0</td>
            <td style="border: 1px solid #000; text-align: center;">0</td>
            <td style="border: 1px solid #000; text-align: center;">0</td>
            <td style="border: 1px solid #000; text-align: center;">0</td>
            <td style="border: 1px solid #000;">{{ $item->npwp ?? '-' }}</td>
            <td style="border: 1px solid #000;">
                {{-- Menampilkan format MAK Lengkap: RO.KMP.SUB.AKUN --}}
                {{ $cRo }}.{{ $cKmp }}.{{ $cSub }}.{{ $cAkn }}
            </td>
            <td style="border: 1px solid #000; text-align: center;">{{ $item->tgl_kuitansi->format('d/m/Y') }}</td>
            <td style="border: 1px solid #000; text-align: center;">TU</td>
        </tr>
        @endforeach
        {{-- Baris Total per Akun --}}
        <tr>
            <td style="border: 1px solid #000; font-weight: bold; text-align: center;">{{ $kodeAkun }}</td>
            <td colspan="3" style="border: 1px solid #000; font-weight: bold; text-align: center; background-color: #f2f2f2;">Total {{ $kodeAkun }}</td>
            <td style="border: 1px solid #000; font-weight: bold; text-align: right; background-color: #ffff00;">{{ number_format($subTotal, 0, ',', '.') }}</td>
            <td style="border: 1px solid #000; text-align: center;">-</td>
            <td style="border: 1px solid #000; text-align: center;">-</td>
            <td style="border: 1px solid #000; text-align: center;">-</td>
            <td style="border: 1px solid #000; text-align: center;">-</td>
            <td style="border: 1px solid #000; text-align: center;">-</td>
            <td colspan="3" style="border: 1px solid #000; background-color: #f2f2f2;"></td>
        </tr>
        @endforeach
    </tbody>
</table>