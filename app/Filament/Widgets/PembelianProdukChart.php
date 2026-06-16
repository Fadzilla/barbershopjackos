<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

// tambahan untuk akses ke model
use App\Models\PembelianProduk;

class PembelianProdukChart extends ChartWidget
{
    protected static ?string $heading = 'Total Pembelian'; //judul widget chart

    protected function getData(): array
    {
        // Ambil data total pembelian berdasarkan faktur
        $data = PembelianProduk::query()
            ->selectRaw('no_faktur, total as total_pembelian')
            ->get()
            ->map(function ($pembelian) {
                return [
                    'no_faktur' => $pembelian->no_faktur,
                    'total_pembelian' => $pembelian->total_pembelian,
                ];
            });

        // Pastikan data ada sebelum dikirim ke chart
        if ($data->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Mengembalikan data dalam format yang dibutuhkan untuk chart
        return [
            'datasets' => [
                [
                    'label' => 'Total Pembelian',
                    'data' => $data->pluck('total_pembelian')->toArray(),
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $data->pluck('no_faktur')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
