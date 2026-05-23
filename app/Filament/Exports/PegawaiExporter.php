<?php

namespace App\Filament\Exports;

use App\Models\Pegawai;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PegawaiExporter extends Exporter
{
    protected static ?string $model = Pegawai::class;

    public static function getColumns(): array
    {
        return [
            //list kolom apa aja yang mau ditampilkan di export
            ExportColumn::make('id')->label('Id User'),
            ExportColumn::make('kode_pegawai')->label('Kode Pegawai'),
            ExportColumn::make('nama_pegawai')->label('Nama Pegawai'),
            ExportColumn::make('no_telpon_pegawai')->label('No Telpon Pegawai'),
            ExportColumn::make('jabatan')->label('Jabatan'),   
            ExportColumn::make('alamat_pegawai')->label('Alamat Pegawai'),
            ExportColumn::make('status_pegawai')->label('Status Pegawai'),
            
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pegawai export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
