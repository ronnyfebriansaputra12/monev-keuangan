<?php

namespace App\Http\Controllers\Transaksi\Realisasi;

use App\Http\Controllers\Controller;
use App\Models\CoaItem;
use App\Models\RealisasiHeader;
use App\Services\Realisasi\RealisasiFinalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RealisasiController extends Controller
{
    public function index(Request $request)
    {
        $coaItemId = $request->get('coa_item_id');

        $coaItem = null;

        if ($coaItemId) {
            $coaItem = CoaItem::findOrFail($coaItemId);

            // hitung total realisasi dari semua header FINAL
            $realisasiTotal = RealisasiHeader::where('coa_item_id', $coaItemId)
                ->where('status_flow', 'FINAL_BENDAHARA')
                ->sum('total');

            // pakai jumlah sebagai pagu
            $pagu = (float) $coaItem->jumlah;

            // inject ke object (virtual, bukan DB)
            $coaItem->realisasi_total = $realisasiTotal;
            $coaItem->sisa_realisasi = max($pagu - $realisasiTotal, 0);
        }

        $headers = RealisasiHeader::withSum('lines as total', 'jumlah')
            ->when($coaItemId, fn($q) => $q->where('coa_item_id', $coaItemId))
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('transaksi.realisasi.index', compact('coaItem', 'headers'));
    }



    public function create(Request $request)
    {
        $coaItem = CoaItem::findOrFail($request->integer('coa_item_id'));

        return view('transaksi.realisasi.create', compact('coaItem'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'coa_item_id' => ['required', 'integer', 'exists:coa_items,id'],
            'tahun_anggaran' => ['required', 'integer'],
            'kode_unik_plo' => ['required', 'string', 'max:100'],
            'sumber_anggaran' => ['nullable', 'string', 'max:50'],
            'no_urut' => ['nullable', 'string', 'max:50'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'akun' => ['nullable', 'string', 'max:20'],
            'bidang' => ['nullable', 'string', 'max:100'],

            'lines' => ['required', 'array', 'min:1'],
            'lines.*.penerima_penyedia' => ['required', 'string', 'max:255'],
            'lines.*.uraian' => ['required', 'string'],
            'lines.*.jumlah' => ['required', 'numeric', 'min:0'],
            'lines.*.ppn' => ['nullable', 'numeric', 'min:0'],
            'lines.*.pph21' => ['nullable', 'numeric', 'min:0'],
            'lines.*.pph22' => ['nullable', 'numeric', 'min:0'],
            'lines.*.pph23' => ['nullable', 'numeric', 'min:0'],
            'lines.*.npwp' => ['nullable', 'string', 'max:50'],
            'lines.*.tgl_kuitansi' => ['nullable', 'date'],
        ]);

        $header = RealisasiHeader::create([
            'coa_item_id' => $data['coa_item_id'],
            'tahun_anggaran' => $data['tahun_anggaran'],
            'kode_unik_plo' => $data['kode_unik_plo'],
            'sumber_anggaran' => $data['sumber_anggaran'] ?? null,
            'no_urut' => $data['no_urut'] ?? null,
            'nama_kegiatan' => $data['nama_kegiatan'],
            'akun' => $data['akun'] ?? null,
            'bidang' => $data['bidang'] ?? null,
            'status_flow' => 'DRAFT',
            'created_by' => Auth::id(),
        ]);

        foreach ($data['lines'] as $line) {
            $header->lines()->create([
                'realisasi_header_id' => $header->id,
                'coa_item_id' => $data['coa_item_id'],
                'nama_kegiatan' => $data['nama_kegiatan'], // legacy field di lines
                'akun' => $data['akun'] ?? null,
                'bidang' => $data['bidang'] ?? null,
                'penerima_penyedia' => $line['penerima_penyedia'],
                'uraian' => $line['uraian'],
                'jumlah' => $line['jumlah'],
                'ppn' => $line['ppn'] ?? 0,
                'pph21' => $line['pph21'] ?? 0,
                'pph22' => $line['pph22'] ?? 0,
                'pph23' => $line['pph23'] ?? 0,
                'npwp' => $line['npwp'] ?? null,
                'tgl_kuitansi' => $line['tgl_kuitansi'] ?? null,
            ]);
        }

        $header->logs()->create([
            'actor_role' => 'PLO',
            'status' => 'DRAFT',
            'catatan' => 'Realisasi dibuat oleh PLO.',
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('realisasi.show', $header->id)
            ->with('success', 'Realisasi berhasil dibuat.');
    }
    public function show($id)
    {
        $header = RealisasiHeader::with([
            'coaItem',
            'lines',
            'logs',
            'attachments'
        ])->findOrFail($id);

        return view('transaksi.realisasi.show', compact('header'));
    }

    public function finalize($id, RealisasiFinalizer $finalizer)
    {
        $header = RealisasiHeader::findOrFail($id);
        // dd($header);
        // ===============================
        // AKALIN LOGIN (sementara)
        // ===============================
        $userId = Auth::id();

        $finalizer->finalize($header, $userId);

        return back()->with('success', 'Finalisasi berhasil. Sisa COA otomatis berkurang.');
    }
}
