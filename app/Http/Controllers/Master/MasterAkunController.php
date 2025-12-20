<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterAkun;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterAkunController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $q = MasterAkun::query();

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('kode_akun', 'like', "%{$search}%")
                    ->orWhere('nama_akun', 'like', "%{$search}%");
            });
        }

        $items = $q->orderBy('kode_akun')->get();

        return view('master.akuns.index', compact('items', 'search'));
    }

    public function create()
    {
        return view('master.akuns.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_akun' => ['required', 'string', 'max:20', 'regex:/^[0-9A-Za-z\.\-]+$/'],
            'nama_akun' => ['required', 'string', 'max:255'],
        ]);

        $request->validate([
            'kode_akun' => [
                Rule::unique('master_akuns', 'kode_akun'),
            ],
        ]);

        MasterAkun::create($validated);

        return redirect()->route('master.master-akuns.index')
            ->with('success', 'Master Akun berhasil ditambahkan.');
    }

    public function show(MasterAkun $master_akun)
    {
        return redirect()->route('master.akuns.edit', $master_akun);
    }

    public function edit(MasterAkun $master_akun)
    {
        return view('master.akuns.edit', compact('master_akun'));
    }

    public function update(Request $request, MasterAkun $master_akun)
    {
        $validated = $request->validate([
            'kode_akun' => ['required', 'string', 'max:20', 'regex:/^[0-9A-Za-z\.\-]+$/'],
            'nama_akun' => ['required', 'string', 'max:255'],
        ]);

        $request->validate([
            'kode_akun' => [
                Rule::unique('master_akuns', 'kode_akun')->ignore($master_akun->id),
            ],
        ]);

        $master_akun->update($validated);

        return redirect()->route('master.master-akuns.index')
            ->with('success', 'Master Akun berhasil diupdate.');
    }

    public function destroy(MasterAkun $master_akun)
    {
        $master_akun->delete();

        return redirect()->route('master.master-akuns.index')
            ->with('success', 'Master Akun berhasil dihapus.');
    }
}
