<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianProduk extends Model
{
    use HasFactory;

    protected $table = 'pembelian_produks';

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | RELASI KE PEGAWAI
    |--------------------------------------------------------------------------
    */

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI KE PRODUK
    |--------------------------------------------------------------------------
    */

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}