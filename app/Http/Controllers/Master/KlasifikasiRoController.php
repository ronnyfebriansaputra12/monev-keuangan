<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\KlasifikasiRo;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KlasifikasiRoController extends Controller
{
    public function index(Request $request)
    {
        $kegiatanId = $request->input('kegiatan_id');
        $tahun      = $request->input('tahun');
        $search     = $request->input('search');

        $q = KlasifikasiRo::with('kegiatan.program.satker');

        if ($kegiatanId) $q->where('kegiatan_id', (int) $kegiatanId);
        if ($tahun)      $q->where('tahun_anggaran', (int) $tahun);

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_klasifikasi', 'like', "%{$search}%")
                    ->orWhere('kode_klasifikasi', 'like', "%{$search}%");
            });
        }

        $klasifikasiRos = $q->orderBy('kode_klasifikasi')->get();
        $kegiatans      = Kegiatan::with('program.satker')->orderBy('kode_kegiatan')->get();

        return view('master.klasifikasi_ros.index', compact(
            'klasifikasiRos',
            'kegiatans',
            'kegiatanId',
            'tahun',
            'search'
        ));
    }

    public function create()
    {
        $kegiatans = Kegiatan::with('program.satker')->orderBy('kode_kegiatan')->get();
        return view('master.klasifikasi_ros.create', compact('kegiatans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kegiatan_id'      => ['required', 'integer', 'exists:kegiatans,id'],
            'kode_klasifikasi' => ['required', 'string', 'max:20'],
            'nama_klasifikasi' => ['required', 'string', 'max:255'],
            'tahun_anggaran'   => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_klasifikasi' => [
                Rule::unique('klasifikasi_ros', 'kode_klasifikasi')
                    ->where(
                        fn($q) => $q
                            ->where('kegiatan_id', $validated['kegiatan_id'])
                            ->where('tahun_anggaran', $validated['tahun_anggaran'])
                    ),
            ],
        ]);

        KlasifikasiRo::create($validated);

        return redirect()
            ->route('master.klasifikasi-ros.index')
            ->with('success', 'Klasifikasi RO berhasil ditambahkan.');
    }

    public function show(KlasifikasiRo $klasifikasi_ro)
    {
        return redirect()->route('master.klasifikasi-ros.edit', $klasifikasi_ro);
    }

    public function edit(KlasifikasiRo $klasifikasi_ro)
    {
        $kegiatans = Kegiatan::with('program.satker')->orderBy('kode_kegiatan')->get();
        return view('master.klasifikasi_ros.edit', [
            'klasifikasiRo' => $klasifikasi_ro,
            'kegiatans' => $kegiatans,
        ]);
    }

    public function update(Request $request, KlasifikasiRo $klasifikasi_ro)
    {
        $validated = $request->validate([
            'kegiatan_id'      => ['required', 'integer', 'exists:kegiatans,id'],
            'kode_klasifikasi' => ['required', 'string', 'max:20'],
            'nama_klasifikasi' => ['required', 'string', 'max:255'],
            'tahun_anggaran'   => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_klasifikasi' => [
                Rule::unique('klasifikasi_ros', 'kode_klasifikasi')
                    ->ignore($klasifikasi_ro->id)
                    ->where(
                        fn($q) => $q
                            ->where('kegiatan_id', $validated['kegiatan_id'])
                            ->where('tahun_anggaran', $validated['tahun_anggaran'])
                    ),
            ],
        ]);

        $klasifikasi_ro->update($validated);

        return redirect()
            ->route('master.klasifikasi-ros.index')
            ->with('success', 'Klasifikasi RO berhasil diupdate.');
    }

    public function destroy(KlasifikasiRo $klasifikasi_ro)
    {
        $klasifikasi_ro->delete();

        return redirect()
            ->route('master.klasifikasi-ros.index')
            ->with('success', 'Klasifikasi RO berhasil dihapus.');
    }
}
