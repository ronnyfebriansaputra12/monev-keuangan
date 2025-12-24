<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
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
use App\Http\Controllers\Transaksi\Realisasi\RealisasiController;
use App\Http\Controllers\RealisasiV2\RealisasiControllerV2;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard Utama (Satu Route)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [LoginController::class, 'edit'])->name('profile.edit');

    // Menangani update data profil dan password
    Route::put('/profile', [LoginController::class, 'update'])->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | MASTER DATA (Akses Global)
    |--------------------------------------------------------------------------
    */
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

        Route::get('coa-items-bulk/edit', [CoaItemController::class, 'bulkEdit'])->name('coa-items.bulk-edit');
        Route::post('coa-items-bulk/update', [CoaItemController::class, 'bulkUpdate'])->name('coa-items.bulk-update');
    });

    /*
    |--------------------------------------------------------------------------
    | TRANSAKSI REALISASI (Akses Global Tanpa Prefix Role)
    |--------------------------------------------------------------------------
    */
    Route::prefix('transaksi')->name('realisasi.')->group(function () {
        // Resource untuk header, lines, dan attachment
        Route::resource('realisasi-headers', RealisasiHeaderController::class);
        Route::resource('realisasi-headers.lines', RealisasiLineController::class)->shallow();
        Route::resource('realisasi-headers.attachments', RealisasiAttachmentController::class)->shallow();

        // Route Tambahan Realisasi
        Route::get('/index', [RealisasiController::class, 'index'])->name('index'); // Ini jadi realisasi.index
        Route::get('/create', [RealisasiController::class, 'create'])->name('create');
        Route::post('/store', [RealisasiController::class, 'store'])->name('store');
        Route::get('/{id}', [RealisasiController::class, 'show'])->name('show');

        // Finalisasi (Akses tetap satu URL, validasi role nanti di Controller)
        Route::post('/{id}/finalize', [RealisasiController::class, 'finalize'])->name('finalize');
    });

    Route::patch('/realisasi-v2/{id}/return', [RealisasiControllerV2::class, 'returnToPlo'])->name('realisasi-v2.return');
    Route::resource('realisasi-v2', RealisasiControllerV2::class);
    // Route::get('realisasi-v2/get-next-no-urut', [App\Http\Controllers\RealisasiV2\RealisasiControllerV2::class, 'getNextNoUrut']);
    Route::get('realisasi-v2/get-next-no-urut', [App\Http\Controllers\RealisasiV2\RealisasiControllerV2::class, 'getNextNoUrut'])->name('realisasi-v2.get-no-urut');
    // Aksi Verifikator (Setujui ke Bendahara & Tolak ke PLO)
    Route::patch('/realisasi-v2/{id}/approve', [RealisasiControllerV2::class, 'approve'])->name('realisasi-v2.approve');
    Route::patch('/realisasi-v2/{id}/reject', [RealisasiControllerV2::class, 'reject'])->name('realisasi-v2.reject');

    // Aksi PPSPM (Perbaikan error image_cc0051.png)
    Route::patch('/realisasi-v2/{id}/verify-ppspm', [RealisasiControllerV2::class, 'verifyPpspm'])->name('realisasi-v2.verify-ppspm');
    // Aksi Bendahara (Finalisasi & Balik ke Verifikator)
    Route::patch('/realisasi-v2/{id}/finalize', [RealisasiControllerV2::class, 'finalize'])->name('realisasi-v2.finalize');
    Route::patch('/realisasi-v2/{id}/return-verif', [RealisasiControllerV2::class, 'returnToVerifikator'])->name('realisasi-v2.return-verif'); // TAMBAHKAN INI
    // Aksi PPK (Hanya Update Status)
    Route::patch('/realisasi-v2/{id}/verify-ppk', [RealisasiControllerV2::class, 'verifyPpk'])->name('realisasi-v2.verify-ppk');
});
