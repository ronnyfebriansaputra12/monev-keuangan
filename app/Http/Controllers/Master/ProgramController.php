<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $satkerId = $request->input('satker_id');
        $tahun    = $request->input('tahun');
        $search   = $request->input('search');

        $q = Program::with('satker');

        if ($satkerId) $q->where('satker_id', (int) $satkerId);
        if ($tahun)    $q->where('tahun_anggaran', (int) $tahun);

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_program', 'like', "%{$search}%")
                    ->orWhere('kode_program', 'like', "%{$search}%");
            });
        }

        $programs = $q->orderBy('kode_program')->get();
        $satkers  = Satker::orderBy('nama_satker')->get();

        return view('master.programs.index', compact('programs', 'satkers', 'satkerId', 'tahun', 'search'));
    }

    public function create()
    {
        $satkers = Satker::orderBy('nama_satker')->get();
        return view('master.programs.create', compact('satkers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'satker_id'      => ['required', 'integer', 'exists:satkers,id'],
            'kode_program'   => ['required', 'string', 'max:50'],
            'nama_program'   => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_program' => [
                Rule::unique('programs', 'kode_program')
                    ->where(
                        fn($q) => $q
                            ->where('satker_id', $validated['satker_id'])
                            ->where('tahun_anggaran', $validated['tahun_anggaran'])
                    ),
            ],
        ]);

        Program::create($validated);

        return redirect()
            ->route('master.programs.index')
            ->with('success', 'Program berhasil ditambahkan.');
    }

    public function show(Program $program)
    {
        return redirect()->route('master.programs.edit', $program);
    }

    public function edit(Program $program)
    {
        $satkers = Satker::orderBy('nama_satker')->get();
        return view('master.programs.edit', compact('program', 'satkers'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'satker_id'      => ['required', 'integer', 'exists:satkers,id'],
            'kode_program'   => ['required', 'string', 'max:50'],
            'nama_program'   => ['required', 'string', 'max:255'],
            'tahun_anggaran' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $request->validate([
            'kode_program' => [
                Rule::unique('programs', 'kode_program')
                    ->ignore($program->id)
                    ->where(
                        fn($q) => $q
                            ->where('satker_id', $validated['satker_id'])
                            ->where('tahun_anggaran', $validated['tahun_anggaran'])
                    ),
            ],
        ]);

        $program->update($validated);

        return redirect()
            ->route('master.programs.index')
            ->with('success', 'Program berhasil diupdate.');
    }

    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()
            ->route('master.programs.index')
            ->with('success', 'Program berhasil dihapus.');
    }
}
