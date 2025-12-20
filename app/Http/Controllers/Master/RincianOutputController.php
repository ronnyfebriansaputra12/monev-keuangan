<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\RincianOutput;
use App\Models\KlasifikasiRo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RincianOutputController extends Controller
{
    public function index(Request $request)
    {
        $klasifikasiRoId = $request->input('klasifikasi_ro_id');
        $tahun = $request->input('tahun');
        $search = $request->input('search');

        $q = RincianOutput::with('klasifikasiRo.kegiatan.program.satker');

        if ($klasifikasiRoId) $q->where('klasifikasi_ro_id', (int)$klasifikasiRoId);
        if ($tahun) $q->where('tahun_anggaran', (int)$tahun);

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_ro', 'like', "%{$search}%")
                  ->orWhere('kode_ro', 'like', "%{$search}%");
            });
        }

        $items = $q->orderBy('kode_ro')->get();
        $klasifikasiRos = KlasifikasiRo::with('kegiatan.program.satker')->orderBy('kode_klasifikasi')->get();

        return view('master.rincian_outputs.index', [
            'rincianOutputs' => $items,
            'klasifikasiRos' => $klasifikasiRos,
            'klasifikasiRoId' => $klasifikasiRoId,
            'tahun' => $tahun,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $klasifikasiRos = KlasifikasiRo::with('kegiatan.program.satker')->orderBy('kode_klasifikasi')->get();
        return view('master.rincian_outputs.create', compact('klasifikasiRos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'klasifikasi_ro_id' => ['required', 'integer', 'exists:klasifikasi_ros,id'],
            'kode_ro' => ['required', 'string', 'max:50'],
            'nama_ro' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_ro' => [
                Rule::unique('rincian_outputs', 'kode_ro')
                    ->where(fn($q) => $q->where('klasifikasi_ro_id', $validated['klasifikasi_ro_id'])
                                      ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        RincianOutput::create($validated);

        return redirect()->route('master.rincian-outputs.index')->with('success', 'Rincian Output berhasil ditambahkan.');
    }

    public function show(RincianOutput $rincian_output)
    {
        return redirect()->route('master.rincian-outputs.edit', $rincian_output);
    }

    public function edit(RincianOutput $rincian_output)
    {
        $klasifikasiRos = KlasifikasiRo::with('kegiatan.program.satker')->orderBy('kode_klasifikasi')->get();
        return view('master.rincian_outputs.edit', ['rincianOutput' => $rincian_output, 'klasifikasiRos' => $klasifikasiRos]);
    }

    public function update(Request $request, RincianOutput $rincian_output)
    {
        $validated = $request->validate([
            'klasifikasi_ro_id' => ['required', 'integer', 'exists:klasifikasi_ros,id'],
            'kode_ro' => ['required', 'string', 'max:50'],
            'nama_ro' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_ro' => [
                Rule::unique('rincian_outputs', 'kode_ro')
                    ->ignore($rincian_output->id)
                    ->where(fn($q) => $q->where('klasifikasi_ro_id', $validated['klasifikasi_ro_id'])
                                      ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        $rincian_output->update($validated);

        return redirect()->route('master.rincian-outputs.index')->with('success', 'Rincian Output berhasil diupdate.');
    }

    public function destroy(RincianOutput $rincian_output)
    {
        $rincian_output->delete();
        return redirect()->route('master.rincian-outputs.index')->with('success', 'Rincian Output berhasil dihapus.');
    }
}
