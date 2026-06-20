<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemakaianProduk extends Model
{
    use HasFactory;

    protected $table = 'pemakaian_produk';
    protected $fillable = ['pemakaian_id','produk_id','jumlah','tanggal_pakai','keterangan',];

    public function pemakaian()
    {
        return $this->belongsTo(Pemakaian::class, 'pemakaian_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    protected static function booted()
{
    static::created(function ($pemakaian) {
        $jurnalService = app(\App\Services\JurnalOtomatisService::class);
        $jurnalService->dariPemakaian($pemakaian);
    });
}
}