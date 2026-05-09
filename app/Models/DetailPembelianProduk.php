<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PembelianProduk;
use App\Models\Produk;

class DetailPembelianProduk extends Model
{
    protected $fillable = [
        'pembelian_produk_id',
        'produk_id',
        'qty',
        'harga_satuan',
        'subtotal',
    ];

    public function pembelianProduk()
    {
        return $this->belongsTo(PembelianProduk::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}