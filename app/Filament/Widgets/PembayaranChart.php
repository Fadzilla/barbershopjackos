<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

// tambahan untuk akses ke model
use App\Models\Pembayaran;

class PembayaranChart extends ChartWidget
{
    protected static ?string $heading = 'Jenis Pembayaran'; //judul widget chart

    protected function getData(): array
    {
        // Ambil data jumlah pembayaran berdasarkan payment type
        $data = Pembayaran::query()
            ->selectRaw('payment_type, COUNT(*) as total')
            ->groupBy('payment_type')
            ->get()
            ->map(function ($pembayaran) {
                return [
                    'payment_type' => $pembayaran->payment_type ?? 'Lainnya',
                    'total' => $pembayaran->total,
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
                    'label' => 'Jenis Pembayaran',
                    'data' => $data->pluck('total')->toArray(),
                ],
            ],
            'labels' => $data->pluck('payment_type')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}