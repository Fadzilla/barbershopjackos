<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanEmailPemakaian extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_email'; // Nama tabel eksplisit

    protected $guarded = []; //semua kolom boleh di isi

    // relasi ke tabel pemakaian
    public function pemakaian()
    {
        return $this->belongsTo(Pemakaian::class, 'pemakaian_id');
    }
}
   

