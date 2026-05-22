<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Produk;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;

// Form Components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

// Table Columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('nama_produk')
                    ->required(),

                Radio::make('status')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Habis' => 'Habis',
                    ])
                    ->required(),

                // ✅ TAMBAHAN STOK (TIDAK MERUBAH YANG LAIN)
                TextInput::make('stok')
                    ->label('Stok Produk')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                TextInput::make('harga_produk')
                    ->label('Harga Produk')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                DatePicker::make('tanggal_masuk')
                    ->required(),

                FileUpload::make('foto_produk')
                    ->image()
                    ->directory('produk-images'),

                RichEditor::make('deskripsi_produk')
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('nama_produk')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'Tersedia',
                        'danger' => 'Habis',
                    ]),

                TextColumn::make('stok'),

                ImageColumn::make('foto_produk')
                    ->size(50),

                TextColumn::make('tanggal_masuk')
                    ->date(),

                TextColumn::make('deskripsi_produk')
                    ->limit(50),

                // ✅ TAMBAHAN STOK (TIDAK MERUBAH YANG LAIN)
                TextColumn::make('stok')
                    ->label('Stok')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state <= 5 ? 'danger' : 'success'),

                TextColumn::make('harga_produk')
                    ->label('Harga Produk')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}