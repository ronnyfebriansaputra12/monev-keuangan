<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Mak;
use App\Models\MasterAkun;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MakController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $q = Mak::with('akun');

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_mak', 'like', "%{$search}%")
                    ->orWhere('jenis_belanja', 'like', "%{$search}%")
                    ->orWhereHas('akun', function ($a) use ($search) {
                        $a->where('kode_akun', 'like', "%{$search}%")
                            ->orWhere('nama_akun', 'like', "%{$search}%");
                    });
            });
        }

        $maks = $q->orderBy('akun_id')->get();

        return view('master.maks.index', compact('maks', 'search'));
    }

    public function create()
    {
        $akuns = MasterAkun::orderBy('kode_akun')->get();
        return view('master.maks.create', compact('akuns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'akun_id' => ['required', 'integer', 'exists:master_akuns,id'],
            'nama_mak' => ['required', 'string', 'max:255'],
            'jenis_belanja' => ['nullable', 'string', 'max:50'],
        ]);

        // 1 akun hanya boleh punya 1 MAK (meniru unique kode_mak dulu)
        $request->validate([
            'akun_id' => [
                Rule::unique('maks', 'akun_id'),
            ],
        ]);

        Mak::create($validated);

        return redirect()
            ->route('master.maks.index')
            ->with('success', 'MAK berhasil ditambahkan.');
    }

    public function show(Mak $mak)
    {
        return redirect()->route('master.maks.edit', $mak);
    }

    public function edit(Mak $mak)
    {
        $akuns = MasterAkun::orderBy('kode_akun')->get();
        return view('master.maks.edit', compact('mak', 'akuns'));
    }

    public function update(Request $request, Mak $mak)
    {
        $validated = $request->validate([
            'akun_id' => ['required', 'integer', 'exists:master_akuns,id'],
            'nama_mak' => ['required', 'string', 'max:255'],
            'jenis_belanja' => ['nullable', 'string', 'max:50'],
        ]);

        $request->validate([
            'akun_id' => [
                Rule::unique('maks', 'akun_id')->ignore($mak->id),
            ],
        ]);

        $mak->update($validated);

        return redirect()
            ->route('master.maks.index')
            ->with('success', 'MAK berhasil diupdate.');
    }

    public function destroy(Mak $mak)
    {
        $mak->delete();

        return redirect()
            ->route('master.maks.index')
            ->with('success', 'MAK berhasil dihapus.');
    }
}
