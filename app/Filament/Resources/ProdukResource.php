<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

//  komponen form
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

// komponen table
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;

class ProdukResource extends Resource
{
   protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Produk';

    // tambahan buat grup masterdata
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

                DatePicker::make('tanggal_masuk')
                    ->label('Tanggal Masuk Produk')
                    ->required(),

                FileUpload::make('foto_produk')
                    ->label('Foto Produk (JPG/PNG)')
                    ->image()
                    ->directory('produk-images')
                    ->required(),

                RichEditor::make('deskripsi_produk')
                    ->label('Deskripsi Lengkap')
                    ->columnSpan(2),
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

                // Kolom Status dengan Badge Warna
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Tersedia', // Hijau
                        'danger' => 'Habis',     // Merah
                    ]),

                ImageColumn::make('foto_produk')
                    ->label('Foto')
                    ->size(50),

                TextColumn::make('tanggal_masuk')
                    ->label('Tgl Masuk')
                    ->date()
                    ->sortable(),

                TextColumn::make('deskripsi_produk')
                    ->label('Deskripsi')
                    ->html() // Agar format teks dari RichEditor muncul rapi
                    ->limit(50),
            ])
            ->filters([
                //
            ])
            ->actions([
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
