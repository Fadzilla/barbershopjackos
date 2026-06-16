<?php

namespace App\Filament\Resources\JurnalResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\JurnalDetail;

class JurnalStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalDebit = JurnalDetail::sum('debit');
        $totalKredit = JurnalDetail::sum('kredit');

        return [

            Stat::make(
                'Total Debit',
                'Rp ' . number_format($totalDebit, 0, ',', '.')
            ),

            Stat::make(
                'Total Kredit',
                'Rp ' . number_format($totalKredit, 0, ',', '.')
            ),

            Stat::make(
                'Status',
                $totalDebit == $totalKredit ? 'BALANCE' : 'TIDAK BALANCE'
            ),

        ];
    }
}
