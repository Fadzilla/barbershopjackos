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

// Table Columns
use Filament\Tables\Columns\TextColumn;

class PembelianProdukResource extends Resource
{
    protected static ?string $model = PembelianProduk::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Pembelian Produk';

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Nama Pegawai
                Select::make('pegawai_id')
                    ->relationship('pegawai', 'nama_pegawai')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->required(),

                // Nama Produk
                Select::make('produk_id')
                    ->relationship('produk', 'nama_produk')
                    ->label('Nama Produk')
                    ->searchable()
                    ->required(),

                // Tanggal
                DatePicker::make('tanggal')
                    ->required(),

                // Harga Per Unit
                TextInput::make('harga_per_unit')
                    ->numeric()
                    ->required(),

                // Total
                TextInput::make('total')
                    ->numeric()
                    ->required(),

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return $table
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

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelianProduks::route('/'),
            'create' => Pages\CreatePembelianProduk::route('/create'),
            'edit' => Pages\EditPembelianProduk::route('/{record}/edit'),
        ];
    }
}