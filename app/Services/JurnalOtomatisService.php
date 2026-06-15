<?php

namespace App\Services;

use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Coa;
use Illuminate\Support\Facades\DB;

class JurnalOtomatisService
{
    // Helper cari COA
    private function getCoaId($kodeAkun)
    {
        $coa = Coa::where('kode_akun', $kodeAkun)->first();
        if (!$coa) {
            throw new \Exception("COA dengan kode '$kodeAkun' tidak ditemukan");
        }
        return $coa->id;
    }

    /**
     * Jurnal dari Pendapatan (status bayar)
     */
    public function dariPendapatan($pendapatan)
    {
        if ($pendapatan->status !== 'bayar') {
            return null;
        }

        return DB::transaction(function () use ($pendapatan) {
            $jurnal = Jurnal::create([
                'no_jurnal' => $this->generateNoJurnal(),
                'tanggal' => $pendapatan->tgl,
                'no_ref' => $pendapatan->no_faktur,
                'sumber' => 'pendapatan',
                'sumber_id' => $pendapatan->id,
                'keterangan' => "Pendapatan faktur {$pendapatan->no_faktur}",
            ]);

            // Debit: Kas (kode 111)
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('111'),
                'debit' => $pendapatan->total,
            ]);

            // Kredit: Pendapatan (kode 411)
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('411'),
                'kredit' => $pendapatan->total,
            ]);

            return $jurnal;
        });
    }

    /**
     * Jurnal dari Penjualan (status bayar)
     */
    public function dariPenjualan($penjualan)
    {
        if ($penjualan->status !== 'bayar') {
            return null;
        }

        return DB::transaction(function () use ($penjualan) {
            $jurnal = Jurnal::create([
                'no_jurnal' => $this->generateNoJurnal(),
                'tanggal' => $penjualan->tgl,
                'no_ref' => $penjualan->no_faktur,
                'sumber' => 'penjualan',
                'sumber_id' => $penjualan->id,
                'keterangan' => "Penjualan faktur {$penjualan->no_faktur}",
            ]);

            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('111'), // Kas
                'debit' => $penjualan->total_dibayar,
            ]);

            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('412'), // Penjualan
                'kredit' => $penjualan->total_dibayar,
            ]);

            return $jurnal;
        });
    }

    /**
     * Jurnal dari Pembelian
     */
    public function dariPembelian($pembelian)
    {
        return DB::transaction(function () use ($pembelian) {
            $jurnal = Jurnal::create([
                'no_jurnal' => $this->generateNoJurnal(),
                'tanggal' => $pembelian->tanggal,
                'no_ref' => $pembelian->no_faktur,
                'sumber' => 'pembelian',
                'sumber_id' => $pembelian->id,
                'keterangan' => "Pembelian faktur {$pembelian->no_faktur}",
            ]);

            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('511'), // Pembelian
                'debit' => $pembelian->total,
            ]);

            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('111'), // Kas
                'kredit' => $pembelian->total,
            ]);

            return $jurnal;
        });
    }

    /**
     * Jurnal dari Pemakaian (no_ref = nomer_pemakaian)
     */
    public function dariPemakaian($pemakaian)
    {
        return DB::transaction(function () use ($pemakaian) {
            $jurnal = Jurnal::create([
                'no_jurnal' => $this->generateNoJurnal(),
                'tanggal' => $pemakaian->tanggal_pakai,
                'no_ref' => $pemakaian->nomer_pemakaian,
                'sumber' => 'pemakaian',
                'sumber_id' => $pemakaian->id,
                'keterangan' => "Pemakaian {$pemakaian->nomer_pemakaian}",
            ]);

            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('512'), // Beban Pemakaian
                'debit' => $pemakaian->total_pemakaian,
            ]);

            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('112'), // Persediaan
                'kredit' => $pemakaian->total_pemakaian,
            ]);

            return $jurnal;
        });
    }

    /**
     * Jurnal dari Retur (no_ref = kode_retur)
     */
    public function dariRetur($retur)
    {
        return DB::transaction(function () use ($retur) {
            $jurnal = Jurnal::create([
                'no_jurnal' => $this->generateNoJurnal(),
                'tanggal' => $retur->tanggal_retur,
                'no_ref' => $retur->kode_retur,
                'sumber' => 'retur',
                'sumber_id' => $retur->id,
                'keterangan' => "Retur {$retur->kode_retur}",
            ]);

            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('111'), // Kas
                'debit' => $retur->total,
            ]);

            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'coa_id' => $this->getCoaId('413'), // Retur
                'kredit' => $retur->total,
            ]);

            return $jurnal;
        });
    }

    private function generateNoJurnal()
    {
        $last = Jurnal::latest('id')->first();
        $number = $last ? intval(substr($last->no_jurnal, 4)) + 1 : 1;
        return 'JRN-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}