<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanProduk extends Model
{
    use HasFactory;

    protected $table = 'penjualan_produk';
    protected $fillable = ['penjualan_id', 'produk_id', 'harga_jual', 'jml', 'tgl'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}