<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianProdukResource\Pages;
use App\Models\PembelianProduk;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;

// Form Components
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

// Wizard
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

// Table Columns
use Filament\Tables\Columns\TextColumn;

// Actions
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportAction;

// PDF
use Barryvdh\DomPDF\Facade\Pdf;

// Export
use App\Filament\Exports\PembelianProdukExporter;

// Mail
use Illuminate\Support\Facades\Mail;
use App\Mail\PembelianProdukMail;

class PembelianProdukResource extends Resource
{
    protected static ?string $model = PembelianProduk::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Pembelian Produk';

    // GROUP MENU
    protected static ?string $navigationGroup = 'Transaksi';

    //form


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Wizard::make([

                    // 1 untuk data pegawai

                    Step::make('Data Pegawai')
                        ->schema([

                            Select::make('pegawai_id')
                                ->relationship('pegawai', 'nama_pegawai')
                                ->label('Nama Pegawai')
                                ->searchable()
                                ->required(),

                            DatePicker::make('tanggal')
                                ->required(),

                        ]),

                    // 2 untruk data produk

                    Step::make('Data Produk')
                        ->schema([

                            TextInput::make('no_faktur')
                                ->label('No Faktur')
                                ->required(),

                            Select::make('produk_id')
                                ->relationship('produk', 'nama_produk')
                                ->label('Nama Produk')
                                ->searchable()
                                ->required(),

                            TextInput::make('qty')
                                ->label('Quantity')
                                ->numeric()
                                ->live()
                                ->required()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                    $harga = (int) $get('harga_per_unit');

                                    $qty = (int) $state;

                                    $set('total', $qty * $harga);
                                }),

                            TextInput::make('harga_per_unit')
                                ->label('Harga Satuan')
                                ->numeric()
                                ->live()
                                ->required()
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                    $qty = (int) $get('qty');

                                    $harga = (int) $state;

                                    $set('total', $qty * $harga);
                                }),

                        ]),

                    // total pembelian

                    Step::make('Total Pembelian')
                        ->schema([

                            TextInput::make('total')
                                ->numeric()
                                ->readOnly()
                                ->prefix('Rp'),

                        ]),

                ])
                ->columnSpanFull()

            ]);
    }

    // tabelnya

    public static function table(Table $table): Table
    {
        return $table

            ->headerActions([

                // untuk export excel

                ExportAction::make()
                    ->label('Unduh Excel')
                    ->exporter(PembelianProdukExporter::class),

               // untuk export pdf

                Action::make('pdf')

                    ->label('Unduh PDF')

                    ->color('success')

                    ->icon('heroicon-o-document-arrow-down')

                    ->action(function () {

                        $data = PembelianProduk::all();

                        $pdf = Pdf::loadView('pdf.pembelian_produk', [
                            'data' => $data
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'laporan-pembelian-produk.pdf'
                        );
                    }),

            ])

            ->columns([

                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Nama Pegawai')
                    ->searchable(),

                TextColumn::make('produk.nama_produk')
                    ->label('Nama Produk')
                    ->searchable(),

                TextColumn::make('tanggal')
                    ->date(),

                TextColumn::make('harga_per_unit')
                    ->money('IDR'),

                TextColumn::make('total')
                    ->money('IDR'),

                TextColumn::make('no_faktur')
                    ->label('No Faktur'),

                TextColumn::make('qty')
                    ->label('Qty'),

            ])

            ->actions([

                // untuk mengirim email
                Action::make('email')

                    ->label('Kirim Email')

                    ->icon('heroicon-o-envelope')

                    ->color('warning')

                    ->action(function ($record) {

                        Mail::to('test@gmail.com')
                            ->send(new PembelianProdukMail($record));

                    }),

                // untuk mengedit

                Tables\Actions\EditAction::make(),

                //untuk mendelete

                Tables\Actions\DeleteAction::make(),

            ])

            ->bulkActions([

                Tables\Actions\DeleteBulkAction::make(),

            ]);
    }

   // untuk relasinya

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    // untuk pages

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelianProduks::route('/'),
            'create' => Pages\CreatePembelianProduk::route('/create'),
            'edit' => Pages\EditPembelianProduk::route('/{record}/edit'),
        ];
    }
}