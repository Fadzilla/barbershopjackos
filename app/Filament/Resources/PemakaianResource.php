<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemakaianResource\Pages;
use App\Models\Pemakaian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// tambahan
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Filters\SelectFilter;

// tambahan export
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\Action;
use App\Filament\Exports\PemakaianExporter;

// model
use App\Models\Pegawai;
use App\Models\Produk;
use App\Models\PemakaianProduk;
use App\Models\KonfirmasiPemakaian;

// DB
use Illuminate\Support\Facades\DB;

// pdf
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PemakaianResource extends Resource
{
    protected static ?string $model = Pemakaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Pemakaian';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Wizard::make([

                Wizard\Step::make('Pemakaian')
                    ->schema([

                        Forms\Components\Section::make('Data Pemakaian')
                            ->icon('heroicon-m-document-text')
                            ->schema([

                                TextInput::make('nomer_pemakaian')
                                    ->default(fn() => Pemakaian::getNomerPemakaian())
                                    ->label('Nomor Pemakaian')
                                    ->required()
                                    ->readonly(),

                                DateTimePicker::make('tanggal_pakai')
                                    ->default(now())
                                    ->required(),

                                Select::make('pegawai_id')
                                    ->label('Pegawai')
                                    ->options(Pegawai::pluck('nama_pegawai', 'id')->toArray())
                                    ->required()
                                    ->searchable()
                                    ->placeholder('Pilih Pegawai'),

                                TextInput::make('total_pemakaian')
                                    ->default(0)
                                    ->hidden(),

                                TextInput::make('Keterangan')
                                    ->label('Keterangan')
                                    ->nullable()
                                    ->columnSpanFull(),

                                TextInput::make('status')
                                    ->default('Pending')
                                    ->hidden(),
                            ])
                            ->columns(3)
                            ->collapsible(),
                    ]),

                Wizard\Step::make('Pilih Produk')
                    ->schema([

                        Repeater::make('items')
                            ->dehydrated(false)
                            ->schema([

                                Select::make('produk_id')
                                    ->label('Produk')
                                    ->options(Produk::pluck('nama_produk', 'id')->toArray())
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $produk = Produk::find($state);
                                        $set('stok', $produk ? $produk->stok : 0);
                                    })
                                    ->afterStateHydrated(function ($state, $set) {
                                        $produk = Produk::find($state);
                                        $set('stok', $produk ? $produk->stok : 0);
                                    }),

                                TextInput::make('stok')
                                    ->readonly()
                                    ->dehydrated(false),

                                TextInput::make('jumlah')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->reactive()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $totalPemakaian = collect($get('../../items'))
                                            ->sum(fn($item) => (int) ($item['jumlah'] ?? 0));

                                        $set('../../total_pemakaian', $totalPemakaian);
                                    }),

                                DatePicker::make('tanggal_pakai')
                                    ->default(today())
                                    ->required(),
                            ])
                            ->columns(['md' => 5])
                            ->addable()
                            ->deletable()
                            ->reorderable()
                            ->minItems(1)
                            ->required()
                            ->createItemButtonLabel('Tambah Produk'),

                        Forms\Components\Actions::make([

                            Forms\Components\Actions\Action::make('Simpan Sementara')
                                ->action(function ($get) {

                                    $pemakaian = Pemakaian::updateOrCreate(
                                        ['nomer_pemakaian' => $get('nomer_pemakaian')],
                                        [
                                            'pegawai_id' => $get('pegawai_id'),
                                            'tanggal_pakai' => $get('tanggal_pakai'),
                                            'Keterangan' => $get('Keterangan'),
                                            'total_pemakaian' => 0,
                                        ]
                                    );

                                    foreach ($get('items') as $item) {

                                        PemakaianProduk::updateOrCreate(
                                            [
                                                'pemakaian_id' => $pemakaian->id,
                                                'produk_id' => $item['produk_id']
                                            ],
                                            [
                                                'jumlah' => $item['jumlah'],
                                                'tanggal_pakai' => $item['tanggal_pakai'],
                                            ]
                                        );

                                        $produk = Produk::find($item['produk_id']);

                                        if ($produk) {
                                            $produk->decrement('stok', $item['jumlah']);
                                        }
                                    }

                                    $totalPemakaian = PemakaianProduk::where(
                                        'pemakaian_id',
                                        $pemakaian->id
                                    )->sum('jumlah');

                                    $pemakaian->update([
                                        'total_pemakaian' => $totalPemakaian
                                    ]);
                                })
                                ->label('Proses')
                                ->color('primary'),
                        ])
                    ]),

                Wizard\Step::make('Konfirmasi')
                    ->schema([
                        Placeholder::make('Tabel Konfirmasi')
                            ->content(fn(Get $get) => view(
                                'filament.components.pemakaian-table',
                                [
                                    'pemakaians' => Pemakaian::where(
                                        'nomer_pemakaian',
                                        $get('nomer_pemakaian')
                                    )->get()
                                ]
                            )),
                    ]),

            ])->columnSpan(3)

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('nomer_pemakaian')
                    ->label('Nomor Pemakaian')
                    ->searchable(),

                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Pegawai')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_pemakaian')
                    ->label('Total')
                    ->sortable()
                    ->alignment('end'),

                // ✅ INI YANG KAMU KURANG
                TextColumn::make('Keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime(),
            ])

            ->headerActions([
                ExportAction::make()
                    ->exporter(PemakaianExporter::class)
                    ->color('success'),

                Action::make('downloadPdf')
                    ->label('Unduh PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function () {
                        $pemakaian = Pemakaian::with('pegawai')->get();

                        $pdf = Pdf::loadView('pdf.pemakaian', [
                            'pemakaian' => $pemakaian
                        ]);

                        return response()->streamDownload(
                            fn() => print ($pdf->output()),
                            'pemakaian-list.pdf'
                        );
                    })
            ])

            ->filters([
                SelectFilter::make('pegawai_id')
                    ->label('Filter Pegawai')
                    ->relationship('pegawai', 'nama_pegawai')
                    ->searchable()
                    ->preload(),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemakaians::route('/'),
            'create' => Pages\CreatePemakaian::route('/create'),
            'edit' => Pages\EditPemakaian::route('/{record}/edit'),
        ];
    }
}