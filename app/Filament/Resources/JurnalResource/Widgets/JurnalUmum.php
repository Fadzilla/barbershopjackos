<?php

namespace App\Filament\Resources\JurnalResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\Jurnal;
use Carbon\Carbon;

class JurnalUmum extends Widget
{
    protected static string $view = 'filament.resources.jurnal-resource.widgets.jurnal-umum';

    protected int | string | array $columnSpan = 'full';

    public $periode; // properti untuk menyimpan periode (format: Y-m)

    // Inisialisasi awal saat widget pertama kali dibuka
    public function mount()
    {
        $this->periode = now()->format('Y-m');
    }

    // Fungsi pemicu saat tombol filter diklik di view
    public function filterJurnal()
    {
        // Livewire otomatis me-refresh komponen saat properti $this->periode berubah
    }

    /**
     * Method khusus Filament untuk mengirimkan data ke file Blade custom
     */
    protected function getViewData(): array
    {
        // Mengambil data jurnal beserta relasi detail dan coa berdasarkan filter periode
        $jurnals = Jurnal::with(['jurnaldetail.coa'])
            ->when($this->periode, function ($query) {
                $parsedDate = Carbon::createFromFormat('Y-m', $this->periode);
                return $query->whereYear('tgl', $parsedDate->year)
                             ->whereMonth('tgl', $parsedDate->month);
            })
            ->orderBy('tgl', 'asc')
            ->get();

        // Mengirimkan variabel ke blade
        return [
            'jurnals' => $jurnals,
        ];
    }
}