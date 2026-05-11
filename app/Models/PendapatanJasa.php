<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendapatanJasa extends Model
{
    use HasFactory;

    protected $table = 'pendapatan_jasa';
    protected $guarded = [];

    public function pendapatan()
    {
        return $this->belongsTo(Penjualan::class, 'pendapatan_id');
    }

    public function pakets()
    {
        return $this->belongsTo(Barang::class, 'pakets_id');
    }
}
