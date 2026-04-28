<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfirmasiPemakaian extends Model
{
    use HasFactory;

    protected $table = 'konfirmasi_pemakaian';

    protected $guarded = [];

    public function pemakaian()
    {
        return $this->belongsTo(Pemakaian::class, 'pemakaian_id');
    }
}