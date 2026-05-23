<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaketController;

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

Route::resource('paket', PaketController::class);

// login pengguna
Route::get('/dashboard', [App\Http\Controllers\UserDashboardController::class, 'index'])->middleware('pengguna')->name('dashboard');
Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// untuk ubah password
Route::get('/ubahpassword', [App\Http\Controllers\AuthController::class, 'ubahpassword'])
    ->middleware('pengguna')
    ->name('ubahpassword');
Route::post('/prosesubahpassword', [App\Http\Controllers\AuthController::class, 'prosesubahpassword'])
    ->middleware('pengguna')
;

//proses kirim email konfirmasi pemakaian
use     App\Http\Controllers\PDFController;
Route::get('/pemakaianpdf', [PDFController::class, 'pemakaianpdf']);

//proses pengiriman email
use App\Http\Controllers\PengirimanEmailPemakaianController;
Route::get('/pengiriman_email_pemakaian', [PengirimanEmailPemakaianController::class, 'proses_kirim_email_pemakaian']);