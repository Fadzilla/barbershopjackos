<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianProduk extends Model
{
    use HasFactory;

    protected $table = 'pembelian_produks';

    protected $fillable = ['pegawai_id', 'no_faktur', 'tanggal', 'total'];
    // relasi untuk pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function detailPembelian()
    {
        // 'pembelian_produks_id' adalah nama kolom di tabel detail_pembelian
        return $this->hasMany(DetailPembelian::class, 'pembelian_produks_id');
    }

    // Tambahkan ini di dalam class PembelianProduk
    public function getTotalAttribute()
    {
        // Hitung total dari relasi detailPembelian
        return $this->detailPembelian->sum(function ($detail) {
            return $detail->qty * $detail->harga_per_unit;
        });
    }

    protected static function booted()
    {
        static::created(function ($pembelian) {
            $jurnalService = app(\App\Services\JurnalOtomatisService::class);
            $jurnalService->dariPembelian($pembelian);
        });
    }

}