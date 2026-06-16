<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

// tambahan untuk akses ke model
use App\Models\Pemakaian;

class PemakaianChart extends ChartWidget
{
    protected static ?string $heading = 'Total Pemakaian'; //judul widget chart

    protected function getData(): array
    {
        // Ambil data total pemakaian
        $data = Pemakaian::query()
            ->selectRaw('id, total_pemakaian')
            ->get()
            ->map(function ($pemakaian) {
                return [
                    'id' => $pemakaian->id,
                    'total_pemakaian' => $pemakaian->total_pemakaian,
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
                    'label' => 'Total Pemakaian',
                    'data' => $data->pluck('total_pemakaian')->toArray(),
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $data->pluck('id')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}