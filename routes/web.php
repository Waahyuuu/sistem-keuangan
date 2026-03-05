<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| MAIN MENU (HARUS LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | MASTER DATA
    |--------------------------------------------------------------------------
    */

    Route::get('/master-data', [MasterDataController::class, 'index'])
        ->name('master.data');

    /*
    |--------------------------------------------------------------------------
    | REKENING (MASTER DATA - TAB REKENING)
    |--------------------------------------------------------------------------
    */

    Route::post('/rekening', [MasterDataController::class, 'storeRekening'])
        ->name('rekening.store');

    Route::put('/rekening/{id}', [MasterDataController::class, 'updateRekening'])
        ->name('rekening.update');

    Route::delete(
        '/rekening/{id}',
        [MasterDataController::class, 'deleteRekening']
    )->name('rekening.delete');

    /*
    |--------------------------------------------------------------------------
    | KANTOR (MASTER DATA - TAB KANTOR)
    |--------------------------------------------------------------------------
    */

    Route::post('/kantor/store', [MasterDataController::class, 'storeKantor'])
        ->name('kantor.store');

    Route::put('/kantor/{id}/update', [MasterDataController::class, 'updateKantor'])
        ->name('kantor.update');

    Route::delete('/kantor/{id}/delete', [MasterDataController::class, 'deleteKantor'])
        ->name('kantor.delete');

    /*
    |--------------------------------------------------------------------------
    | DEPARTEMEN (MASTER DATA - TAB DEPARTEMEN)
    |--------------------------------------------------------------------------
    */

    Route::post('/departemen/store', [MasterDataController::class, 'storeDepartemen'])
        ->name('departemen.store');

    Route::put('/departemen/{id}/update', [MasterDataController::class, 'updateDepartemen'])
        ->name('departemen.update');

    Route::delete('/departemen/{id}/delete', [MasterDataController::class, 'deleteDepartemen'])
        ->name('departemen.delete');

    /*
    |--------------------------------------------------------------------------
    | SUB-DEPARTEMEN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/master-data/departemen/{departemen}/sub',
        [MasterDataController::class, 'subDepartemen']
    )->name('departemen.sub');

    Route::post(
        '/master-data/departemen/{departemen}/sub',
        [MasterDataController::class, 'storeSubDepartemen']
    )->name('departemen.sub.store');

    Route::put(
        '/master-data/departemen/sub/{departemen}',
        [MasterDataController::class, 'updateSubDepartemen']
    )->name('departemen.sub.update');

    Route::delete(
        '/master-data/departemen/sub/{departemen}',
        [MasterDataController::class, 'deleteSubDepartemen']
    )->name('departemen.sub.delete');

    /*
    |--------------------------------------------------------------------------
    | PROGRAM (MASTER DATA - TAB PROGRAM)
    |--------------------------------------------------------------------------
    */

    Route::post('/program/store', [MasterDataController::class, 'storeProgram'])
        ->name('program.store');

    Route::put('/program/{program}/update', [MasterDataController::class, 'updateProgram'])
        ->name('program.update');

    Route::delete('/program/{program}/delete', [MasterDataController::class, 'deleteProgram'])
        ->name('program.delete');

    /*
    |--------------------------------------------------------------------------
    | KATEGORI (MASTER DATA - TAB KATEGORI)
    |--------------------------------------------------------------------------
    */

    Route::post('/kategori/store', [MasterDataController::class, 'storeKategori'])
        ->name('kategori.store');

    Route::put('/kategori/{kategori}/update', [MasterDataController::class, 'updateKategori'])
        ->name('kategori.update');

    Route::delete('/kategori/{kategori}/delete', [MasterDataController::class, 'deleteKategori'])
        ->name('kategori.delete');

    /*
    |--------------------------------------------------------------------------
    | TRANSAKSI
    |--------------------------------------------------------------------------
    */

    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');

    /*
    |--------------------------------------------------------------------------
    | LAPORAN
    |--------------------------------------------------------------------------
    */

    Route::get('/laporan', [LaporanController::class, 'index'])
        ->name('laporan');

    /*
    |--------------------------------------------------------------------------
    | PENGATURAN AKUN
    |--------------------------------------------------------------------------
    */

    Route::get('/pengaturan-akun', [ProfileController::class, 'index'])
        ->name('pengaturan');

    Route::post('/pengaturan-akun/store', [ProfileController::class, 'store'])
        ->name('pengguna.store');

    Route::put('/pengaturan-akun/{id}/update', [ProfileController::class, 'update'])
        ->name('pengguna.update');

    /*
    |--------------------------------------------------------------------------
    | PENGATURAN TOGGLE AKTIF/NON-AKTIF
    |--------------------------------------------------------------------------
    */

    Route::patch(
        '/rekening/{id}/toggle',
        [MasterDataController::class, 'toggleRekening']
    )->name('rekening.toggle');
});
