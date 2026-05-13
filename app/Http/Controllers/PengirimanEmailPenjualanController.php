<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengirimanEmailPenjualan; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Mail; 
use App\Mail\InvoiceMail; 
use Barryvdh\DomPDF\Facade\Pdf; 

class PengirimanEmailController extends Controller
{
    public static function proses_kirim_email_pembayaran(){
        
        // 1. Ambil data transaksi utama termasuk email user dan nama pegawai
        $data = DB::table('penjualan')
                ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                ->join('users', 'pelanggan.user_id', '=', 'users.id') 
                ->join('pegawai', 'penjualan.pegawai_id', '=', 'pegawai.id') 
                ->where('status', 'bayar')
                ->whereNotIn('penjualan.id', function ($query) {
                    $query->select('penjualan_id')
                        ->from('pengiriman_email_penjualan');
                })
                ->select(
                    'penjualan.id', 
                    'penjualan.no_faktur', 
                    'users.email', 
                    'users.name as nama_user',
                    'penjualan.pelanggan.id'
                )
                ->first()
                ->get();
        // var_dump($data);
        // 2. Untuk setiap data penjualan, cari item barang detailnya
        // inisialisasi array kosong
        // foreach($data as $p){
        if ($data) {
            $id = $data->id;
            $no_faktur = $data->no_faktur;
            $email = $data->email;
            $pelanggan_id = $data->pelanggan_id;
            // query data barang detailnya
            $produk = DB::table('penjualan')
                        ->join('penjualan_produk', 'penjualan.id', '=', 'penjualan_produk.penjualan_id')
                        ->join('pembayaran_penjualan', 'penjualan.id', '=', 'pembayaran_penjualan.penjualan_id')
                        ->join('produk', 'penjualan_produk.produk_id', '=', 'produk.id')
                        ->join('pelanggan', 'penjualan.pelanggan_id', '=', 'pelanggan.id')
                        ->select('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan', 'penjualan_produk.produk_id', 'produk.nama_produk','penjualan_produk.harga_jual', 
                                 'produk.foto',
                                  DB::raw('SUM(penjualan_produk.jml) as total_produk'),
                                  DB::raw('SUM(penjualan_produk.harga_jual * penjualan_produk.jml) as total_belanja'))
                        ->where('penjualan.pelanggan_id', '=',$pelanggan_id) 
                        ->where('penjualan.id', '=',$id) 
                        ->groupBy('penjualan.id','penjualan.no_faktur','pelanggan.nama_pelanggan','penjualan_produk.produk_id', 'produk.nama_produk','penjualan_produk.harga_jual',
                                  'produk.foto',
                                 )
                        ->get();

            $pdf = Pdf::loadView('pdf.invoice', [
                'no_faktur' => $no_faktur,
                'nama_pelanggan' => $produk[0]->nama_pelanggan ?? '-',
                'items' => $produk,
                'total' => $produk->sum('total_belanja'),
                'tanggal' => now()->format('d-M-Y'),
            ]);

            // data 
            $dataAtributPelanggan = [
                'customer_name' => $produk[0]->nama_pelanggan,
                'invoice_number' => $no_faktur
            ];

             // Kirim email menggunakan Mailable
             Mail::to($email)->send(new InvoiceMailPenjualan($dataAtributPelanggan,$pdf->output()));

             // Delay 10 detik sebelum lanjut ke email berikutnya
            sleep(10);

             // Catat pengiriman email
            PengirimanEmailPenjualan::create([
                'penjualan_id' => $id,
                'status' => 'sudah terkirim',
                'tgl_pengiriman_pesan' => now(),
            ]);

            // echo "<hr>";
            // var_dump($data);
            // echo "<hr>";
            
        }

        // dibungkus autorefresh
        return view('autorefresh_email_penjualan');
    }
}