<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk'; // Nama tabel eksplisit
    protected $guarded = []; // Mengizinkan semua field diisi
}

    protected $table = 'produks';

    protected $guarded = [];

    // Relasi ke detail pembelian produk
    public function detailPembelianProduks()
    {
        return $this->hasMany(DetailPembelianProduk::class);
    }
}
