<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Tambahan untuk akses ke model
use App\Models\PengirimanEmail; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Mail; 
use App\Mail\KonfirmasiPemakaianMail; 
use Barryvdh\DomPDF\Facade\Pdf; 

class PengirimanEmailController extends Controller
{
    public static function proses_kirim_email_konfirmasi_pemakaian(){
        
        // 1. Query data pemakaian dgn status 'Disetujui' yang belum tercatat di pengiriman_email
        $data = DB::table('pemakaian')
                ->join('pegawai', 'pemakaian.pegawai_id', '=', 'pegawai.id')
                ->join('konfirmasi_pemakaian', 'pemakaian.id', '=', 'konfirmasi_pemakaian.pemakaian_id')
                ->where('konfirmasi_pemakaian.status_konfirmasi', 'Disetujui') 
                ->whereNotIn('pemakaian.id', function ($query) {
                    $query->select('pemakaian_id')
                        ->from('pengiriman_email');
                })
                ->select(
                    'pemakaian.id',
                    'pemakaian.nomer_pemakaian', 
                    'pemakaian.pegawai_id',
                    'pegawai.nama_pegawai'
                )
                ->first();

        if ($data) {
            $id = $data->id;
            $nomer_pemakaian = $data->nomer_pemakaian;
            $pegawai_id = $data->pegawai_id;

            // Tentukan email tujuan (Email Admin/Owner Barbershop)
            // Ganti alamat ini sesuai kebutuhan
            $emailTujuan = 'barbershopjackos@gmail.com';

            // 2. Query detail produk yang digunakan dalam transaksi ini
            $barang = DB::table('pemakaian_produk')
                        ->join('produks', 'pemakaian_produk.produk_id', '=', 'produks.id')
                        ->join('pemakaian', 'pemakaian_produk.pemakaian_id', '=', 'pemakaian.id')
                        ->join('pegawai', 'pemakaian.pegawai_id', '=', 'pegawai.id')
                        ->select(
                            'pemakaian.id',
                            'pemakaian.nomer_pemakaian',
                            'pegawai.nama_pegawai',
                            'produks.nama_produk',
                            'pemakaian_produk.jumlah',
                            'produks.foto_produk'
                        )
                        ->where('pemakaian.id', '=', $id)
                        ->get();

            // 3. Generate PDF
            $pdf = Pdf::loadView('pdf.konfirmasi_pemakaian', [
                'nomer_pemakaian' => $nomer_pemakaian,
                'nama_pegawai' => $data->nama_pegawai,
                'items' => $barang,
                'tanggal' => now()->format('d-M-Y'),
            ]);

            // 4. Siapkan data untuk template email
            $dataAtributPegawai = [
                'pegawai_name' => $data->nama_pegawai,
                'nomer_pemakaian' => $nomer_pemakaian
            ];

            // 5. Kirim email menggunakan Mailable
            try {
                Mail::to($emailTujuan)->send(new KonfirmasiPemakaianMail($dataAtributPegawai, $pdf->output()));

                // Delay 5 detik agar server tidak overload
                sleep(5);

                // 6. Catat log pengiriman agar tidak dikirim ulang
                PengirimanEmail::create([
                    'pemakaian_id' => $id,
                    'status' => 'sudah terkirim',
                    'tgl_pengiriman_pesan' => now(),
                ]);

            } catch (\Exception $e) {
                // Log jika terjadi kegagalan pengiriman (misal: koneksi internet)
                return "Gagal mengirim email: " . $e->getMessage();
            }
        }

        return view('autorefresh_email');
    }
}