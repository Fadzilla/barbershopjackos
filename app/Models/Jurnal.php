<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table = 'jurnal';
    
    protected $guarded = []; //agar seluruh kolom dapat dimodifikasi

    // relasi ke jurnal detail 1-N
    public function jurnaldetail()
    {
        return $this->hasMany(JurnalDetail::class);
    }

    public function isBalanced()
    {
        $debit = $this->jurnaldetail->sum('debit');
        $credit = $this->jurnaldetail->sum('credit');
        return $debit == $credit;
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