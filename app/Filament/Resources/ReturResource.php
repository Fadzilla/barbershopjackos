<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReturResource\Pages;
use App\Models\Retur;
use Filament\Forms; 
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// FORM
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;

// TABLE
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;

// MODEL
use App\Models\Produk;

// PDF
use Barryvdh\DomPDF\Facade\Pdf;

// CONTROLLER EMAIL
use App\Http\Controllers\PengirimanEmailReturController;

class ReturResource extends Resource
{
    protected static ?string $model = Retur::class;

    protected static ?string $navigationIcon =
        'heroicon-o-arrow-uturn-left';

    protected static ?string $navigationLabel =
        'Retur';

    protected static ?string $navigationGroup =
        'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Wizard::make([

                    Wizard\Step::make('Data Retur')
                        ->schema([

                            TextInput::make('kode_retur')
                                ->label('Kode Retur')
                                ->default(fn () =>
                                    Retur::getKodeRetur()
                                )
                                ->readonly()
                                ->required(),

                            Select::make('karyawan_id')
                                ->label('Pegawai')
                                ->relationship(
                                    'pegawai',
                                    'nama_pegawai'
                                )
                                ->searchable()
                                ->required(),

                            DatePicker::make('tanggal_retur')
                                ->label('Tanggal Retur')
                                ->default(now())
                                ->required(),

                        ])
                        ->columns(3),

                    Wizard\Step::make('Produk Retur')
                        ->schema([

                            Select::make('produk_id')
                                ->label('Nama Produk')
                                ->relationship(
                                    'produk',
                                    'nama_produk'
                                )
                                ->searchable()
                                ->reactive()
                                ->required()

                                ->afterStateUpdated(function (
                                    $state,
                                    $set
                                ) {

                                    $produk =
                                        Produk::find($state);

                                    $set(
                                        'harga_per_unit',
                                        $produk?->harga_produk ?? 0
                                    );

                                    $set(
                                        'total',
                                        ($produk?->harga_produk ?? 0)
                                        * 1
                                    );
                                }),

                            TextInput::make('qty')
                                ->label('Jumlah Retur')
                                ->numeric()
                                ->default(1)
                                ->reactive()
                                ->required()

                                ->afterStateUpdated(function (
                                    $state,
                                    $set,
                                    Get $get
                                ) {

                                    $set(
                                        'total',

                                        (float) $get(
                                            'harga_per_unit'
                                        )

                                        *

                                        (int) $state
                                    );
                                }),

                            TextInput::make('harga_per_unit')
                                ->label('Harga per Unit')
                                ->numeric()
                                ->readonly()
                                ->required(),

                            TextInput::make('total')
                                ->label('Total')
                                ->numeric()
                                ->readonly()
                                ->required(),

                        ])
                        ->columns(2),

                    Wizard\Step::make('Konfirmasi')
                        ->schema([

                            Placeholder::make(
                                'preview_retur'
                            )

                                ->label('Preview Retur')

                                ->content(function (
                                    Get $get
                                ) {

                                    $produk =
                                        Produk::find(
                                            $get('produk_id')
                                        );

                                    return
                                        'Produk : '
                                        . ($produk->nama_produk ?? '-')

                                        . "\nJumlah : "
                                        . $get('qty')

                                        . "\nHarga : Rp "
                                        . number_format(
                                            $get(
                                                'harga_per_unit'
                                            ),
                                            0,
                                            ',',
                                            '.'
                                        )

                                        . "\nTotal : Rp "
                                        . number_format(
                                            $get('total'),
                                            0,
                                            ',',
                                            '.'
                                        );
                                }),

                            Select::make('status')
                                ->label('Status')
                                ->options([

                                    'Diproses' =>
                                        'Diproses',

                                    'Selesai' =>
                                        'Selesai',

                                ])
                                ->default('Diproses')
                                ->required(),

                            Select::make('alasan')
                                ->label('Alasan Retur')
                                ->options([

                                    'Salah Kirim' =>
                                        'Salah Kirim',

                                    'Barang Rusak' =>
                                        'Barang Rusak',

                                ])
                                ->required(),

                            FileUpload::make('foto')
                                ->label('Foto Bukti')
                                ->image()
                                ->directory(
                                    'retur-images'
                                ),

                        ])

                ])
                ->columnSpanFull()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([

                TextColumn::make(
                    'kode_retur'
                )
                    ->label('Kode Retur')
                    ->searchable(),

                TextColumn::make(
                    'pegawai.nama_pegawai'
                )
                    ->label('Pegawai'),

                TextColumn::make(
                    'produk.nama_produk'
                )
                    ->label('Produk'),

                BadgeColumn::make('status')
                    ->colors([

                        'warning' =>
                            'Diproses',

                        'success' =>
                            'Selesai',

                    ]),

                TextColumn::make('qty')
                    ->label('Qty'),

                TextColumn::make(
                    'harga_per_unit'
                )
                    ->money('IDR'),

                TextColumn::make('total')
                    ->money('IDR'),

                ImageColumn::make('foto')
                    ->size(50),

                TextColumn::make(
                    'tanggal_retur'
                )
                    ->date(),

            ])

            ->headerActions([

                Tables\Actions\Action::make('download_pdf')

                    ->label('Unduh PDF')

                    ->icon('heroicon-o-document-arrow-down')

                    ->color('success')

                    ->action(function () {

                        $returs = Retur::with([
                            'pegawai',
                            'produk'
                        ])->get();

                        $pdf = Pdf::loadView(

                            'pdf.returs',

                            [
                                'returs' => $returs
                            ]
                        );

                        return response()->streamDownload(

                            fn () => print(
                                $pdf->output()
                            ),

                            'laporan-retur.pdf'
                        );
                    }),

            ])

            ->actions([

                Tables\Actions\Action::make('kirim_email')

                    ->label('Kirim Email')

                    ->icon('heroicon-o-envelope')

                    ->color('warning')

                    ->action(function ($record) {

                        PengirimanEmailReturController::
                            proses_kirim_email_retur(
                                $record->id
                            );

                    })

                    ->requiresConfirmation(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),

            ])

            ->bulkActions([

                Tables\Actions\DeleteBulkAction::make(),

            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [

            'index' =>
                Pages\ListReturs::route('/'),

            'create' =>
                Pages\CreateRetur::route('/create'),

            'edit' =>
                Pages\EditRetur::route('/{record}/edit'),

        ];
    }
}