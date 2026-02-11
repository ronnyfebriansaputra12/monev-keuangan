<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BudgetImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetImportController extends Controller
{
    public function showForm()
    {
        // Pastikan folder di resources/views adalah master/coa-items/import.blade.php
        return view('master.coa_items.import');
    }
    public function import(Request $request)
    {
        // 1. Validasi File yang Lebih Ketat
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240' // Batasi maksimal 10MB untuk mencegah RAM jebol
            ],
            'tahun' => 'required|digits:4|integer|min:2020|max:2099' // Validasi format tahun
        ]);

        DB::beginTransaction();
        try {
            $import = new BudgetImport($request->tahun);
            Excel::import($import, $request->file('file'));

            DB::commit();

            // Opsional: Logging aktivitas admin
            Log::info("Admin ID: " . auth()->id() . " berhasil import anggaran tahun " . $request->tahun);

            return back()->with('success', 'Data Anggaran Berhasil Diimpor!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            return back()->with('error', 'Gagal pada baris ke-' . $failures[0]->row() . ': ' . $failures[0]->errors()[0]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Jangan tampilkan pesan error mentah ke user di sistem produksi
            Log::error("Import Error: " . $e->getMessage());
            return back()->with('error', 'Gagal Impor: Format data tidak sesuai atau file korup.');
        }
    }
}
