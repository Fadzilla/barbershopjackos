<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Components Form
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;

// Components Table
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_pegawai')
                    ->required()
                    ->label('Kode Pegawai'),

                TextInput::make('nama_pegawai')
                    ->required()
                    ->label('Nama Pegawai'),

                TextInput::make('no_telpon_pegawai')
                    ->required()
                    ->label('No Telpon'),

                TextInput::make('jabatan')
                    ->required()
                    ->label('Jabatan'),

                TextInput::make('alamat_pegawai')
                    ->required()
                    ->label('Alamat'),

                Select::make('status_pegawai')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->required()
                    ->label('Status'),

                FileUpload::make('foto')
                    ->directory('foto') 
                    ->required()
                   
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_pegawai')->searchable(),

                TextColumn::make('nama_pegawai')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('no_telpon_pegawai'),

                TextColumn::make('jabatan'),

                TextColumn::make('alamat_pegawai'),

                TextColumn::make('status_pegawai'),

                ImageColumn::make('foto')
                   
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}