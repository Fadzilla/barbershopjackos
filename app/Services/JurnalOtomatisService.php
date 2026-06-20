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
     * Method utama untuk membuat jurnal dengan pengecekan duplikat yang ketat
     */
    private function buatJurnal($data, $detailEntries)
    {
        return DB::transaction(function () use ($data, $detailEntries) {
            // 1. Cek dengan lock for update
            $existingJurnal = Jurnal::where('no_referensi', $data['no_referensi'])
                ->lockForUpdate()
                ->first();
                
            if ($existingJurnal) {
                return $existingJurnal;
            }

            // 2. Buat jurnal
            $jurnal = Jurnal::create([
                'tgl' => $data['tgl'],
                'no_referensi' => $data['no_referensi'],
                'deskripsi' => $data['deskripsi'] ?? '',
            ]);

            // 3. Buat detail jurnal
            foreach ($detailEntries as $detail) {
                $debit = $detail['debit'] ?? 0;
                $credit = $detail['credit'] ?? 0;
                
                if ($debit == 0 && $credit == 0) {
                    throw new \Exception("Debit atau credit harus memiliki nilai > 0 untuk jurnal {$data['no_referensi']}");
                }

                JurnalDetail::create([
                    'jurnal_id' => $jurnal->id,
                    'coa_id' => $this->getCoaId($detail['kode_akun']),
                    'debit' => $debit,
                    'credit' => $credit,
                    'deskripsi' => $detail['deskripsi'] ?? '',
                ]);
            }

            return $jurnal;
        });
    }

    /**
     * Jurnal dari Pendapatan (status bayar)
     */
    public function dariPendapatan($pendapatan)
    {
        if ($pendapatan->status !== 'bayar') {
            return null;
        }

        $total = $pendapatan->total ?? 0;
        
        if ($total <= 0) {
            return null;
        }

        return $this->buatJurnal(
            [
                'tgl' => $pendapatan->tgl,
                'no_referensi' => $pendapatan->no_faktur,
                'deskripsi' => "Pendapatan - Faktur {$pendapatan->no_faktur}",
            ],
            [
                [
                    'kode_akun' => '111',
                    'debit' => $total,
                    'credit' => 0,
                    'deskripsi' => 'Penerimaan kas dari pendapatan',
                ],
                [
                    'kode_akun' => '411',
                    'debit' => 0,
                    'credit' => $total,
                    'deskripsi' => 'Pendapatan jasa salon/barbershop',
                ]
            ]
        );
    }

    /**
     * Jurnal dari Penjualan (status bayar)
     */
    public function dariPenjualan($penjualan)
    {
        if ($penjualan->status !== 'bayar') {
            return null;
        }

        $total = $penjualan->total_dibayar ?? 0;
        
        if ($total <= 0) {
            return null;
        }

        return $this->buatJurnal(
            [
                'tgl' => $penjualan->tgl,
                'no_referensi' => $penjualan->no_faktur,
                'deskripsi' => "Penjualan - Faktur {$penjualan->no_faktur}",
            ],
            [
                [
                    'kode_akun' => '111',
                    'debit' => $total,
                    'credit' => 0,
                    'deskripsi' => 'Penerimaan kas kasir penjualan',
                ],
                [
                    'kode_akun' => '412',
                    'debit' => 0,
                    'credit' => $total,
                    'deskripsi' => 'Penjualan produk/layanan barbershop',
                ]
            ]
        );
    }

    /**
     * Jurnal dari Pembelian
     */
    public function dariPembelian($pembelian)
    {
        $total = $pembelian->total ?? 0;
        
        if ($total <= 0) {
            return null;
        }

        return $this->buatJurnal(
            [
                'tgl' => $pembelian->tanggal,
                'no_referensi' => $pembelian->no_faktur,
                'deskripsi' => "Pembelian - Faktur {$pembelian->no_faktur}",
            ],
            [
                [
                    'kode_akun' => '511',
                    'debit' => $total,
                    'credit' => 0,
                    'deskripsi' => 'Pembelian stok/perlengkapan',
                ],
                [
                    'kode_akun' => '111',
                    'debit' => 0,
                    'credit' => $total,
                    'deskripsi' => 'Pengeluaran kas untuk pembelian',
                ]
            ]
        );
    }

    /**
     * Jurnal dari Pemakaian
     */
    public function dariPemakaian($pemakaian)
    {
        $total = $pemakaian->total_pemakaian ?? 0;
        
        if ($total <= 0) {
            return null;
        }

        return $this->buatJurnal(
            [
                'tgl' => $pemakaian->tanggal_pakai,
                'no_referensi' => $pemakaian->nomer_pemakaian,
                'deskripsi' => "Pemakaian - {$pemakaian->nomer_pemakaian}",
            ],
            [
                [
                    'kode_akun' => '512',
                    'debit' => $total,
                    'credit' => 0,
                    'deskripsi' => 'Beban pemakaian perlengkapan internal',
                ],
                [
                    'kode_akun' => '112',
                    'debit' => 0,
                    'credit' => $total,
                    'deskripsi' => 'Pengurangan persediaan barang',
                ]
            ]
        );
    }

    /**
     * Jurnal dari Retur
     */
    public function dariRetur($retur)
    {
        $total = $retur->total ?? 0;
        
        if ($total <= 0) {
            return null;
        }

        return $this->buatJurnal(
            [
                'tgl' => $retur->tanggal_retur,
                'no_referensi' => $retur->kode_retur,
                'deskripsi' => "Retur - {$retur->kode_retur}",
            ],
            [
                [
                    'kode_akun' => '111',
                    'debit' => $total,
                    'credit' => 0,
                    'deskripsi' => 'Penerimaan kembali kas dari retur',
                ],
                [
                    'kode_akun' => '413',
                    'debit' => 0,
                    'credit' => $total,
                    'deskripsi' => 'Pencatatan retur barang',
                ]
            ]
        );
    }

    /**
     * Hapus semua jurnal duplikat yang sudah terlanjur masuk
     */
    public function hapusSemuaDuplikat()
    {
        return DB::transaction(function () {
            $duplikat = Jurnal::select('no_referensi', DB::raw('COUNT(*) as count'))
                ->groupBy('no_referensi')
                ->having('count', '>', 1)
                ->get();

            if ($duplikat->isEmpty()) {
                return "Tidak ada data duplikat ditemukan";
            }

            $totalDihapus = 0;
            $detailDihapus = 0;
            
            foreach ($duplikat as $dup) {
                $jurnals = Jurnal::where('no_referensi', $dup->no_referensi)
                    ->orderBy('id', 'asc')
                    ->get();
                
                $sisanya = $jurnals->slice(1);
                foreach ($sisanya as $jurnal) {
                    $deletedDetail = JurnalDetail::where('jurnal_id', $jurnal->id)->delete();
                    $detailDihapus += $deletedDetail;
                    $jurnal->delete();
                    $totalDihapus++;
                }
            }
            
            return "Berhasil menghapus {$totalDihapus} data jurnal duplikat dan {$detailDihapus} detail jurnal";
        });
    }

    /**
     * Cek apakah ada jurnal duplikat
     */
    public function cekDuplikat()
    {
        $duplikat = Jurnal::select('no_referensi', DB::raw('COUNT(*) as count'))
            ->groupBy('no_referensi')
            ->having('count', '>', 1)
            ->get();

        if ($duplikat->isEmpty()) {
            return "Tidak ada data duplikat";
        }

        $result = [];
        foreach ($duplikat as $dup) {
            $result[] = "no_referensi: {$dup->no_referensi} - {$dup->count} kali muncul";
        }
        return $result;
    }
}