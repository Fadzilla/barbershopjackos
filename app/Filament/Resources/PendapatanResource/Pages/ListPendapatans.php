<?php

namespace App\Filament\Resources\PendapatanResource\Pages;

use App\Filament\Resources\PendapatanResource;
use App\Filament\Widgets\PendapatanInsightWidget;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class ListPendapatans extends ListRecords
{
    protected static string $resource = PendapatanResource::class;

    /**
     * Mendaftarkan Widget Analisis Pendapatan AI murni di bagian atas halaman
     */
    protected function getHeaderWidgets(): array
    {
        return [
            PendapatanInsightWidget::class,
        ];
    }

    /**
     * Membuat tombol aksi hapus cache analisis pendapatan dan memicu ulang Gemini AI secara paksa
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refreshAiRevenue')
                ->label('Refresh AI Revenue Insights')
                ->icon('heroicon-m-sparkles')
                ->color('success')
                ->action(function () {
                    // Hapus cache analisis pendapatan agar Gemini AI dipaksa menghitung ulang dari data riil
                    Cache::forget('barbershop_ai_revenue_chart');

                    Notification::make()
                        ->title('Menganalisis Pendapatan Jasa!')
                        ->body('Data riwayat kasir dan performa kapster Anda sedang dianalisis ulang secara live oleh Gemini AI.')
                        ->success()
                        ->send();
                    
                    // Muat ulang halaman
                    $this->redirect(static::$resource::getUrl('index'));
                }),
            CreateAction::make(),
        ];
    }
}