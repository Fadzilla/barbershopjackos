<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Pegawai;
use App\Models\Produk;

class Retur extends Model
{
    use HasFactory;

    protected $table = 'returs';

    protected $fillable = [
        'kode_retur',
        'pegawai_id',
        'produk_id',
        'status',
        'alasan',
        'harga_per_unit',
        'qty',
        'total',
        'tanggal_retur',
        'foto',
    ];

    protected static function booted()
    {
        // Auto hitung total
        static::saving(function ($retur) {
            $retur->total =
                (float) $retur->harga_per_unit *
                (int) $retur->qty;
        });

        // Kurangi stok saat retur dibuat
        static::created(function ($retur) {
            $produk = Produk::find($retur->produk_id);

            if ($produk && $produk->stok >= $retur->qty) {
                $produk->decrement('stok', $retur->qty);
            }
        });

        // Kembalikan stok saat retur dihapus
        static::deleted(function ($retur) {
            $produk = Produk::find($retur->produk_id);

            if ($produk) {
                $produk->increment('stok', $retur->qty);
            }
        });
    }

    public static function getKodeRetur()
    {
        $lastId = DB::table('returs')->max('id') ?? 0;
        $nextId = $lastId + 1;

        return 'RT-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}