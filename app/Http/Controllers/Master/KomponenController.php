<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Komponen;
use App\Models\RincianOutput;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KomponenController extends Controller
{
    public function index(Request $request)
    {
        $roId = $request->input('rincian_output_id');
        $tahun = $request->input('tahun');
        $search = $request->input('search');

        $q = Komponen::with('rincianOutput.klasifikasiRo.kegiatan.program.satker');

        if ($roId) $q->where('rincian_output_id', (int)$roId);
        if ($tahun) $q->where('tahun_anggaran', (int)$tahun);

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_komponen', 'like', "%{$search}%")
                  ->orWhere('kode_komponen', 'like', "%{$search}%");
            });
        }

        $items = $q->orderBy('kode_komponen')->get();
        $rincianOutputs = RincianOutput::with('klasifikasiRo.kegiatan.program.satker')->orderBy('kode_ro')->get();

        return view('master.komponens.index', compact('items', 'rincianOutputs', 'roId', 'tahun', 'search'));
    }

    public function create()
    {
        $rincianOutputs = RincianOutput::with('klasifikasiRo.kegiatan.program.satker')->orderBy('kode_ro')->get();
        return view('master.komponens.create', compact('rincianOutputs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rincian_output_id' => ['required', 'integer', 'exists:rincian_outputs,id'],
            'kode_komponen' => ['required', 'string', 'max:50'],
            'nama_komponen' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_komponen' => [
                Rule::unique('komponens', 'kode_komponen')
                    ->where(fn($q) => $q->where('rincian_output_id', $validated['rincian_output_id'])
                                      ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        Komponen::create($validated);

        return redirect()->route('master.komponens.index')->with('success', 'Komponen berhasil ditambahkan.');
    }

    public function show(Komponen $komponen)
    {
        return redirect()->route('master.komponens.edit', $komponen);
    }

    public function edit(Komponen $komponen)
    {
        $rincianOutputs = RincianOutput::with('klasifikasiRo.kegiatan.program.satker')->orderBy('kode_ro')->get();
        return view('master.komponens.edit', compact('komponen', 'rincianOutputs'));
    }

    public function update(Request $request, Komponen $komponen)
    {
        $validated = $request->validate([
            'rincian_output_id' => ['required', 'integer', 'exists:rincian_outputs,id'],
            'kode_komponen' => ['required', 'string', 'max:50'],
            'nama_komponen' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_komponen' => [
                Rule::unique('komponens', 'kode_komponen')
                    ->ignore($komponen->id)
                    ->where(fn($q) => $q->where('rincian_output_id', $validated['rincian_output_id'])
                                      ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        $komponen->update($validated);

        return redirect()->route('master.komponens.index')->with('success', 'Komponen berhasil diupdate.');
    }

    public function destroy(Komponen $komponen)
    {
        $komponen->delete();
        return redirect()->route('master.komponens.index')->with('success', 'Komponen berhasil dihapus.');
    }
}
