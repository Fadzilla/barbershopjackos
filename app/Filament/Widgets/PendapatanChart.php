<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

// tambahan untuk akses ke model
use App\Models\Pendapatan;

class PendapatanChart extends ChartWidget
{
    protected static ?string $heading = 'Total Pendapatan'; //judul widget chart

    protected function getData(): array
    {
        // Ambil data total pendapatan
        $data = Pendapatan::query()
            ->selectRaw('no_faktur, total as total_pendapatan')
            ->get()
            ->map(function ($pendapatan) {
                return [
                    'no_faktur' => $pendapatan->no_faktur,
                    'total_pendapatan' => $pendapatan->total_pendapatan,
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
                    'label' => 'Total Pendapatan',
                    'data' => $data->pluck('total_pendapatan')->toArray(),
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $data->pluck('no_faktur')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}