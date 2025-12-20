<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Master\{
    SatkerController,
    ProgramController,
    KegiatanController,
    KlasifikasiRoController,
    RincianOutputController,
    KomponenController,
    SubKomponenController,
    MasterAkunController,
    MakController,
    CoaItemController,
    PaguLineController
};

use App\Http\Controllers\Transaksi\{
    RealisasiHeaderController,
    RealisasiLineController,
    RealisasiAttachmentController
};

Route::middleware(['web'])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // MASTER DATA
    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('satkers', SatkerController::class);
        Route::resource('programs', ProgramController::class);
        Route::resource('kegiatans', KegiatanController::class);
        Route::resource('klasifikasi-ros', KlasifikasiRoController::class);
        Route::resource('rincian-outputs', RincianOutputController::class);
        Route::resource('komponens', KomponenController::class);
        Route::resource('sub-komponens', SubKomponenController::class);
        Route::resource('master-akuns', MasterAkunController::class);
        Route::resource('maks', MakController::class);
        Route::resource('coa-items', CoaItemController::class);
        Route::resource('pagu-lines', PaguLineController::class);
    });
    Route::get('master/coa-items/bulk-edit', [CoaItemController::class, 'bulkEdit'])
        ->name('master.coa-items.bulk-edit');

    Route::post('master/coa-items/bulk-update', [CoaItemController::class, 'bulkUpdate'])
        ->name('master.coa-items.bulk-update');


    // TRANSAKSI REALISASI
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::resource('realisasi-headers', RealisasiHeaderController::class);

        // Lines lebih enak nested biar jelas header mana
        Route::resource('realisasi-headers.lines', RealisasiLineController::class)
            ->shallow(); // biar route edit/delete line ga kepanjangan

        // attachment nested
        Route::resource('realisasi-headers.attachments', RealisasiAttachmentController::class)
            ->shallow();
    });
});
