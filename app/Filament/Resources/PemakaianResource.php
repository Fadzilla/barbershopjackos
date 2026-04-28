<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemakaianResource\Pages;
use App\Models\Pemakaian;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// components
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;

// model
use App\Models\Pegawai;
use App\Models\Produk;
use App\Models\PemakaianProduk;

// DB
use Illuminate\Support\Facades\DB;

class PemakaianResource extends Resource
{
    protected static ?string $model = Pemakaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Pemakaian';

    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    
                    Wizard\Step::make('Pemakaian')
                        ->schema([
                            Forms\Components\Section::make('Data Pemakaian')
                                ->schema([
                                    TextInput::make('no_pemakaian')
                                        ->default(fn () => Pemakaian::getKodePemakaian())
                                        ->readonly()
                                        ->required(),

                                    DateTimePicker::make('tgl')
                                        ->default(now())
                                        ->required(),

                                    Select::make('pegawai_id')
                                        ->label('Pegawai')
                                        ->options(Pegawai::pluck('nama_pegawai', 'id'))
                                        ->required(),

                                    TextInput::make('status')
                                        ->default('pending')
                                        ->hidden(),
                                ])
                                ->columns(3),
                        ]),

                    Wizard\Step::make('Produk')
                        ->schema([

                            Repeater::make('items')
                                ->relationship('pemakaianProduk')
                                ->schema([

                                    Select::make('produk_id')
                                        ->label('Produk')
                                        ->options(Produk::pluck('nama_produk', 'id'))
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, $set) {
                                            $produk = Produk::find($state);
                                            $set('harga', $produk?->harga ?? 0);
                                        }),

                                    TextInput::make('harga')
                                        ->numeric()
                                        ->required(),

                                    TextInput::make('qty')
                                        ->numeric()
                                        ->default(1)
                                        ->required(),
                                ])
                                ->columns(3)
                                ->addable()
                                ->required(),

                            Forms\Components\Actions::make([
                                Forms\Components\Actions\Action::make('proses')
                                    ->label('Proses')
                                    ->action(function ($get) {

                                        $pemakaian = Pemakaian::updateOrCreate(
                                            ['no_pemakaian' => $get('no_pemakaian')],
                                            [
                                                'tgl' => $get('tgl'),
                                                'pegawai_id' => $get('pegawai_id'),
                                                'status' => 'pending',
                                                'total' => 0
                                            ]
                                        );

                                        foreach ($get('items') as $item) {

                                            PemakaianProduk::updateOrCreate(
                                                [
                                                    'pemakaian_id' => $pemakaian->id,
                                                    'produk_id' => $item['produk_id']
                                                ],
                                                [
                                                    'harga' => $item['harga'],
                                                    'qty' => $item['qty'],
                                                ]
                                            );

                                            // kurangi stok
                                            $produk = Produk::find($item['produk_id']);
                                            if ($produk) {
                                                $produk->decrement('stok', $item['qty']);
                                            }
                                        }

                                        // hitung total
                                        $total = PemakaianProduk::where('pemakaian_id', $pemakaian->id)
                                            ->sum(DB::raw('harga * qty'));

                                        $pemakaian->update(['total' => $total]);
                                    })
                                    ->color('primary')
                            ])
                        ]),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_pemakaian')->label('No Pemakaian'),

                TextColumn::make('pegawai.nama_pegawai')
                    ->label('Pegawai'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    }),

                TextColumn::make('total')
                    ->money('IDR')
                    ->alignment('end'),

                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
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