<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanProduk extends Model
{
    protected $table = 'penjualan_produk';    
    protected $fillable = ['penjualan_id', 'produk_id', 'harga_produk', 'harga_jual', 'jml', 'tgl'];

    protected static function booted()
    {
        // Setiap kali ada item produk terjual (Insert ke tabel penjualan_produk)
        static::created(function ($item) {
            $produk = $item->produk;
            if ($produk) {
                // Kurangi stok produk berdasarkan jumlah (jml) yang dibeli
                $produk->decrement('stok', $item->jml);
            }
        });
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}