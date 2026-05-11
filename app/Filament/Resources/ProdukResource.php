<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Produk;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// form components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

// table components
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
                    ->label('Nama Produk')
                    ->required(),

                Radio::make('status')
                    ->label('Status Ketersediaan')
                    ->options([
                        'Tersedia' => 'Tersedia',
                        'Habis' => 'Habis',
                    ])
                    ->required(),

                // stok
                TextInput::make('stok')
                    ->label('Stok Produk')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {

                        if ($state <= 0) {

                            $set('status', 'Habis');

                        } else {

                            $set('status', 'Tersedia');

                        }

                    }),

                // harga
                TextInput::make('harga_produk')
                    ->label('Harga Produk')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                // tanggal masuk
                DatePicker::make('tanggal_masuk')
                    ->label('Tanggal Masuk Produk')
                    ->required(),

                // foto
                FileUpload::make('foto_produk')
                    ->label('Foto Produk')
                    ->image()
                    ->directory('produk-images')
                    ->required(),

                // deskripsi
                RichEditor::make('deskripsi_produk')
                    ->label('Deskripsi Lengkap')
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Tersedia',
                        'danger' => 'Habis',
                    ]),

                ImageColumn::make('foto_produk')
                    ->label('Foto')
                    ->size(60),

                TextColumn::make('stok')
                    ->label('Stok')
                    ->badge()
                    ->sortable()
                    ->color(fn ($state) =>
                        $state <= 5 ? 'danger' : 'success'
                    ),

                TextColumn::make('harga_produk')
                    ->label('Harga')
                    ->money('IDR'),

                TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->date()
                    ->sortable(),

                TextColumn::make('deskripsi_produk')
                    ->label('Deskripsi')
                    ->html()
                    ->limit(40),

            ])

            ->filters([
                //
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
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}