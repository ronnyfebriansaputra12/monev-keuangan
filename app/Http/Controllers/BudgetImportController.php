<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BudgetImport;
use Illuminate\Support\Facades\DB;

class BudgetImportController extends Controller
{
    public function showForm()
    {
        // Pastikan folder di resources/views adalah master/coa-items/import.blade.php
        return view('master.coa_items.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'tahun' => 'required|numeric'
        ]);

        DB::beginTransaction();
        try {
            Excel::import(new BudgetImport($request->tahun), $request->file('file'));
            DB::commit();
            return back()->with('success', 'Data Anggaran Berhasil Diimpor!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal Impor: ' . $e->getMessage());
        }
    }
}
