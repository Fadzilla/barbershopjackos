<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemakaianInsight extends Model
{
    use HasFactory;

    // KUNCINYA DI SINI: Paksa Laravel pakai nama tabel yang ada di phpMyAdmin Anda (tanpa 's')
    protected $table = 'pemakaian_insight';

    protected $fillable = [
        'pemakaian_id',
        'nama_produk',
        'jumlah_pemakaian',
        'analisis_ai'
    ];

    public function pemakaian()
    {
        // Karena nama tabel utamanya 'pemakaian', kita set foreign key dan owner key-nya sekalian biar aman
        return $this->belongsTo(Pemakaian::class, 'pemakaian_id', 'id');
    }
}