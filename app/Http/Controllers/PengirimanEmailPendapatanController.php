<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengirimanEmailPendapatan; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Mail; 
use App\Mail\InvoiceMailPendapatan; 
use Barryvdh\DomPDF\Facade\Pdf; 

class PengirimanEmailPendapatanController extends Controller
{
    public static function proses_kirim_email_pembayaran(){
        
        // 1. Ambil data transaksi utama termasuk email user dan nama pegawai
        $data = DB::table('pendapatan')
                ->join('pelanggan', 'pendapatan.pelanggan_id', '=', 'pelanggan.id')
                ->join('users', 'pelanggan.user_id', '=', 'users.id') 
                ->join('pegawai', 'pendapatan.pegawai_id', '=', 'pegawai.id') 
                ->where('pendapatan.status', 'bayar')
                ->whereNotIn('pendapatan.id', function ($query) {
                    $query->select('pendapatan_id')
                        ->from('pengiriman_email');
                })
                ->select(
                    'pendapatan.id', 
                    'pendapatan.no_faktur', 
                    'users.email', 
                    'users.name as nama_user',
                    'pegawai.nama_pegawai'
                )
                ->first();

        $status_pesan = "";

        if ($data) {
            $id = $data->id;
            $no_faktur = $data->no_faktur;
            $email = $data->email;
            $nama_penerima = $data->nama_user;
            $nama_pegawai = $data->nama_pegawai;

            // 2. Ambil detail layanan untuk isi tabel invoice
            $layanan = DB::table('pendapatan_jasa')
                        ->join('pakets', 'pendapatan_jasa.paket_id', '=', 'pakets.id')
                        ->select(
                            'pakets.deskripsi', 
                            'pendapatan_jasa.harga_paket', 
                            'pendapatan_jasa.jml',
                            DB::raw('(pendapatan_jasa.harga_paket * pendapatan_jasa.jml) as subtotal')
                        )
                        ->where('pendapatan_jasa.pendapatan_id', '=', $id) 
                        ->get();

            // 3. Generate PDF
            $pdf = Pdf::loadView('pdf.invoice', [
                'no_faktur' => $no_faktur,
                'nama_pelanggan' => $nama_penerima,
                'nama_pegawai' => $nama_pegawai,
                'items' => $layanan,
                'total' => $layanan->sum('subtotal'),
                'tanggal' => now()->format('d-M-Y'),
            ]);

            // 4. Bungkus data untuk Mailable (DISINI PERBAIKANNYA)
            // Kunci array harus sama dengan yang dipanggil di emails/invoice.blade.php
            $dataAtributPelanggan = [
                'nama_pelanggan' => $nama_penerima, // Diubah agar sesuai dengan file blade email
                'invoice_number' => $no_faktur
            ];

            // 5. Eksekusi pengiriman
            Mail::to($email)->send(new InvoiceMailPendapatan($dataAtributPelanggan, $pdf->output()));

            // 6. Catat log agar tidak dikirim ulang
            PengirimanEmailPendapatan::create([
                'pendapatan_id' => $id, 
                'status' => 'sudah terkirim',
                'tgl_pengiriman_pesan' => now(),
            ]);

            $status_pesan = "Email berhasil dikirim ke: " . $email;
        } else {
            $status_pesan = "Tidak ada data yang perlu dikirim (Data pelanggan kosong atau sudah terkirim).";
        }

        return view('autorefresh_email', compact('status_pesan'));
    }
}