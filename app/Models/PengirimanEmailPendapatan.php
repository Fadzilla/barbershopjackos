<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanEmailPendapatan extends Model
{
    use HasFactory;

     protected $table = 'pengiriman_email'; // Nama tabel eksplisit

    protected $guarded = []; //semua kolom boleh di isi

    // relasi ke tabel pendapatan
    public function pendapatan()
    {
        return $this->belongsTo(Pendapatan::class, 'pendapatan_id');
    }
}
