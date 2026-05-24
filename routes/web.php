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
    // return view('login');
    return redirect('/login');
});

// proses pengiriman email
use App\Http\Controllers\PengirimanEmailReturController;
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailReturController::class, 'proses_kirim_email_pembayaran']);

// contoh route yang mengarah ke konten statis
Route::get('/selamat', function () {
    return view('selamat',['nama'=>'Farel Prayoga']);
});

// contoh route yang mengarah ke konten statis
Route::get('/utama', function () {
    return view('layout',['nama'=>'Farel Prayoga','title'=>'Selamat Datang']);
});

// untuk ubah password
Route::get('/ubahpassword', [App\Http\Controllers\AuthController::class, 'ubahpassword'])
    ->middleware('customer')
    ->name('ubahpassword');
Route::post('/prosesubahpassword', [App\Http\Controllers\AuthController::class, 'prosesubahpassword'])
    ->middleware('customer')
;

//proses kirim email konfirmasi pemakaian
use     App\Http\Controllers\PDFController;
Route::get('/pemakaianpdf', [PDFController::class, 'pemakaianpdf']);

//proses pengiriman email
use App\Http\Controllers\PengirimanEmailPemakaianController;
Route::get('/pengiriman_email_pemakaian', [PengirimanEmailPemakaianController::class, 'proses_kirim_email_pemakaian']);
// prosesubahpassword

// proses pengiriman email
use App\Http\Controllers\PengirimanEmailPendapatanController;
Route::get('/proses_kirim_email_pembayaran_jasa', [PengirimanEmailPendapatanController::class, 'proses_kirim_email_pembayaran_jasa']);

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
Route::get('/midtrans', [MidtransController::class, 'midtranscallback']);

Route::get('/midtrans/pembayaran/{token}', function ($token) {
    return view('midtrans.pembayaran', ['snapToken' => $token]);
})->name('midtrans.pembayaran');

// Route untuk menerima laporan dari Midtrans (Callback) sesuai 8.7
Route::post('/midtrans/callback', [MidtransController::class, 'handleCallback']);

// proses pengiriman email
use App\Http\Controllers\PengirimanEmailPembelianController;
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailPembelianController::class, 'proses_kirim_email_pembayaran']);


Route::get('/paket-pdf', [PDFController::class, 'paketPdf']);
