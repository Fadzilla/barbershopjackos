<?php

namespace App\Mail;

use App\Models\PembelianProduk;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PembelianProdukMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pembelian;

    public function __construct(PembelianProduk $pembelian)
    {
        $this->pembelian = $pembelian;
    }

    public function build()
    {
        return $this
            ->subject('Laporan Pembelian Produk')
            ->view('emails.pembelian_produk');
    }
}