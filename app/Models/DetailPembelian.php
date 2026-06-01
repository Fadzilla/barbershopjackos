<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;

    protected $table = 'detail_pembelian';
    protected $fillable = ['pembelian_produks_id','produk_id','qty','harga_per_unit',];

    public function pembelianProduk()
    {
        return $this->belongsTo(PembelianProduk::class, 'pembelian_produks_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
