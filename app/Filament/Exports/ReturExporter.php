<?php

namespace App\Filament\Exports;

use App\Models\Retur;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ReturExporter extends Exporter
{
    protected static ?string $model = Retur::class;

    public static function getColumns(): array
    {
        return [

            // pegawai
            ExportColumn::make('pegawai.nama_pegawai')
                ->label('Nama Pegawai'),

            // produk
            ExportColumn::make('produk.nama_produk')
                ->label('Nama Produk'),

            // status
            ExportColumn::make('status')
                ->label('Status'),

            // alasan
            ExportColumn::make('alasan')
                ->label('Alasan'),

            // harga per unit
            ExportColumn::make('harga_per_unit')
                ->label('Harga Per Unit'),

            // qty
            ExportColumn::make('qty')
                ->label('Qty'),

            // total
            ExportColumn::make('total')
                ->label('Total'),

            // tanggal retur
            ExportColumn::make('tanggal_retur')
                ->label('Tanggal Retur'),

            // dibuat
            ExportColumn::make('created_at')
                ->label('Dibuat Pada'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export data retur berhasil. ' .
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