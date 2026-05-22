<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// tambahan
use Illuminate\Support\Facades\DB;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai'; // wajib karena tidak plural

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | GENERATE KODE PEGAWAI
    |--------------------------------------------------------------------------
    */
    public static function getKodePegawai()
    {
        $sql = "SELECT IFNULL(MAX(kode_pegawai), 'PG000') as kode_pegawai FROM pegawai";
        $kodepegawai = DB::select($sql);

        foreach ($kodepegawai as $kdpgw) {
            $kd = $kdpgw->kode_pegawai;
        }

        $noawal = substr($kd, -3);
        $noakhir = $noawal + 1;

        // 🔥 perbaikan: hilangkan spasi biar konsisten
        return 'PG' . str_pad($noakhir, 3, "0", STR_PAD_LEFT);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // relasi ke retur
    public function returs()
    {
        return $this->hasMany(Retur::class, 'pegawai_id');
    }
}