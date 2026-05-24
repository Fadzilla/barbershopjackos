<?php

namespace App\Filament\Exports;

use App\Models\Pelanggan;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PelangganExporter extends Exporter
{
    protected static ?string $model = Pelanggan::class;

    public static function getColumns(): array
    {
        return [
            // List kolom yang disesuaikan dengan Masterdata Pelanggan
    ExportColumn::make('kode_pelanggan')
        ->label('Kode Pelanggan'),
    ExportColumn::make('nama_pelanggan')
        ->label('Nama Pelanggan'),
    ExportColumn::make('no_hp')
        ->label('Nomor HP'),
    ExportColumn::make('alamat')
        ->label('Alamat'),
    ExportColumn::make('status')
        ->label('Status Pelanggan'),
    ExportColumn::make('tanggal_bergabung')
        ->label('Tanggal Bergabung'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pelanggan export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
