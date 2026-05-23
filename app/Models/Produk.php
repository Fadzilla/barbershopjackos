<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// relasi
use App\Models\Retur;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produks'; // Nama tabel eksplisit
    protected $guarded = []; // Mengizinkan semua field diisi


// Relasi dengan tabel relasi many to many nya
    public function pendapatanJasa()
    {
        return $this->hasMany(PendapatanJasa::class, 'pakets_id');
    }
}
