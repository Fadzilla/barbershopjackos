<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    protected $table = 'jurnal_detail';
    
    protected $guarded = [];

    // relasi ke tabel jurnal
    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class);
    }

    // relasi ke tabel coa
    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }
}