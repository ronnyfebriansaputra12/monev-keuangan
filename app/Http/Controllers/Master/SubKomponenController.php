<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\SubKomponen;
use App\Models\Komponen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubKomponenController extends Controller
{
    public function index(Request $request)
    {
        $komponenId = $request->input('komponen_id');
        $tahun = $request->input('tahun');
        $search = $request->input('search');

        $q = SubKomponen::with('komponen.rincianOutput.klasifikasiRo.kegiatan.program.satker');

        if ($komponenId) $q->where('komponen_id', (int)$komponenId);
        if ($tahun) $q->where('tahun_anggaran', (int)$tahun);

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_subkomponen', 'like', "%{$search}%")
                  ->orWhere('kode_subkomponen', 'like', "%{$search}%");
            });
        }

        $items = $q->orderBy('kode_subkomponen')->get();
        $komponens = Komponen::with('rincianOutput.klasifikasiRo.kegiatan.program.satker')->orderBy('kode_komponen')->get();

        return view('master.sub_komponens.index', compact('items', 'komponens', 'komponenId', 'tahun', 'search'));
    }

    public function create()
    {
        $komponens = Komponen::with('rincianOutput.klasifikasiRo.kegiatan.program.satker')->orderBy('kode_komponen')->get();
        return view('master.sub_komponens.create', compact('komponens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'komponen_id' => ['required', 'integer', 'exists:komponens,id'],
            'kode_subkomponen' => ['required', 'string', 'max:50'],
            'nama_subkomponen' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_subkomponen' => [
                Rule::unique('sub_komponens', 'kode_subkomponen')
                    ->where(fn($q) => $q->where('komponen_id', $validated['komponen_id'])
                                      ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        SubKomponen::create($validated);

        return redirect()->route('master.sub-komponens.index')->with('success', 'Sub Komponen berhasil ditambahkan.');
    }

    public function show(SubKomponen $sub_komponen)
    {
        return redirect()->route('master.sub-komponens.edit', $sub_komponen);
    }

    public function edit(SubKomponen $sub_komponen)
    {
        $komponens = Komponen::with('rincianOutput.klasifikasiRo.kegiatan.program.satker')->orderBy('kode_komponen')->get();
        return view('master.sub_komponens.edit', ['subKomponen' => $sub_komponen, 'komponens' => $komponens]);
    }

    public function update(Request $request, SubKomponen $sub_komponen)
    {
        $validated = $request->validate([
            'komponen_id' => ['required', 'integer', 'exists:komponens,id'],
            'kode_subkomponen' => ['required', 'string', 'max:50'],
            'nama_subkomponen' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_subkomponen' => [
                Rule::unique('sub_komponens', 'kode_subkomponen')
                    ->ignore($sub_komponen->id)
                    ->where(fn($q) => $q->where('komponen_id', $validated['komponen_id'])
                                      ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        $sub_komponen->update($validated);

        return redirect()->route('master.sub-komponens.index')->with('success', 'Sub Komponen berhasil diupdate.');
    }

    public function destroy(SubKomponen $sub_komponen)
    {
        $sub_komponen->delete();
        return redirect()->route('master.sub-komponens.index')->with('success', 'Sub Komponen berhasil dihapus.');
    }
}
