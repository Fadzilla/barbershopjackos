<?php

namespace App\Filament\Resources\BukuBesarResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\Jurnal;
use Carbon\Carbon;

class BukuBesar extends Widget
{
    protected static string $view = 'filament.resources.buku-besar-resource.widgets.buku-besar';

    protected int | string | array $columnSpan = 'full';

    public $periode_awal;
    public $periode_akhir;
    public $coa_id;

    public function mount(): void
    {
        $this->periode_awal = request('periode_awal', now()->format('Y-m'));
        $this->periode_akhir = request('periode_akhir', now()->format('Y-m'));
        $this->coa_id = request('coa_id');
    }

    // 🌟 TAMBAHKAN FUNGSI INI AGAR TOMBOL FILTER DI BLADE BERFUNGSI NORMAL TANPA ERROR 🌟
    public function filterJurnal()
    {
        // Cukup dikosongkan. Livewire otomatis menangkap perubahan data wire:model
        // dan melakukan re-render komponen secara otomatis ke view Blade Skontro.
    }

    public function getViewData(): array
    {
        $saldoAwal = 0;

        // QUERY UTAMA
        $jurnalsQuery = Jurnal::with(['details' => function ($query) {
            if ($this->coa_id) {
                $query->where('coa_id', $this->coa_id);
            }
            $query->with('coa');
        }])
        ->orderBy('tanggal', 'asc')
        ->orderBy('id', 'asc');

        if ($this->periode_awal && $this->periode_akhir) {

            $awal = Carbon::createFromFormat('Y-m', $this->periode_awal)->startOfMonth();
            $akhir = Carbon::createFromFormat('Y-m', $this->periode_akhir)->endOfMonth();

            // SALDO AWAL (Menghitung total transaksi sebelum periode yang dipilih)
            $saldoAwal = Jurnal::where('tanggal', '<', $awal)
                ->with(['details' => function ($query) {
                    if ($this->coa_id) {
                        $query->where('coa_id', $this->coa_id);
                    }
                }])
                ->get()
                ->flatMap->details
                ->sum(function ($detail) {
                    // Menghitung selisih debit dan credit
                    return ($detail->debit ?? $detail->debit) - ($detail->credit ?? $detail->kredit);
                });

            // FILTER PERIODE
            $jurnalsQuery->whereBetween('tanggal', [$awal, $akhir]);
        }

        // Supaya sinkron dengan perulangan di view Blade milikmu yang menggunakan $jurnal->jurnaldetail
        // kita lakukan load ke property 'jurnaldetail' juga jika dibutuhkan
        $jurnals = $jurnalsQuery->get()->map(function($jurnal) {
            // Kita duplikat property 'details' ke 'jurnaldetail' agar variabel di Blade tidak rusak
            $jurnal->jurnaldetail = $jurnal->details;
            // Kita duplikat property 'tanggal' ke 'tgl' agar Carbon::parse($jurnal->tgl) di Blade tidak error null
            $jurnal->tgl = $jurnal->tanggal;
            // Kita duplikat property 'no_ref' ke 'no_referensi' agar match dengan Blade kamu
            $jurnal->no_referensi = $jurnal->no_ref;
            return $jurnal;
        });

        return [
            'jurnals' => $jurnals,
            'periode_awal' => $this->periode_awal,
            'periode_akhir' => $this->periode_akhir,
            'saldoAwal' => $saldoAwal,
        ];
    }
}