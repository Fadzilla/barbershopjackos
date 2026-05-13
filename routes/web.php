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
    ->middleware('pengguna');

// proses pengiriman email
use App\Http\Controllers\PengirimanEmailController;
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailController::class, 'proses_kirim_email_pembayaran']);

use App\Http\Controllers\PengirimanEmailPenjualanController;
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailController::class, 'proses_kirim_email_pembayaran']);

// untuk tes apriori
use App\Http\Controllers\AprioriTestController;
Route::get('/test-apriori', [AprioriTestController::class, 'test']);
Route::get('/test-apriori-2', [AprioriTestController::class, 'tes2']);

// contoh sampel sederhana untuk mengetes midtrans
Route::get('/midtrans', [App\Http\Controllers\MidtransController::class, 'midtrans']);

// contoh menggunakan callback
use App\Http\Controllers\MidtransController;
// Route untuk menampilkan halaman tombol bayar & simulasi
Route::get('/cek-midtrans', [MidtransController::class, 'midtranscallback']);
// Route untuk menampilkan halaman tombol bayar & simulasi
Route::get('/midtrans', [MidtransController::class, 'midtranscallback']);

Route::get('/midtrans/pembayaran/{token}', function ($token) {
    return view('midtrans.pembayaran', ['snapToken' => $token]);
})->name('midtrans.pembayaran');

// Route untuk menerima laporan dari Midtrans (Callback) sesuai 8.7
Route::post('/midtrans/callback', [MidtransController::class, 'handleCallback']);
// untuk autorefresh pembayaran
Route::get('/cek_status_pembayaran_pg', [App\Http\Controllers\KeranjangController::class, 'cek_status_pembayaran_pg']);