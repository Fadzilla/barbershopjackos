<?php

namespace App\Filament\Exports;

use App\Models\Paket;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PaketExporter extends Exporter
{
    protected static ?string $model = Paket::class;

    public static function getColumns(): array
    {
        return [

            ExportColumn::make('no_paket')
                ->label('No Paket'),

            ExportColumn::make('harga')
                ->label('Harga'),

            ExportColumn::make('deskripsi')
                ->label('Deskripsi'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export paket selesai dan siap didownload.';
    }
}