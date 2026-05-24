<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// relasi
use App\Models\Retur;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $guarded = [];

    protected $fillable = [
    'nama_produk',
    'harga_jual',
    'stok', // <--- WAJIB ADA DI SINI
    // ...
];

    // Relasi ke detail pembelian produk
    public function detailPembelianProduk()
    {
       return $this->hasMany(DetailPembelianProduk::class);
    }
}
