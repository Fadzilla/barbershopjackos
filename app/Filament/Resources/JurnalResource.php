<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JurnalResource\Pages;
use App\Models\Jurnal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Number;

// Tambahan komponen form dan tabel sesuai contohmu
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use App\Models\Coa;

class JurnalResource extends Resource
{
    protected static ?string $model = Jurnal::class;

    // Merubah icon menjadi buku terbuka sesuai request-mu
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    // Label Jurnal Umum
    protected static ?string $navigationLabel = 'Jurnal Umum';

    // Grup navigasi dirubah ke Laporan sesuai request-mu
    protected static ?string $navigationGroup = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Deskripsi Jurnal')
                    ->schema([
                        // Menggunakan nama field dari migration/database kamu
                        DatePicker::make('tgl') // Disesuaikan agar sinkron dengan database/service
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),

                        TextInput::make('no_ref') // Menggunakan 'no_ref' agar sinkron dengan database/service
                            ->label('No Referensi')
                            ->maxLength(100),

                        Textarea::make('keterangan') // Menggunakan 'keterangan' agar sinkron dengan database/service
                            ->label('Deskripsi'),
                    ])->columns(1)
                    ->collapsed() // <- Awalnya tertutup sesuai tampilan yang kamu mau
                    ->collapsible(),

                Section::make('Detail Jurnal')
                    ->schema([
                        // Menggunakan nama 'items' namun diarahkan ke relasi 'jurnaldetail' agar aman
                        Repeater::make('items')
                            ->label('Detail Jurnal')
                            ->relationship('jurnaldetail') // Menghubungkan ke relasi jurnaldetail di model Jurnal
                            ->schema([
                                Select::make('coa_id')
                                    ->label('Akun')
                                    ->options(Coa::all()->pluck('nama_akun', 'id'))
                                    ->searchable()
                                    ->required(),
                                TextInput::make('debit')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp')
                                    ->required(),
                                TextInput::make('credit') // Tetap 'credit' sesuai kolom di database kamu
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp')
                                    ->required(),
                                Textarea::make('deskripsi')->label('Keterangan')->rows(2),
                            ])
                            ->columns(1)
                            ->required(),
                    ])
                    ->collapsed() // <- Awalnya tertutup sesuai tampilan yang kamu mau
                    ->collapsible(),
            ])
            ->columns(1); // Layout diatur menjadi 1 kolom penuh
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tgl') // Menggunakan field 'tanggal' dari database kamu
                    ->label('Tanggal')
                    ->date('d/m/Y'),
                TextColumn::make('no_referensi') // Menggunakan field 'no_ref' dari database kamu
                    ->label('Ref'),
                TextColumn::make('deskripsi') // Menggunakan field 'keterangan' dari database kamu
                    ->label('Deskripsi')
                    ->formatStateUsing(function ($state) {
                        if (blank($state)) {
                            return '-';
                        }

                        // Memecah string berdasarkan karakter ' - '
                        // Contoh: "Pendapatan - Faktur F-0000066" akan dipecah menjadi array
                        $parts = explode(' - ', $state);

                        // Ambil elemen pertama (indeks 0), yaitu kata "Pendapatan"
                        return trim($parts[0]);
                    })
                    ->limit(30),
                TextColumn::make('jurnaldetail.debit')
                    ->label('Total Debit')
                    ->formatStateUsing(function ($record) {
                        // Menghitung jumlah debit dari relasi jurnaldetail
                        $debit = $record->jurnaldetail()->sum('debit');
                        return Number::currency($debit, 'IDR', 'id'); // Aman dan tidak butuh helper eksternal
                    })
                    ->alignment('end'),
                TextColumn::make('jurnaldetail.credit')
                    ->label('Total Kredit')
                    ->formatStateUsing(function ($record) {
                        // Menghitung jumlah credit dari relasi jurnaldetail
                        $credit = $record->jurnaldetail()->sum('credit');
                        return Number::currency($credit, 'IDR', 'id');
                    })
                    ->alignment('end'),
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
            ])
            ->defaultSort('tgl', 'desc'); // Diurutkan berdasarkan tanggal terbaru kamu
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
            'index' => Pages\Listjurnal::route('/'),
            'create' => Pages\CreateJurnal::route('/create'),
            'edit' => Pages\EditJurnal::route('/{record}/edit'),
        ];
    }
}