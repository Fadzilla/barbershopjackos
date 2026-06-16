<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

// tambahan
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Pemakaian;
use App\Models\Pendapatan;
use App\Models\Pembayaran;
use App\Models\PembelianProduk;
use App\Models\Retur;
use Illuminate\Support\Number;

class DashboardStatCards extends BaseWidget
{
    protected function getStats(): array
    {

        return [
    Stat::make('Total Pemakaian',
        Pemakaian::query()
            ->count()
    )
        ->description('Jumlah transaksi pemakaian')
    ,

    Stat::make('Total Pendapatan',
        Pendapatan::query()
            ->count()
    )
        ->description('Jumlah transaksi pendapatan')
    ,

    Stat::make('Total Pembayaran',
        Pembayaran::query()
            ->count()
    )
        ->description('Jumlah transaksi pembayaran')
    ,

    Stat::make('Total Pembelian',
        PembelianProduk::query()
            ->count()
    )
        ->description('Jumlah transaksi pembelian')
    ,

    Stat::make('Total Retur',
        Retur::query()
            ->count()
    )
        ->description('Jumlah transaksi retur')
    ,
];
    }
}