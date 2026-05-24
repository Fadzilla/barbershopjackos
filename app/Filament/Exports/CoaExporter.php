<?php

namespace App\Filament\Exports;

use App\Models\Coa;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CoaExporter extends Exporter
{
    protected static ?string $model = Coa::class;

    public static function getColumns(): array
    {
        return [

            ExportColumn::make('header_akun')
                ->label('Header Akun'),

            ExportColumn::make('kode_akun')
                ->label('Kode Akun'),

            ExportColumn::make('nama_akun')
                ->label('Nama Akun'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export data COA berhasil dengan ' .
            number_format($export->successful_rows) .
            ' data berhasil diexport.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' .
                number_format($failedRowsCount) .
                ' data gagal diexport.';
        }

        return $body;
    }
}