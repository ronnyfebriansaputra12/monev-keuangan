<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\CoaItem;
use App\Models\Mak;
use App\Models\SubKomponen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoaItemController extends Controller
{
    public function index(Request $request)
    {
        $subKomponenId = $request->input('sub_komponen_id');
        $makId = $request->input('mak_id');
        $tahun = $request->input('tahun');
        $search = $request->input('search');

        $q = CoaItem::with([
            'subKomponen',
            'mak.akun'
        ])
            ->when($subKomponenId, fn($qq) => $qq->where('sub_komponen_id', (int)$subKomponenId))
            ->when($makId, fn($qq) => $qq->where('mak_id', (int)$makId))
            ->when($tahun, fn($qq) => $qq->where('tahun_anggaran', (int)$tahun))
            ->when($search, fn($qq) => $qq->where('uraian', 'like', "%{$search}%"));

        $coaItems = $q->orderBy('sub_komponen_id')
            ->orderBy('mak_id')
            ->orderBy('tahun_anggaran')
            ->orderBy('urutan')
            ->get();

        $maks = Mak::with('akun')->orderBy('akun_id')->get();

        $subKomponens = SubKomponen::orderBy('kode_subkomponen')->get();

        return view('master.coa_items.index', compact(
            'coaItems',
            'maks',
            'subKomponens',
            'subKomponenId',
            'makId',
            'tahun',
            'search'
        ));
    }


    public function create()
    {
        $subKomponens = SubKomponen::with('komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker')
            ->orderBy('kode_subkomponen')
            ->get();

        $maks = Mak::with('akun')
            ->orderBy('akun_id')
            ->orderBy('nama_mak')
            ->get();

        $rows = old('items', [
            ['uraian' => '', 'volume' => 1, 'satuan' => 'Pcs', 'harga_satuan' => 0],
            ['uraian' => '', 'volume' => 1, 'satuan' => 'Pcs', 'harga_satuan' => 0],
            ['uraian' => '', 'volume' => 1, 'satuan' => 'Pcs', 'harga_satuan' => 0],
        ]);

        return view('master.coa_items.create', compact('subKomponens', 'maks', 'rows'));
    }

    /**
     * STORE BULK: berdasarkan Sub Komponen + MAK + Tahun
     * support hirarki pakai prefix ">" di uraian.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $header = $request->validate([
            'sub_komponen_id' => ['required', 'integer', 'exists:sub_komponens,id'],
            'mak_id' => ['required', 'integer', 'exists:maks,id'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.uraian' => ['required', 'string', 'max:255'],
            'items.*.volume' => ['nullable', 'integer', 'min:0'],
            'items.*.satuan' => ['nullable', 'string', 'max:50'],
            'items.*.harga_satuan' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($header, $validated) {

            // urutan mulai dari max urutan existing + 1 (per konteks)
            $startOrder = (int) CoaItem::query()
                ->where('sub_komponen_id', $header['sub_komponen_id'])
                ->where('mak_id', $header['mak_id'])
                ->where('tahun_anggaran', $header['tahun_anggaran'])
                ->max('urutan');

            $order = $startOrder + 1;

            // map last saved id per level (untuk parent_id)
            $lastIdAtLevel = [];

            foreach ($validated['items'] as $row) {
                [$level, $uraianClean] = $this->parseLevel($row['uraian']);

                $parentId = null;
                if ($level > 0) {
                    $parentId = $lastIdAtLevel[$level - 1] ?? null;
                }

                $vol = (int) ($row['volume'] ?? 0);
                $harga = (float) ($row['harga_satuan'] ?? 0);
                $jumlah = $vol * $harga;



                $item = CoaItem::create([
                    'sub_komponen_id' => $header['sub_komponen_id'],
                    'mak_id' => $header['mak_id'],
                    'tahun_anggaran' => $header['tahun_anggaran'],

                    'urutan' => $order,
                    'level' => $level,
                    'parent_id' => $parentId,

                    'uraian' => $uraianClean,
                    'volume' => $vol,
                    'satuan' => $row['satuan'] ?? null,
                    'harga_satuan' => $harga,
                    'jumlah' => $jumlah,
                ]);

                $lastIdAtLevel[$level] = $item->id;
                $this->resetDeeperLevels($lastIdAtLevel, $level);

                $order++;
            }
        });

        return redirect()
            ->route('master.coa-items.index')
            ->with('success', 'COA berhasil ditambahkan (bulk).');
    }

    /**
     * BULK EDIT FORM: berdasarkan sub_komponen_id + mak_id + tahun
     * GET /master/coa-items/bulk-edit?sub_komponen_id=..&mak_id=..&tahun=..
     */
    public function bulkEdit(Request $request)
    {
        $subKomponenId = (int) $request->query('sub_komponen_id');
        $makId = (int) $request->query('mak_id');
        $tahun = (int) $request->query('tahun');

        abort_if(!$subKomponenId || !$makId || !$tahun, 404);

        $coaItems = CoaItem::query()
            ->with(['mak.akun', 'subKomponen'])
            ->where('sub_komponen_id', $subKomponenId)
            ->where('mak_id', $makId)
            ->where('tahun_anggaran', $tahun)
            ->orderBy('urutan')
            ->get();

        $mak = Mak::with('akun')->findOrFail($makId);
        $subKomponen = SubKomponen::with('komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker')
            ->findOrFail($subKomponenId);

        return view('master.coa_items.edit', compact('coaItems', 'mak', 'subKomponen', 'tahun'));
    }

    /**
     * BULK UPDATE: update/create/delete berdasarkan sub_komponen_id + mak_id + tahun
     * POST /master/coa-items/bulk-update
     */
    public function bulkupdate(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'sub_komponen_id' => ['required', 'integer', 'exists:sub_komponens,id'],
            'mak_id' => ['required', 'integer', 'exists:maks,id'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', 'exists:coa_items,id'],
            'items.*.uraian' => ['required', 'string', 'max:255'],
            'items.*.volume' => ['nullable', 'integer', 'min:0'],
            'items.*.satuan' => ['nullable', 'string', 'max:50'],
            'items.*.harga_satuan' => ['nullable', 'numeric', 'min:0'],
            'items.*._delete' => ['nullable', 'in:1'],
        ]);

        $validated = $validator->validate();

        DB::transaction(function () use ($validated) {
            $subKomponenId = (int) $validated['sub_komponen_id'];
            $makId = (int) $validated['mak_id'];
            $tahun = (int) $validated['tahun_anggaran'];

            // urutan reset sesuai urutan submit (lebih mantap buat Excel)
            $order = 1;
            $lastIdAtLevel = [];

            foreach ($validated['items'] as $row) {

                // delete row
                if (!empty($row['_delete']) && !empty($row['id'])) {
                    CoaItem::query()
                        ->where('id', $row['id'])
                        ->where('sub_komponen_id', $subKomponenId)
                        ->where('mak_id', $makId)
                        ->where('tahun_anggaran', $tahun)
                        ->delete();
                    continue;
                }

                [$level, $uraianClean] = $this->parseLevel($row['uraian']);
                $parentId = $level > 0 ? ($lastIdAtLevel[$level - 1] ?? null) : null;

                $vol = (int) ($row['volume'] ?? 0);
                $harga = (float) ($row['harga_satuan'] ?? 0);
                $jumlah = $vol * $harga;

                $payload = [
                    'sub_komponen_id' => $subKomponenId,
                    'mak_id' => $makId,
                    'tahun_anggaran' => $tahun,

                    'urutan' => $order,
                    'level' => $level,
                    'parent_id' => $parentId,

                    'uraian' => $uraianClean,
                    'volume' => $vol,
                    'satuan' => $row['satuan'] ?? null,
                    'harga_satuan' => $harga,
                    'jumlah' => $jumlah,
                ];

                if (!empty($row['id'])) {
                    CoaItem::query()
                        ->where('id', $row['id'])
                        ->where('sub_komponen_id', $subKomponenId)
                        ->where('mak_id', $makId)
                        ->where('tahun_anggaran', $tahun)
                        ->update($payload);

                    $savedId = (int) $row['id'];
                } else {
                    $saved = CoaItem::create($payload);
                    $savedId = (int) $saved->id;
                }

                $lastIdAtLevel[$level] = $savedId;
                $this->resetDeeperLevels($lastIdAtLevel, $level);

                $order++;
            }
        });

        return redirect()
            ->route('master.coa-items.index', [
                'sub_komponen_id' => $validated['sub_komponen_id'],
                'mak_id' => $validated['mak_id'],
                'tahun' => $validated['tahun_anggaran'],
            ])
            ->with('success', 'COA berhasil diupdate (bulk).');
    }

    // =========================
    // Helpers
    // =========================

    /**
     * Ubah:
     * "> Biaya konsumsi" => level 1, uraian "Biaya konsumsi"
     * ">> Kudapan" => level 2, uraian "Kudapan"
     * "Pengadaan Pelaporan" => level 0, uraian "Pengadaan Pelaporan"
     */
    private function parseLevel(string $uraian): array
    {
        $raw = ltrim($uraian);

        $level = 0;
        while (str_starts_with($raw, '>')) {
            $level++;
            $raw = ltrim(substr($raw, 1));
        }

        $level = min($level, 10);

        // kalau user input cuma ">>>>" tanpa teks, minimal jadikan "-"
        $raw = trim($raw);
        if ($raw === '') $raw = '-';

        return [$level, $raw];
    }

    private function resetDeeperLevels(array &$lastIdAtLevel, int $level): void
    {
        foreach (array_keys($lastIdAtLevel) as $k) {
            if ($k > $level) unset($lastIdAtLevel[$k]);
        }
    }
}
