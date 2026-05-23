<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMailPemakaian;
use Illuminate\Http\Request;

// tambahan untuk akses ke model
use App\Models\PengirimanEmailPemakaian;

// untuk menggunakan db
use Illuminate\Support\Facades\DB;

// akses ke method mail
use Illuminate\Support\Facades\Mail;

// akses ke kelas email pemakaian mail
use App\Mail\PemakaianMail;

// akses ke dom pdf
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanEmailPemakaianController extends Controller
{
    public static function proses_kirim_email_pemakaian()
    {

        // 1. Query data pemakaian dengan status selesai
        // yang belum dikirim

        $data = DB::table('pemakaian')
                ->join('pegawai', 'pemakaian.pegawai_id', '=', 'pegawai.id')

                ->whereNotIn('pemakaian.id', function ($query) {
                    $query->select('pemakaian_id')
                        ->from('pengiriman_email');
                })

                ->select(
                    'pemakaian.id',
                    'pemakaian.nomer_pemakaian',
                    'pegawai.email',
                    'pemakaian.pegawai_id'
                )

                ->first();

        // 2. Untuk setiap data pemakaian,
        // cari item produk detailnya

        if ($data) {

            $id = $data->id;

            $nomer_pemakaian = $data->nomer_pemakaian;

            $email = $data->email;

            $pegawai_id = $data->pegawai_id;

            // query data produk detailnya

            $produk = DB::table('pemakaian')

                        ->join(
                            'pemakaian_produk',
                            'pemakaian.id',
                            '=',
                            'pemakaian_produk.pemakaian_id'
                        )

                        ->join(
                            'produks',
                            'pemakaian_produk.produk_id',
                            '=',
                            'produks.id'
                        )

                        ->join(
                            'pegawai',
                            'pemakaian.pegawai_id',
                            '=',
                            'pegawai.id'
                        )

                        ->select(
                            'pemakaian.id',

                            'pemakaian.nomer_pemakaian',

                            'pegawai.nama_pegawai',

                            'produks.id as produk_id',

                            'produks.nama_produk',

                            DB::raw('SUM(pemakaian_produk.jumlah) as total_produk'),

                            'pemakaian_produk.tanggal_pakai'
                        )

                        ->where('pemakaian.pegawai_id', '=', $pegawai_id)

                        ->where('pemakaian.id', '=', $id)

                        ->groupBy(
                            'pemakaian.id',
                            'pemakaian.nomer_pemakaian',
                            'pegawai.nama_pegawai',
                            'produks.id',
                            'produks.nama_produk',
                            'pemakaian_produk.tanggal_pakai'
                        )

                        ->get();

            // generate pdf

            $pdf = Pdf::loadView('pdf.InvoicePemakaian', [

                'nomer_pemakaian' => $nomer_pemakaian,

                'nama_pegawai' =>
                    $produk[0]->nama_pegawai ?? '-',

                'items' => $produk,

                'total' => $produk->sum('total_produk'),

                'tanggal' => now()->format('d-M-Y'),

            ]);

            // data atribut email

            $dataAtributPemakaian = [

                'nama_pegawai' =>
                    $produk[0]->nama_pegawai ?? '-',

                'nomer_pemakaian' =>
                    $nomer_pemakaian

            ];

            // Kirim email menggunakan Mailable

            Mail::to($email)->send(

                new InvoiceMailPemakaian(
                    $dataAtributPemakaian,
                    $pdf->output()
                )

            );

            // Delay 5 detik sebelum lanjut berikutnya

            sleep(5);

            // Catat pengiriman email

            PengirimanEmailPemakaian::create([

                'pemakaian_id' => $id,

                'status' => 'sudah terkirim',

                'tgl_pengiriman_pesan' => now(),

            ]);

        }

        // dibungkus autorefresh

        return view('autorefresh_email');
    }
}