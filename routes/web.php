<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return view('login');
});

// contoh route yang mengarah ke konten statis
Route::get('/selamat', function () {
    return view('selamat',['nama'=>'Farel Prayoga']);
});

// contoh route tanpa view, hanya controller
Route::get('/contoh1', [App\Http\Controllers\Contoh1Controller::class,'show']);

// contoh route tanpa view, hanya controller dengan membagi layout 
Route::get('/contoh2', [App\Http\Controllers\Contoh2Controller::class,'show']);

// contoh route coa
Route::get('/coa', [App\Http\Controllers\CoaController::class,'index']);

// login customer
Route::get('/depan', [App\Http\Controllers\KeranjangController::class, 'daftarbarang'])->middleware('pengguna')->name('depan');
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/logout', function () {
    Auth::logout(); 
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// untuk contoh perusahaan
use App\Http\Controllers\PerusahaanController;
Route::resource('perusahaan', PerusahaanController::class);
Route::get('/perusahaan/destroy/{id}', [PerusahaanController::class,'destroy']);

// untuk contoh ocr
use App\Http\Controllers\OcrController;

Route::get('/ocr', [OcrController::class, 'index'])->name('ocr.index');
Route::post('/ocr/process', [OcrController::class, 'process'])->name('ocr.process');
Route::post('/ocr/store', [OcrController::class, 'store'])->name('ocr.store');
// Halaman daftar tabel KTP
Route::get('/ktp/list', [OcrController::class, 'list'])->name('ktp.list');

// untuk ubah password
Route::get('/ubahpassword', [App\Http\Controllers\AuthController::class, 'ubahpassword'])
    ->middleware('pengguna')
    ->name('ubahpassword');
Route::post('/prosesubahpassword', [App\Http\Controllers\AuthController::class, 'prosesubahpassword'])
    ->middleware('pengguna')
;
// prosesubahpassword