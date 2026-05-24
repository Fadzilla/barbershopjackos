<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Retur;

class PengirimanEmailRetur extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_emails_retur';

    protected $guarded = [];

    public function retur()
    {
        return $this->belongsTo(
            Retur::class,
            'retur_id'
        );
    }
}