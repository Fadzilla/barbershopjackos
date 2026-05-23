<?php

namespace App\Filament\Exports;

use App\Models\Pemakaian;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PemakaianExporter extends Exporter
{
    protected static ?string $model = Pemakaian::class;

    public static function getColumns(): array
    {
        return [
            // list kolom yang mau ditampilkan di export

            ExportColumn::make('id')->label('ID Pemakaian'),
            ExportColumn::make('nomer_pemakaian')->label('Nomor Pemakaian'),
            ExportColumn::make('created_at')->label('Tanggal Pemakaian'),
            ExportColumn::make('total_pemakaian')->label('Total Pemakaian'),
            ExportColumn::make('Keterangan')->label('Keterangan'),
            

        ];
    }

   public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pemakaian export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
