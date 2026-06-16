<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

// tambahan untuk akses ke model
use App\Models\Retur;

class ReturChart extends ChartWidget
{
    protected static ?string $heading = 'Alasan Retur'; //judul widget chart

    protected function getData(): array
    {
        // Ambil data jumlah retur berdasarkan alasan
        $data = Retur::query()
            ->selectRaw('alasan, COUNT(*) as total')
            ->groupBy('alasan')
            ->get()
            ->map(function ($retur) {
                return [
                    'alasan' => $retur->alasan,
                    'total' => $retur->total,
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
                    'label' => 'Alasan Retur',
                    'data' => $data->pluck('total')->toArray(),
                ],
            ],
            'labels' => $data->pluck('alasan')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}