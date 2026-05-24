<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// menggunakan kelas pdf
use Barryvdh\DomPDF\Facade\Pdf;

// model
use App\Models\Pemakaian;

class PDFController extends Controller
{
    // cetak pdf pemakaian
    public function pemakaianpdf()
    {
        // ambil semua data pemakaian
        $pemakaian = Pemakaian::with('pegawai')->get();

        // load view pdf
        $pdf = Pdf::loadView('pdf.pemakaian-pdf', [
            'pemakaian' => $pemakaian
        ]);

        // download pdf
        return $pdf->download('data-pemakaian.pdf');
    }
}