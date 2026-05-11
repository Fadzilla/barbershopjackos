<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $retur;

    /**
     * Create a new message instance.
     */
    public function __construct($retur)
    {
        $this->retur = $retur;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this

            ->subject('Invoice Retur Barang')

            ->html(

                '
                    <h2>Invoice Retur</h2>

                    <p>
                        Kode Retur :
                        '.$this->retur->kode_retur.'
                    </p>

                    <p>
                        Pegawai :
                        '.$this->retur->nama_pegawai.'
                    </p>

                    <p>
                        Produk :
                        '.$this->retur->nama_produk.'
                    </p>

                    <p>
                        Qty :
                        '.$this->retur->qty.'
                    </p>

                    <p>
                        Total :
                        Rp '.number_format(
                            $this->retur->total,
                            0,
                            ",",
                            "."
                        ).'
                    </p>
                '

            );
    }
}