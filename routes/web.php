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
// prosesubahpassword

// proses pengiriman email
use App\Http\Controllers\PengirimanEmailPendapatanController;
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailPendapatanController::class, 'proses_kirim_email_pembayaran']);


// contoh menggunakan callback
use App\Http\Controllers\CobaMidtransController;

// proses pengiriman email
use App\Http\Controllers\PengirimanEmailPembelianController;
Route::get('/proses_kirim_email_pembayaran', [PengirimanEmailPembelianController::class, 'proses_kirim_email_pembayaran']);
use App\Http\Controllers\PDFController;

Route::get('/paket-pdf', [PDFController::class, 'paketPdf']);
