<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PaguLine;
use App\Models\SubKomponen;
use App\Models\Mak;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaguLineController extends Controller
{
    public function index(Request $request)
    {
        $subKomponenId = $request->input('sub_komponen_id');
        $makId = $request->input('mak_id');
        $tahun = $request->input('tahun');

        $q = PaguLine::with(['subKomponen.komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker', 'mak']);

        if ($subKomponenId) $q->where('sub_komponen_id', (int)$subKomponenId);
        if ($makId) $q->where('mak_id', (int)$makId);
        if ($tahun) $q->where('tahun_anggaran', (int)$tahun);

        $paguLines = $q->orderByDesc('pagu_mak')->get();

        $subKomponens = SubKomponen::with('komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker')
            ->orderBy('kode_subkomponen')->get();

        $maks = Mak::orderBy('kode_mak')->get();

        return view('master.pagu_lines.index', compact('paguLines','subKomponens','maks','subKomponenId','makId','tahun'));
    }

    public function create()
    {
        $subKomponens = SubKomponen::with('komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker')
            ->orderBy('kode_subkomponen')->get();
        $maks = Mak::orderBy('kode_mak')->get();

        return view('master.pagu_lines.create', compact('subKomponens', 'maks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_komponen_id' => ['required', 'integer', 'exists:sub_komponens,id'],
            'mak_id' => ['required', 'integer', 'exists:maks,id'],
            'pagu_mak' => ['required', 'numeric', 'min:0'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'mak_id' => [
                Rule::unique('pagu_lines', 'mak_id')
                    ->where(fn($q) => $q->where('sub_komponen_id', $validated['sub_komponen_id'])
                                      ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        PaguLine::create($validated);

        return redirect()->route('master.pagu-lines.index')->with('success', 'Pagu MAK berhasil ditambahkan.');
    }

    public function show(PaguLine $pagu_line)
    {
        return redirect()->route('master.pagu-lines.edit', $pagu_line);
    }

    public function edit(PaguLine $pagu_line)
    {
        $subKomponens = SubKomponen::with('komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker')
            ->orderBy('kode_subkomponen')->get();
        $maks = Mak::orderBy('kode_mak')->get();

        return view('master.pagu_lines.edit', compact('pagu_line','subKomponens','maks'));
    }

    public function update(Request $request, PaguLine $pagu_line)
    {
        $validated = $request->validate([
            'sub_komponen_id' => ['required', 'integer', 'exists:sub_komponens,id'],
            'mak_id' => ['required', 'integer', 'exists:maks,id'],
            'pagu_mak' => ['required', 'numeric', 'min:0'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'mak_id' => [
                Rule::unique('pagu_lines', 'mak_id')
                    ->ignore($pagu_line->id)
                    ->where(fn($q) => $q->where('sub_komponen_id', $validated['sub_komponen_id'])
                                      ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        $pagu_line->update($validated);

        return redirect()->route('master.pagu-lines.index')->with('success', 'Pagu MAK berhasil diupdate.');
    }

    public function destroy(PaguLine $pagu_line)
    {
        $pagu_line->delete();
        return redirect()->route('master.pagu-lines.index')->with('success', 'Pagu MAK berhasil dihapus.');
    }
}
