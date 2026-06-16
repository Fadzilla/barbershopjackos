<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Phpml\Clustering\KMeans;
// use Phpml\Preprocessing\Normalizer;
// use Phpml\Preprocessing\MinMaxScaler;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class CustomerClustering extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static string $view = 'filament.pages.pelanggan-clustering';
    protected static ?string $navigationGroup = 'pelanggan clustering';

    public $axisX = 0;
    public $axisY = 1;

    
    // FORM (TANPA LIVE)
        protected function getFormSchema(): array
    {
        return [
            Select::make('axisX')
                ->label('Sumbu X')
                ->options([
                    0 => 'Total Tagihan',
                    1 => 'Total Qty',
                    2 => 'Total penjualan'
                ])
                ->statePath('axisX')
                ->dehydrateStateUsing(fn ($state) => (int) $state) //  ini kunci
                ->required(),

            Select::make('axisY')
                ->label('Sumbu Y')
                ->options([
                    0 => 'Total dibayar',
                    1 => 'Total Qty',
                    2 => 'Total penjualan'
                ])
                ->statePath('axisY')
                ->dehydrateStateUsing(fn ($state) => (int) $state) //  ini kunci
                ->required(),
        ];
    }

   
    // CLUSTERING
       public function getAllCharts()
    {
        $data = DB::table('pelanggan')
    ->join('penjualan', 'pelanggan.id', '=', 'penjualan.pelanggan_id')
    ->join('penjualan_produk', 'penjualan.id', '=', 'penjualan_produk.penjualan_id')
    ->select(
        'nama_pelanggan',
        DB::raw('SUM(penjualan.total_dibayar) as total_dibayar'),
        DB::raw('SUM(penjualan_produk.jml) as total_qty'),
        DB::raw('SUM(penjualan_produk.harga_jual * penjualan_produk.jml) as total_penjualan')
    )
    ->groupBy('pelanggan.id', 'nama_pelanggan')
    ->get();

        if ($data->isEmpty()) return [];

        $originalData = $data->values()->all();

        // === BUILD SAMPLE ===
        $samples = [];
        foreach ($originalData as $i => $row) {
            $samples[$i] = [
                (float)$row->total_dibayar,
                (float)$row->total_qty,
                (float)$row->total_penjualan
            ];
        }

        // === MIN MAX SCALING ===
        $this->minMaxScale($samples);

        // === KMEANS ===
        $kmeans = new KMeans(3);
        $clusters = $kmeans->cluster($samples);

        // === BUILD 3 CHART ===
        return [
            'chart1' => $this->formatChart($clusters, $originalData, 0, 1),
            'chart2' => $this->formatChart($clusters, $originalData, 0, 2),
            'chart3' => $this->formatChart($clusters, $originalData, 1, 2),
        ];
    }

    private function minMaxScale(&$samples)
    {
        $numFeatures = count($samples[0]);

        $mins = array_fill(0, $numFeatures, INF);
        $maxs = array_fill(0, $numFeatures, -INF);

        // cari min & max tiap kolom
        foreach ($samples as $sample) {
            foreach ($sample as $i => $value) {
                if ($value < $mins[$i]) $mins[$i] = $value;
                if ($value > $maxs[$i]) $maxs[$i] = $value;
            }
        }

        // scaling
        foreach ($samples as &$sample) {
            foreach ($sample as $i => &$value) {
                if ($maxs[$i] - $mins[$i] == 0) {
                    $value = 0; // hindari pembagian 0
                } else {
                    $value = ($value - $mins[$i]) / ($maxs[$i] - $mins[$i]);
                }
            }
        }
    }

    // =========================
    // FORMAT CHART
    // =========================
    private function formatChart($clusters, $originalData, $axisX, $axisY)
    {
        $datasets = [];
        $colors = ['#efff0e', '#c830ee', '#5c51ff'];
        $columns = ['total_dibayar', 'total_qty', 'total_penjualan'];

        $colX = $columns[$axisX];
        $colY = $columns[$axisY];

        foreach ($clusters as $index => $cluster) {

            $points = [];

            foreach ($cluster as $key => $sample) {
                if (isset($originalData[$key])) {
                    $row = $originalData[$key];

                    $points[] = [
                        'x' => (float)$row->$colX,
                        'y' => (float)$row->$colY,
                        'label' => $row->nama_pelanggan,
                    ];
                }
            }

            $datasets[] = [
                'label' => 'Cluster ' . ($index + 1),
                'data' => $points,
                'backgroundColor' => $colors[$index],
            ];
        }

        return ['datasets' => $datasets];
    }

    // =========================
    // DEFAULT LOAD
    // =========================
    protected function getViewData(): array
    {
        return $this->getAllCharts();
    }

    // =========================
    // SUBMIT BUTTON TRIGGER
    // =========================
    public function submit()
    {
        // 🔥 pastikan ambil state terbaru dari form
        $this->form->getState();

        $data = $this->getClusteringData(3);
        // dd($data);
        // optional debug (tidak menghentikan program)
        // logger($data);

        $this->dispatch('updateChart', [
            'chartData' => $data,
            'axisX' => (int) $this->axisX,
            'axisY' => (int) $this->axisY,
        ]);
    }

    
}