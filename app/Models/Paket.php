<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    protected $fillable = [
    'no_paket',
    'deskripsi',
    'harga',
];

// Relasi dengan tabel relasi many to many nya
    public function pendapatanJasa()
    {
        return $this->hasMany(PendapatanJasa::class, 'pakets_id');
    }
}
