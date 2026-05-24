<?php

namespace App\Filament\Resources;

// tambahan
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;

use App\Filament\Resources\CoaResource\Pages;
use App\Models\Coa;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// tambahan pdf
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;

// tambahan export excel
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Exports\CoaExporter;

class CoaResource extends Resource
{
    protected static ?string $model = Coa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'COA';

    // tambahan buat grup masterdata
    protected static ?string $navigationGroup = 'Masterdata';    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(1)
                    ->schema([

                        TextInput::make('header_akun')
                            ->required()
                            ->placeholder('Masukkan header akun'),

                        TextInput::make('kode_akun')
                            ->required()
                            ->placeholder('Masukkan kode akun'),

                        TextInput::make('nama_akun')
                            ->label('Nama Akun')
                            ->autocapitalize('words')
                            ->required()
                            ->placeholder('Masukkan nama akun'),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('header_akun')
                    ->label('Header Akun')
                    ->searchable(),

                TextColumn::make('kode_akun')
                    ->label('Kode Akun')
                    ->searchable(),

                TextColumn::make('nama_akun')
                    ->label('Nama Akun')
                    ->searchable(),

            ])

            ->filters([

                Tables\Filters\SelectFilter::make('header_akun')
                    ->options([
                        'Aset/Aktiva' => 'Aset/Aktiva',
                        'Utang' => 'Utang',
                        'Modal' => 'Modal',
                        'Pendapatan' => 'Pendapatan',
                        'Beban' => 'Beban',
                    ]),

            ])

            ->actions([

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),

            ])

            // header actions
            ->headerActions([

                // export excel
                ExportAction::make()
                    ->exporter(CoaExporter::class)
                    ->color('success'),

                // export pdf
                Action::make('downloadPdf')
                    ->label('Unduh PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function () {

                        $coas = Coa::all();

                        $pdf = Pdf::loadView('pdf.coa', [
                            'coas' => $coas
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'coa-list.pdf'
                        );
                    })

            ])

            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),

                // bulk export excel
                ExportBulkAction::make()
                    ->exporter(CoaExporter::class),

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
            'index' => Pages\ListCoas::route('/'),
            'create' => Pages\CreateCoa::route('/create'),
            'edit' => Pages\EditCoa::route('/{record}/edit'),
        ];
    }
}