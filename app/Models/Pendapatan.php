<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\JurnalOtomatisService;

// untuk tambahan db
use Illuminate\Support\Facades\DB;

class Pendapatan extends Model
{
    use HasFactory;

    protected $table = 'pendapatan'; // Nama tabel eksplisit

    protected $guarded = [];

    public static function getKodeFaktur()
    {
        // query kode perusahaan
        $sql = "SELECT IFNULL(MAX(no_faktur), 'F-0000000') as no_faktur 
                FROM pendapatan ";
        $kodefaktur = DB::select($sql);

        // cacah hasilnya
        foreach ($kodefaktur as $kdpmbl) {
            $kd = $kdpmbl->no_faktur;
        }
        // Mengambil substring tiga digit akhir dari string PR-000
        $noawal = substr($kd, -7);
        $noakhir = $noawal + 1; //menambahkan 1, hasilnya adalah integer cth 1
        $noakhir = 'F-' . str_pad($noakhir, 7, "0", STR_PAD_LEFT); //menyambung dengan string P-00001
        return $noakhir;

    }

    // relasi ke tabel pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    // relasi ke tabel pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    // relasi ke tabel pendapatan jasa
    public function pendapatanJasa()
    {
        return $this->hasMany(PendapatanJasa::class, 'pendapatan_id');
    }

    protected static function booted()
    {
        // Berjalan otomatis setiap kali data pendapatan disimpan (baru atau di-update)
        static::saved(function ($pendapatan) {
            // Jurnal hanya dibuat jika statusnya sudah lunas/bayar
            if ($pendapatan->status === 'bayar') {
                $jurnalService = app(JurnalOtomatisService::class);
                $jurnalService->dariPendapatan($pendapatan);
            }
        });
    }
}