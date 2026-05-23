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

    protected $fillable = [
        'nama_produk',
        'status',
        'stok',
        'harga_produk',
        'tanggal_masuk',
        'foto_produk',
        'deskripsi_produk',
    ];

    /**
     * Relasi ke retur
     */
    public function returs()
    {
        return $this->hasMany(Retur::class, 'produk_id');
  }
}
