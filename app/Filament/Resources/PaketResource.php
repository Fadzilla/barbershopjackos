<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaketResource\Pages;
use App\Filament\Resources\PaketResource\RelationManagers;
use App\Models\Paket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\PaketExporter;
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;


class PaketResource extends Resource
{
    protected static ?string $model = Paket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Masterdata';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('no_paket')
                ->required(),

            Forms\Components\Textarea::make('deskripsi'),

            Forms\Components\TextInput::make('harga')
                ->numeric()
                ->required(),

        ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('no_paket')
                ->searchable(),

            Tables\Columns\TextColumn::make('harga')
                ->money('IDR'),

            Tables\Columns\TextColumn::make('deskripsi')
                ->label('Deskripsi')
                ->limit(50)
                ->wrap(),
        ])

    ->headerActions([

    ExportAction::make()
        ->exporter(PaketExporter::class),

    Action::make('export_pdf')
        ->label('Export PDF')
        ->color('danger')
        ->icon('heroicon-o-document-text')

        ->action(function () {

            $pakets = Paket::all();

            $pdf = Pdf::loadView('pdf.paket-pdf', [
                'pakets' => $pakets
            ]);

            return response()->streamDownload(
                fn () => print($pdf->output()),
                'data-paket.pdf'
            );
        }),

])

        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPakets::route('/'),
            'create' => Pages\CreatePaket::route('/create'),
            'edit' => Pages\EditPaket::route('/{record}/edit'),
        ];
    }
}
