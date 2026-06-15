<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    protected $table = 'jurnal_detail';
    
    protected $fillable = [
        'jurnal_id',
        'coa_id',
        'debit',
        'kredit'
    ];

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class);
    }

    public function coa()
    {
        return $this->belongsTo(Coa::class);
    }
}