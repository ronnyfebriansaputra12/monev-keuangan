<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SatkerController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $search = $request->input('search');

        $q = Satker::query();

        if ($tahun) $q->where('tahun_anggaran', (int)$tahun);

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_satker', 'like', "%{$search}%")
                    ->orWhere('kode_satker', 'like', "%{$search}%");
            });
        }

        $satkers = $q->orderBy('nama_satker')->get();

        return view('master.satkers.index', compact('satkers', 'tahun', 'search'));
    }

    public function create()
    {
        return view('master.satkers.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_satker' => [
                'required',
                'string',
                'max:50',
            ],
            'tahun_anggaran' => [
                'required',
                'integer',
                'between:2000,2100',
            ],
            'nama_satker' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        // 🔒 validasi kombinasi kode_satker + tahun_anggaran
        $request->validate([
            'kode_satker' => [
                Rule::unique('satkers')
                    ->where(fn($q) => $q->where('tahun_anggaran', $validated['tahun_anggaran'])),
            ],
        ]);

        Satker::create($validated);

        return redirect()
            ->route('master.satkers.index')
            ->with('success', 'Satker berhasil ditambahkan.');
    }



    public function show(Satker $satker)
    {
        return redirect()->route('master.satkers.edit', $satker);
    }

    public function edit(Satker $satker)
    {
        return view('master.satkers.edit', compact('satker'));
    }

    public function update(Request $request, Satker $satker)
    {
        $validated = $request->validate([
            'kode_satker' => [
                'required',
                'string',
                'max:50',
            ],
            'tahun_anggaran' => [
                'required',
                'integer',
                'between:2000,2100',
            ],
            'nama_satker' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $request->validate([
            'kode_satker' => [
                Rule::unique('satkers')
                    ->ignore($satker->id)
                    ->where(fn($q) => $q->where('tahun_anggaran', $validated['tahun_anggaran'])),
            ],
        ]);

        $satker->update($validated);

        return redirect()
            ->route('master.satkers.index')
            ->with('success', 'Satker berhasil diupdate.');
    }



    public function destroy(Satker $satker)
    {
        $satker->delete();

        return redirect()
            ->route('master.satkers.index')
            ->with('success', 'Satker berhasil dihapus.');
    }
}
