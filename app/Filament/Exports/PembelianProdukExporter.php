<?php

namespace App\Filament\Exports;

use App\Models\PembelianProduk;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PembelianProdukExporter extends Exporter
{
    protected static ?string $model = PembelianProduk::class;

    public static function getColumns(): array
    {
        return [

            ExportColumn::make('pegawai.nama_pegawai')
                ->label('Nama Pegawai'),

            ExportColumn::make('produk.nama_produk')
                ->label('Nama Produk'),

            ExportColumn::make('tanggal')
                ->label('Tanggal'),

            ExportColumn::make('harga_per_unit')
                ->label('Harga Satuan'),

            ExportColumn::make('total')
                ->label('Total'),

            ExportColumn::make('no_faktur')
                ->label('No Faktur'),

            ExportColumn::make('qty')
                ->label('Qty'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Export pembelian produk selesai.';
    }
}