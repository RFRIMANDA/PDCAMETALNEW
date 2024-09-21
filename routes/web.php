<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\ListKecilController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

//Dasboard HomeController
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

Route::middleware('auth')->group(function () {
//List Register ListController
Route::get('/listregister', [ListController::class, 'index'])->name('list.listregister');
Route::get('/list/create/{id}', [ListController::class, 'create'])->name('list.create');
Route::post('/list/store', [ListController::class, 'store'])->name('list.store');
Route::get('/list/{id}/edit', [ListController::class, 'edit'])->name('list.edit');
Route::put('/list/{id}', [ListController::class, 'update'])->name('list.update');
Route::get('/tablelist/{id}', [ListController::class, 'tablelistawal'])->name('list.tablelistawal');
Route::get('/tablelist', [ListController::class, 'tablelist'])->name('list.tablelist');
Route::get('/biglist', [ListController::class, 'biglist'])->name('list.biglist'); // No parameter
Route::get('logout', [ListController::class, 'logout'])->name('logout');
});

//login regist
// Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/register-act', [UserController::class, 'register_action'])->name('register.action');
Route::get('login', [UserController::class, 'login'])->name('login');
Route::post('login', [UserController::class, 'login_action'])->name('login.action');
Route::get('password', [UserController::class, 'password'])->name('password')->middleware('auth');
Route::post('password', [UserController::class, 'password_action'])->name('password.action')->middleware('auth');
Route::get('logout', [UserController::class, 'logout'])->name('logout');

Route::middleware('admin')->group(function () {
//admin kelola user
Route::get('/kelolaakun', [AdminController::class, 'index'])->name('admin.kelolaakun');
Route::get('/admin/create', [AdminController::class, 'create'])->name('admin.create')->middleware('admin');
Route::post('/admin/store', [AdminController::class, 'store'])->name('admin.store')->middleware('admin');
Route::get('/admin/{id}/edit', [AdminController::class, 'edit'])->name('admin.edit')->middleware('admin');
Route::put('/admin/{id}', [AdminController::class, 'update'])->name('admin.update')->middleware('admin');
Route::delete('/admin/{id}', [AdminController::class, 'destroy'])->name('admin.destroy')->middleware('admin');
});


// Route untuk menampilkan halaman index ListKecil
// Route::get('/listkecil/index/{id}/{index}', [ListKecilController::class, 'index'])->name('listkecil.index');
/// Route untuk mengupdate detail ListKecil via POST
Route::post('/listkecil/{id}/update-detail', [ListKecilController::class, 'updateDetail'])->name('listkecil.update-detail');
Route::get('/listkecil/{id}', [ListKecilController::class, 'detail'])->name('listkecil.detail');
// Route untuk menampilkan detail ListKecil
Route::get('/listkecil/show/{id}', [ListKecilController::class, 'show'])->name('listkecil.show');
// Route untuk menampilkan form edit ListKecil
Route::get('/listkecil/{id}/edit/{index}', [ListKecilController::class, 'edit'])->name('listkecil.edit');
Route::post('/listkecil/update/{id}', [ListKecilController::class, 'update'])->name('listkecil.update');
Route::get('/listkecil/{id}/detail', [ListKecilController::class, 'getDetail']);

//PRINT
Route::get('/list/print/{id}', [ListController::class, 'print'])->name('list.print');




