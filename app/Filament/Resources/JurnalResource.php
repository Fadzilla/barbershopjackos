<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JurnalResource\Pages;
use App\Filament\Resources\JurnalResource\RelationManagers;
use App\Models\Jurnal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Number;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JurnalResource\Widgets\JurnalStats;

class JurnalResource extends Resource
{
    protected static ?string $model = Jurnal::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Jurnal Umum';
    protected static ?string $pluralLabel = 'Jurnal Umum';
    protected static ?string $navigationGroup = 'Akuntansi';

    // Nonaktifkan create (karena jurnal dibuat otomatis)
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jurnal')
                    ->schema([
                        Forms\Components\TextInput::make('no_jurnal')
                            ->label('No. Jurnal')
                            ->disabled(),
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->disabled(),
                        Forms\Components\TextInput::make('no_ref')
                            ->label('No. Referensi')
                            ->disabled(),
                        Forms\Components\TextInput::make('sumber')
                            ->label('Sumber Transaksi')
                            ->disabled(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->disabled()
                            ->rows(2),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Jurnal')
                    ->schema([
                        Forms\Components\Repeater::make('details')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('coa_id')
                                    ->label('Akun')
                                    ->relationship('coa', 'nama_akun')
                                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->kode_akun} - {$record->nama_akun}")
                                    ->disabled(),
                                Forms\Components\TextInput::make('debit')
                                    ->label('Debit')
                                    ->numeric()
                                    ->disabled()
                                    ->formatStateUsing(fn($state) => Number::currency($state, 'IDR')),
                                Forms\Components\TextInput::make('kredit')
                                    ->label('Kredit')
                                    ->numeric()
                                    ->disabled()
                                    ->formatStateUsing(fn($state) => Number::currency($state, 'IDR')),
                            ])
                            ->columns(3)
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_jurnal')
                    ->label('No. Jurnal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('no_ref')
                    ->label('No. Referensi')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('No. Referensi disalin'),
                BadgeColumn::make('sumber')
                    ->label('Keterangan')
                    ->colors([
                        'primary' => 'pendapatan',
                        'success' => 'penjualan',
                        'warning' => 'pembelian',
                        'info' => 'pemakaian',
                        'danger' => 'retur',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('sumber')
                    ->label('Sumber Transaksi')
                    ->options([
                        'pendapatan' => 'Pendapatan',
                        'penjualan' => 'Penjualan',
                        'pembelian' => 'Pembelian',
                        'pemakaian' => 'Pemakaian',
                        'retur' => 'Retur',
                    ]),
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari'),
                        Forms\Components\DatePicker::make('sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'], fn($q) => $q->whereDate('tanggal', '>=', $data['dari']))
                            ->when($data['sampai'], fn($q) => $q->whereDate('tanggal', '<=', $data['sampai']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\Listjurnal::route('/'),
            'view' => Pages\ViewJurnal::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            JurnalStats::class,
        ];
    }
}
