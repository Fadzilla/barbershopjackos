<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengirimanEmailPembelian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMailPembelian;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanEmailPembelianController extends Controller
{
    public static function proses_kirim_email_pembayaran()
    {

        // Ambil data pembelian yang sudah bayar
        // dan belum pernah dikirim email
        $data = DB::table('pembelian')
            ->join('users', 'pembelian.user_id', '=', 'users.id')
            ->where('pembelian.status', 'bayar')
            ->whereNotIn('pembelian.id', function ($query) {
                $query->select('pembelian_id')
                    ->from('pengiriman_email');
            })
            ->select(
                'pembelian.id',
                'pembelian.no_faktur',
                'users.email',
                'pembelian.user_id'
            )
            ->first();

        if ($data) {

            $id = $data->id;
            $no_faktur = $data->no_faktur;
            $email = $data->email;

            // Detail produk pembelian
            $barang = DB::table('pembelian')
                ->join('pembelian_produk', 'pembelian.id', '=', 'pembelian_produk.pembelian_id')
                ->join('produk', 'pembelian_produk.produk_id', '=', 'produk.id')
                ->join('users', 'pembelian.user_id', '=', 'users.id')

                ->select(
                    'pembelian.id',
                    'pembelian.no_faktur',
                    'users.name',

                    'produk.nama_produk',
                    'produk.foto',

                    'pembelian_produk.harga',

                    DB::raw('SUM(pembelian_produk.jumlah) as total_barang'),

                    DB::raw('SUM(pembelian_produk.harga * pembelian_produk.jumlah) as total_belanja')
                )

                ->where('pembelian.id', $id)

                ->groupBy(
                    'pembelian.id',
                    'pembelian.no_faktur',
                    'users.name',
                    'produk.nama_produk',
                    'produk.foto',
                    'pembelian_produk.harga'
                )

                ->get();

            // Generate PDF
            $pdf = Pdf::loadView('pdf.invoice', [
                'no_faktur' => $no_faktur,
                'nama_pelanggan' => $barang[0]->name ?? '-',
                'items' => $barang,
                'total' => $barang->sum('total_belanja'),
                'tanggal' => now()->format('d-M-Y'),
            ]);

            // Data email
            $dataAtributPelanggan = [
                'nama_pelanggan' => $barang[0]->name ?? '-',
                'invoice_number' => $no_faktur
            ];

            // Kirim email
            Mail::to($email)->send(
                new InvoiceMailPembelian(
                    $dataAtributPelanggan,
                    $pdf->output()
                )
            );

            // Simpan log pengiriman
            PengirimanEmailPembelian::create([
                'pembelian_id' => $id,
                'status' => 'sudah terkirim',
                'tgl_pengiriman_pesan' => now(),
            ]);
<<<<<<< HEAD:app/Http/Controllers/PengirimanEmailPembelianController.php
        }
            // Kirim email menggunakan Mailable
             Mail::to($email)->send(new InvoiceMailPembelian($dataAtributPelanggan,$pdf->output()));
=======
>>>>>>> ddf2c200108f6ae2f083791715d3d56bd9065019:app/Http/Controllers/PengirimanEmailController.php

            // Kirim email menggunakan Mailable
            Mail::to($email)->send(new InvoiceMail($dataAtributPelanggan, $pdf->output()));

            // Delay 5 detik sebelum lanjut ke email berikutnya
            sleep(5);
        }

        return view('autorefresh_email');
    }
}