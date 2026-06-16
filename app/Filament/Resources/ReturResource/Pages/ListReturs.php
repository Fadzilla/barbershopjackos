<?php

namespace App\Filament\Resources\ReturResource\Pages;

use App\Filament\Resources\ReturResource;
use App\Filament\Widgets\ReturInsightWidget;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class ListReturs extends ListRecords
{
    protected static string $resource = ReturResource::class;

    /**
     * Mendaftarkan Widget Analisis Kualitas Supplier AI di bagian atas halaman list retur
     */
    protected function getHeaderWidgets(): array
    {
        return [
            ReturInsightWidget::class,
        ];
    }

    /**
     * Membuat tombol aksi untuk menghapus cache analisis retur dan memaksa Gemini AI menghitung ulang
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refreshAiRetur')
                ->label('Refresh AI Supplier Insights')
                ->icon('heroicon-m-sparkles')
                ->color('danger')
                ->action(function () {
                    // Hapus cache analisis retur agar Gemini AI dipaksa menghitung ulang data riil dari database
                    Cache::forget('barbershop_ai_retur_chart');

                    Notification::make()
                        ->title('Menganalisis Klaim Retur!')
                        ->body('Data kecacatan produk dan performa supplier sedang dihitung ulang secara live oleh Gemini AI.')
                        ->success()
                        ->send();
                    
                    // Muat ulang halaman
                    $this->redirect(static::$resource::getUrl('index'));
                }),
            CreateAction::make(),
        ];
    }
}