<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengirimanEmailRetur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMailRetur;
use Barryvdh\DomPDF\Facade\Pdf;

class PengirimanEmailReturController extends Controller
{
    public static function proses_kirim_email_retur($id)
    {
        $data = DB::table('returs')
            ->join('pegawai', 'returs.karyawan_id', '=', 'pegawai.id')
            ->join('produk', 'returs.produk_id', '=', 'produk.id')
            ->where('returs.id', $id)
            ->select(
                'returs.id',
                'returs.kode_retur',
                'returs.status',
                'returs.alasan',
                'returs.qty',
                'returs.total',
                'returs.harga_per_unit',
                'returs.tanggal_retur',
                'pegawai.nama_pegawai',
                'produk.nama_produk',
                'produk.harga_produk'
            )
            ->first();

        if ($data) {

            $email = 'admin@gmail.com';

            $pdf = Pdf::loadView('pdf.returs', [
                'returs' => [$data]
            ]);

            Mail::to($email)->send(
                new InvoiceMailRetur($data)
            );

            PengirimanEmailRetur::create([
                'retur_id'      => $data->id,
                'email_tujuan'  => $email,
                'subjek'        => 'Invoice Retur Barang',
                'pesan'         => 'Invoice retur berhasil dikirim',
                'status'        => 'Terkirim',
            ]);

            sleep(3);
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Email retur berhasil dikirim'
            );
    }
}