<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\ResikoController;
use App\Http\Controllers\RealisasiController;
use App\Http\Controllers\PpkController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\ExportController;
use App\Exports\RiskOpportunityPdfExport;
use App\Exports\PpkExport;
use App\Exports\RiskOpportunityExport;
use App\Models\Kriteria;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Ppk;
use App\Models\Realisasi;
use Illuminate\Support\Facades\Route;

// Dasboard HomeController
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('auth');

// login regist
Route::post('/register-act', [UserController::class, 'register_action'])->name('register.action');
Route::get('login', [UserController::class, 'login'])->name('login');
Route::post('login', [UserController::class, 'login_action'])->name('login.action');
Route::get('password', [UserController::class, 'password'])->name('password')->middleware('auth');
Route::post('password', [UserController::class, 'password_action'])->name('password.action')->middleware('auth');
Route::get('logout', [UserController::class, 'logout'])->name('logout');

Route::middleware('admin','manajemen')->group(function () {
    // Admin kelola user
    Route::get('/kelolaakun', [AdminController::class, 'index'])->name('admin.kelolaakun');
    Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/admin/{id}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::get('/divisi', [AdminController::class, 'divisi'])->name('admin.divisi');
    Route::get('/admin/divisi/create', [AdminController::class, 'createDivisi'])->name('admin.divisi.create');
    Route::post('/admin/divisi/store', [AdminController::class, 'storeDivisi'])->name('admin.divisi.store');
    // Route untuk Edit Divisi
    Route::get('/admin/divisi/{id}/edit', [AdminController::class, 'editDivisi'])->name('admin.divisi.edit');
    Route::put('/admin/divisi/{id}', [AdminController::class, 'updateDivisi'])->name('admin.divisi.update');
    // Route untuk Hapus Divisi
    Route::delete('/admin/divisi/{id}', [AdminController::class, 'destroyDivisi'])->name('admin.divisi.destroy');

// Kriteria
Route::get('/kriteria', [KriteriaController::class, 'index'])->name('admin.kriteria');
Route::get('/kriteriacreate', [KriteriaController::class, 'create'])->name('admin.kriteriacreate');
Route::post('/kriteriacreate', [KriteriaController::class, 'store'])->name('admin.kriteriastore');
Route::get('/kriteria/{id}/edit', [KriteriaController::class, 'edit'])->name('admin.kriteriaedit');
Route::put('/kriteria/{id}', [KriteriaController::class, 'update'])->name('admin.kriteria.update');
Route::delete('/kriteria/{id}', [KriteriaController::class, 'destroy'])->name('admin.kriteriadestroy');
});

// Risk
Route::middleware(['checkrole:manager,manajemen,supervisor'])->group(function () {
    Route::get('/bigrisk', [RiskController::class, 'biglist'])->name('riskregister.biglist');
});
Route::middleware(['checkrole:admin,manager,manajemen,supervisor'])->group(function () {
    Route::get('/riskregister/create/{id}', [RiskController::class, 'create'])->name('riskregister.create');
    Route::get('/riskregister/{id}/edit', [RiskController::class, 'edit'])->name('riskregister.edit');
    Route::post('/riskregister/store', [RiskController::class, 'store'])->name('riskregister.store');
    Route::put('/riskregister/update/{id}', [RiskController::class, 'update'])->name('riskregister.update');
    Route::get('/riskregister/preview/{id}', [RiskController::class, 'preview'])->name('riskregister.preview');
    Route::get('/riskregister/printAll/{id}', [RiskController::class, 'printAll'])->name('riskregister.printAll');
    Route::get('/riskregister/export/{id}', [RiskController::class, 'exportExcel'])->name('riskregister.exportExcel');
    Route::get('/riskregister/export-filtered/{id}', [RiskController::class, 'exportFilteredExcel'])->name('riskregister.exportFilteredExcel');
    Route::get('/export-pdf/{id}', [RiskController::class, 'exportFilteredPDF'])->name('riskregister.export-pdf');
    Route::delete('/riskregister/{id}', [RiskController::class, 'destroy'])->name('riskregister.destroy');
});
Route::get('/riskregister', [RiskController::class, 'index'])->name('riskregister.index');
Route::get('/riskregister/{id}', [RiskController::class, 'tablerisk'])->name('riskregister.tablerisk');


// Resiko
Route::middleware('manager','manajemen','supervisor')->group(function () {
    Route::get('/resiko/{id}', [ResikoController::class, 'index'])->name('resiko.index');
    Route::get('/resiko/create/{id}', [ResikoController::class, 'create'])->name('resiko.create');
    Route::post('/resiko/store', [ResikoController::class, 'store'])->name('resiko.store');
    Route::get('/resiko/{id}/edit', [ResikoController::class, 'edit'])->name('resiko.edit');
    Route::post('/resiko/{id}/update', [ResikoController::class, 'update'])->name('resiko.update');
    Route::get('/resiko/matriks/{id}', [ResikoController::class, 'matriks'])->name('resiko.matriks');
    Route::get('/matriks-risiko/{id}', [ResikoController::class, 'show'])->name('matriks-risiko.show');
});

// Realisasi
Route::middleware('auth')->group(function () {
    Route::get('/realisasi/{id}', [RealisasiController::class, 'index'])->name('realisasi.index');
    Route::get('/realisasi/{id}/edit', [RealisasiController::class, 'edit'])->name('realisasi.edit');
    Route::post('/realisasi/store', [RealisasiController::class, 'store'])->name('realisasi.store');
    Route::put('/realisasi/{id}/update', [RealisasiController::class, 'update'])->name('realisasi.update');
    Route::delete('/realisasi/{id}/destroy', [RealisasiController::class, 'destroy'])->name('realisasi.destroy');
    Route::get('/realisasi/{id}/detail', [RealisasiController::class, 'getDetail'])->name('realisasi.detail');
});

Route::middleware('manager','manajemen')->group(function () {
// --PPK--//
Route::get('/ppk', [PpkController::class, 'index'])->name('ppk.index');
Route::get('/formppk', [PpkController::class, 'create'])->name('ppk.create');
Route::post('/form/store', [PpkController::class, 'store'])->name('ppk.store');
Route::get('/ppk/{id}/edit', [PpkController::class, 'edit'])->name('ppk.edit');
Route::put('/ppk/{id}', [PpkController::class, 'update'])->name('ppk.update');
Route::get('/formidentifikasi/{id}', [PpkController::class, 'create2'])->name('ppk.formppkkedua');
Route::post('/ppk/store-formppkkedua', [PpkController::class, 'storeFormPpkkedua'])->name('ppk.storeformppkkedua');
Route::get('/ppk/export/{id}', [PpkController::class, 'exportSingle'])->name('ppk.export');
});


