<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $programId = $request->input('program_id');
        $tahun = $request->input('tahun');
        $search = $request->input('search');

        $q = Kegiatan::with('program.satker');

        if ($programId) $q->where('program_id', (int)$programId);
        if ($tahun) $q->where('tahun_anggaran', (int)$tahun);

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('kode_kegiatan', 'like', "%{$search}%");
            });
        }

        $kegiatans = $q->orderBy('kode_kegiatan')->get();
        $programs = Program::with('satker')->orderBy('kode_program')->get();

        return view('master.kegiatans.index', compact('kegiatans', 'programs', 'programId', 'tahun', 'search'));
    }

    public function create()
    {
        $programs = Program::with('satker')->orderBy('kode_program')->get();
        return view('master.kegiatans.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'kode_kegiatan' => ['required', 'string', 'max:50'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_kegiatan' => [
                Rule::unique('kegiatans', 'kode_kegiatan')
                    ->where(fn($q) => $q->where('program_id', $validated['program_id'])
                        ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        Kegiatan::create($validated);

        return redirect()->route('master.kegiatans.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Kegiatan $kegiatan)
    {
        return redirect()->route('master.kegiatans.edit', $kegiatan);
    }

    public function edit(Kegiatan $kegiatan)
    {
        $programs = Program::with('satker')->orderBy('kode_program')->get();
        return view('master.kegiatans.edit', compact('kegiatan', 'programs'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'kode_kegiatan' => ['required', 'string', 'max:50'],
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_kegiatan' => [
                Rule::unique('kegiatans', 'kode_kegiatan')
                    ->ignore($kegiatan->id)
                    ->where(fn($q) => $q->where('program_id', $validated['program_id'])
                        ->where('tahun_anggaran', $validated['tahun_anggaran']))
            ],
        ]);

        $kegiatan->update($validated);

        return redirect()->route('master.kegiatans.index')->with('success', 'Kegiatan berhasil diupdate.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $kegiatan->delete();
        return redirect()->route('master.kegiatans.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
