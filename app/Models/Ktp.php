<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ktp extends Model
{
    use HasFactory;

    // beri nama table eksplisit karena kita merubah dari ktps menjadi ktp
    protected $table = 'ktp';

    // daftarkan kolom yang bisa diisi dan di modifikasi
    protected $fillable = [
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'image_path'
    ];
}