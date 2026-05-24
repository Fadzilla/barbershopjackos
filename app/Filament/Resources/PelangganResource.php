<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PelangganResource\Pages;
use App\Filament\Resources\PelangganResource\RelationManagers;
use App\Models\Pelanggan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// tambahan
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload; //untuk tipe file
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker; 

// tambahan untuk pelanggan exporter
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\PelangganExporter;

// tambahan untuk tombol unduh pdf
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf; // Kalau kamu pakai DomPDF
use Illuminate\Support\Facades\Storage;


class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('kode_pelanggan')
                    ->default(fn () => Pelanggan::getKodePelanggan()) // Ambil default dari method getKodeBarang
                    ->label('Kode pelanggan')
                    ->required()
                    ->readonly() // Membuat field menjadi read-only
                ,

                Forms\Components\Select::make('user_id')
                    ->label('Pilih Akun User')
                    ->options(function () {
                        // Mengambil data user yang grupnya 'pengguna'
                        // pluck('name', 'id') -> tampilkan 'name' di dropdown, simpan 'id' ke DB
                        return \App\Models\User::where('user_group', 'pengguna')
                            ->pluck('name', 'id');
                })
                ->searchable() // Supaya bisa diketik/dicari namanya
                ->preload()    // Supaya data langsung muncul saat diklik
                ->required()
                ->placeholder('--- Pilih User ---')
                ,
                TextInput::make('nama_pelanggan')
                    ->required()
                    ->placeholder('Masukkan nama pelanggan ') // Placeholder untuk membantu pengguna
                ,
                TextInput::make('no_hp')
                    ->tel() // Biar muncul keyboard angka di HP
                    ->required()
                    ->placeholder('Contoh: 08123456789')
                ,
                Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull() // Biar alamat lebar ke samping
                ,
                Select::make('status') // Ini cara masukin STATUS di Filament
                    ->options([
                        'Biasa' => 'Pelanggan Biasa',
                        'Member' => 'Member VIP',
                    ])
                    ->default('Biasa') // Sesuai dengan migration 
                    ->required()
                ,
                DatePicker::make('tanggal_bergabung')
                    ->label('Tanggal bergabung')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('kode_pelanggan')
                    ->searchable(),
                // agar bisa di search
                TextColumn::make('nama_pelanggan')
                    ->searchable()
                    ->sortable(),
                //
                TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->searchable(),
                //
                TextColumn::make('alamat')
                   ->limit(30), 
                // Kita pakai Badge biar status "Member" kelihatan keren (Warna-warni)
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Member' => 'success', // Warna Hijau
                        'Biasa' => 'gray',     // Warna Abu-abu
                        default => 'gray',     // sesuai di migration
                    }),
                TextColumn::make('tanggal_bergabung')
                    ->label('Tanggal bergabung')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            // tombol tambahan
            ->headerActions([
                // tombol tambahan export csv dan excel
                ExportAction::make()->exporter(PelangganExporter::class)->color('success'),
                // tombol tambahan export pdf
                // ✅ Tombol Unduh PDF
                Action::make('downloadPdf')
                ->label('Unduh PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $pelanggan = Pelanggan::all();

                    $pdf = Pdf::loadView('pdf.pelanggan', ['pelanggan' => $pelanggan]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'user-list.pdf'
                    );
                })
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
            'index' => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }
}