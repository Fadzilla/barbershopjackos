<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// tambahan db
use Illuminate\Support\Facades\DB;

class Pemakaian extends Model
{
    use HasFactory;

    protected $table = 'pemakaian'; 
    protected $guarded = [];

    /**
     * Generate nomor pemakaian otomatis
     */
    public static function getNomerPemakaian()
    {
        // Query nomor pemakaian terakhir
        $sql = "SELECT IFNULL(MAX(nomer_pemakaian), 'PM-0000000') as nomer_pemakaian 
                FROM pemakaian";
        $nomerpemakaian = DB::select($sql);

        // cacah hasilnya
        foreach ($nomerpemakaian as $kdpmk) {
            $kd = $kdpmk->nomer_pemakaian;
        }

        // Ambil 7 digit terakhir
        $noawal = substr($kd, -7);
        $noakhir = $noawal + 1;
        $noakhir = 'PM-' . str_pad($noakhir, 7, "0", STR_PAD_LEFT);
        return $noakhir;
    }

    /**
     * Relasi ke tabel pegawai
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Relasi ke tabel detail pemakaian produk
     */
    public function pemakaianProduk()
    {
        return $this->hasMany(PemakaianProduk::class, 'pemakaian_id');
    }
}