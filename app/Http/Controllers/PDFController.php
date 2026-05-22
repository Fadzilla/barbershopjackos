<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function paketPdf()
    {
        $pakets = Paket::all();

        $pdf = Pdf::loadView('pdf.paket-pdf', compact('pakets'));

        return $pdf->download('data-paket.pdf');
    }
}