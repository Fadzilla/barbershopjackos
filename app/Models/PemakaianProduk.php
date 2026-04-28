<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemakaianProduk extends Model
{
    use HasFactory;

    protected $table = 'pemakaian_produk';

    protected $fillable = [
        'pemakaian_id',
        'produk_id',
        'harga',
        'qty'
    ];

    // RELASI KE PEMAKAIAN
    public function pemakaian()
    {
        return $this->belongsTo(Pemakaian::class, 'pemakaian_id');
    }

    // RELASI KE PRODUK
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
