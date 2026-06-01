<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianProdukResource\Pages;
use App\Models\PembelianProduk;
use App\Models\Produk;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;

// Form Components
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;

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
                    Step::make('Data Pegawai')
                        ->schema([
                            TextInput::make('no_faktur')
                                ->label('No Faktur')
                                ->required()
                                ->default(function () {
                                    $lastRecord = \App\Models\PembelianProduk::latest('id')->first();
                                    $nextId = $lastRecord ? $lastRecord->id + 1 : 1;
                                    return 'INV' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
                                })
                                ->readOnly(),
                            Select::make('pegawai_id')
                                ->relationship('pegawai', 'nama_pegawai')
                                ->required(),
                            DatePicker::make('tanggal')
                                ->default(now())
                                ->required(),
                        ]),

                    Step::make('Daftar Produk')
                        ->schema([
                            Repeater::make('detailPembelian')
                                ->relationship('detailPembelian')
                                ->live()
                                // Logika hitung total saat baris ditambah/dihapus
                                ->afterStateUpdated(function ($get, $set) {
                                    $items = $get('detailPembelian') ?? [];
                                    $total = 0;
                                    foreach ($items as $item) {
                                        $total += (int) ($item['qty'] ?? 0) * (int) ($item['harga_per_unit'] ?? 0);
                                    }
                                    // Gunakan ../../ untuk keluar dari scope repeater menuju field total di Wizard
                                    $set('../../total', $total);
                                })
                                ->schema([
                                    Select::make('produk_id')
                                        ->relationship('produk', 'nama_produk')
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            // Isi harga otomatis saat produk dipilih
                                            $harga = \App\Models\Produk::find($state)?->harga_produk ?? 0;
                                            $set('harga_per_unit', $harga);

                                            // Langsung hitung ulang total setelah pilih produk
                                            $items = $get('../../detailPembelian') ?? [];
                                            $total = 0;
                                            foreach ($items as $item) {
                                                $total += (int) ($item['qty'] ?? 0) * (int) ($item['harga_per_unit'] ?? 0);
                                            }
                                            $set('../../total', $total);
                                        })
                                        ->columnSpan(2),

                                    TextInput::make('qty')
                                        ->numeric()
                                        ->default(1)
                                        ->live()
                                        ->afterStateUpdated(function ($get, $set) {
                                            // Update total saat qty diketik
                                            $items = $get('../../detailPembelian') ?? [];
                                            $total = 0;
                                            foreach ($items as $item) {
                                                $total += (int) ($item['qty'] ?? 0) * (int) ($item['harga_per_unit'] ?? 0);
                                            }
                                            $set('../../total', $total);
                                        }),

                                    TextInput::make('harga_per_unit')
                                        ->numeric()
                                        ->live()
                                        ->afterStateUpdated(function ($get, $set) {
                                            // Update total saat harga diketik manual
                                            $items = $get('../../detailPembelian') ?? [];
                                            $total = 0;
                                            foreach ($items as $item) {
                                                $total += (int) ($item['qty'] ?? 0) * (int) ($item['harga_per_unit'] ?? 0);
                                            }
                                            $set('../../total', $total);
                                        }),
                                ])->columns(4),
                        ]),

                    Step::make('Total Pembelian')
                        ->schema([
                            TextInput::make('total')
                                ->label('Total Keseluruhan')
                                ->numeric()
                                ->prefix('Rp')
                                ->readOnly()
                                ->required(),
                        ]),
                ])->columnSpanFull(),
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
                            fn() => print ($pdf->output()),
                            'laporan-pembelian-produk.pdf'
                        );
                    }),

            ])

            ->columns([
                Tables\Columns\TextColumn::make('no_faktur')->searchable(),
                Tables\Columns\TextColumn::make('pegawai.nama_pegawai')->label('Pegawai'),
                Tables\Columns\TextColumn::make('tanggal')->date(),
                // Tambahkan kolom ini untuk menampilkan total di tabel
                Tables\Columns\TextColumn::make('total')
                    ->label('Total Bayar')
                    ->state(function (PembelianProduk $record) {
                        return $record->total; // Ini akan memanggil fungsi getTotalAttribute di model
                    })
                    ->money('IDR'),
            ])

            ->actions([

                // untuk mengirim email
                Action::make('email')

                    ->label('Kirim Email')

                    ->icon('heroicon-o-envelope')

                    ->color('warning')

                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Kirim Email')
                    ->modalDescription('Apakah Anda yakin ingin mengirim email faktur ini?')

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