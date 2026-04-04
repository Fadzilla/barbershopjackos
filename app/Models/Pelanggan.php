<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// tambahan
use Illuminate\Support\Facades\DB;

class Pelanggan extends Model
{
    use HasFactory;
    protected $table = 'pelanggan'; // Nama tabel eksplisit

    protected $guarded = [];

    public static function getKodePelanggan()
    {
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(kode_pelanggan), 'PL000') as kode_pelanggan 
                FROM pelanggan ";
        $kodepelanggan = DB::select($sql);

        // cacah hasilnya
        foreach ($kodepelanggan as $kdplg) {
            $kd = $kdplg->kode_pelanggan;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($kd,-3);
        $noakhir = $noawal+1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'PL'.str_pad($noakhir,3,"0",STR_PAD_LEFT); //menyambung dengan string PR-001
        return $noakhir;

    }

}