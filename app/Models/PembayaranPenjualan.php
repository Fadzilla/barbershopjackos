<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPenjualan extends Model
{
    use HasFactory;

    // tambahan penyebutan tabel secara eksplisit
    protected $table = 'pembayaran_penjualan'; // Nama tabel eksplisit
    // proteksi kolom tabel (tidak ada yg diproteksi)
    protected $guarded = [];
}
