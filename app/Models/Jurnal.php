<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table = 'jurnal';
    
    protected $fillable = [
        'no_jurnal',
        'tanggal',
        'no_ref',
        'sumber',
        'sumber_id',
        'keterangan'
    ];

    public function details()
    {
        return $this->hasMany(JurnalDetail::class);
    }

    // Relasi polymorphic ke sumber transaksi
    public function sumberTransaksi()
    {
        return match($this->sumber) {
            'pendapatan' => $this->belongsTo(Pendapatan::class, 'sumber_id'),
            'penjualan' => $this->belongsTo(Penjualan::class, 'sumber_id'),
            'pembelian' => $this->belongsTo(PembelianProduk::class, 'sumber_id'),
            'pemakaian' => $this->belongsTo(Pemakaian::class, 'sumber_id'),
            'retur' => $this->belongsTo(Retur::class, 'sumber_id'),
            default => null,
        };
    }
}