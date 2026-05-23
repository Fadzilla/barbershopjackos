<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $guarded = [];

    // Relasi ke detail pembelian produk
    public function detailPembelianProduk()
    {
        return $this->hasMany(DetailPembelianProduk::class);
    }
}
