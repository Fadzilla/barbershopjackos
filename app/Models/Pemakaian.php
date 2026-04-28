<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// tambahan
use Illuminate\Support\Facades\DB;

class Pemakaian extends Model
{
    use HasFactory;

    protected $table = 'pemakaian';
    protected $guarded = [];
    
    public static function getKodePemakaian()
    {
        $sql = "SELECT IFNULL(MAX(no_pemakaian), 'PMK-0000000') as no_pemakaian 
                FROM pemakaian";
        $kode = DB::select($sql);

        foreach ($kode as $kdp) {
            $kd = $kdp->no_pemakaian;
        }

        $noawal = substr($kd, -7);
        $noakhir = $noawal + 1;
        $noakhir = 'PMK-' . str_pad($noakhir, 7, "0", STR_PAD_LEFT);

        return $noakhir;
    }

    // RELASI KE PEGAWAI
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    // RELASI KE PEMAKAIAN PRODUK
    public function pemakaianProduk()
    {
        return $this->hasMany(PemakaianProduk::class, 'pemakaian_id');
    }
}