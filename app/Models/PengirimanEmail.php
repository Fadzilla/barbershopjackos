<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Retur;

class PengirimanEmail extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_emails';

    protected $guarded = [];

    public function retur()
    {
        return $this->belongsTo(
            Retur::class,
            'retur_id'
        );
    }
}