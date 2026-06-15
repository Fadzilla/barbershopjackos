<?php

namespace App\Filament\Resources\JurnalResource\Pages;

use App\Filament\Resources\JurnalResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Grid;
use Illuminate\Support\Number;

class ViewJurnal extends ViewRecord
{
    protected static string $resource = JurnalResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Jurnal')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('no_jurnal')->label('No. Jurnal'),
                                TextEntry::make('tanggal')->label('Tanggal')->date('d/m/Y'),
                                TextEntry::make('no_ref')->label('No. Referensi'),
                                TextEntry::make('sumber')->label('Sumber Transaksi')
                                    ->badge()
                                    ->color(fn($state) => match($state) {
                                        'pendapatan' => 'primary',
                                        'penjualan' => 'success',
                                        'pembelian' => 'warning',
                                        'pemakaian' => 'info',
                                        'retur' => 'danger',
                                        default => 'gray',
                                    }),
                                TextEntry::make('keterangan')->label('Keterangan')->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Detail Jurnal')
                    ->schema([
                        RepeatableEntry::make('details')
                            ->label('')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('coa.kode_akun')->label('Kode'),
                                        TextEntry::make('coa.nama_akun')->label('Nama Akun'),
                                        TextEntry::make('debit')
                                            ->label('Debit')
                                            ->formatStateUsing(fn($state) => $state > 0 ? Number::currency($state, 'IDR') : '-'),
                                        TextEntry::make('kredit')
                                            ->label('Kredit')
                                            ->formatStateUsing(fn($state) => $state > 0 ? Number::currency($state, 'IDR') : '-'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),

                
            ]);
    }
}