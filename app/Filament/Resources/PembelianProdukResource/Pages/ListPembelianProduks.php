<?php

namespace App\Filament\Resources\PembelianProdukResource\Pages;

use App\Filament\Resources\PembelianProdukResource;
use App\Filament\Widgets\PembelianInsightWidget;
use Filament\Actions\Action; // KUNCI SOLUSI: Mengimpor class Action secara spesifik
use Filament\Actions\CreateAction; // KUNCI SOLUSI: Mengimpor class CreateAction secara spesifik
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

// NAMA CLASS: Harus sama persis dengan nama file fisik Anda 'ListPembelianProduk.php'
class ListPembelianProduks extends ListRecords
{
    protected static string $resource = PembelianProdukResource::class;

    /**
     * Mendaftarkan Widget Analisis Pembelian AI tepat di atas tabel pembelian
     */
    protected function getHeaderWidgets(): array
    {
        return [
            PembelianInsightWidget::class,
        ];
    }

    /**
     * Membuat tombol aksi untuk hapus cache dan kemas kini analisis AI secara langsung
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('refreshAiPurchase')
                ->label('Refresh AI Purchase Insights')
                ->icon('heroicon-m-sparkles')
                ->color('warning')
                ->action(function () {
                    // Hapus cache analisis pembelian lama agar sistem memanggil semula Gemini AI
                    Cache::forget('barbershop_ai_purchase_chart');

                    Notification::make()
                        ->title('Menganalisis Perbelanjaan Modal!')
                        ->body('Data bekalan pembekal sedang dikira semula secara langsung oleh Gemini AI.')
                        ->success()
                        ->send();
                    
                    // Segarkan halaman untuk memuat data baru
                    $this->redirect(static::$resource::getUrl('index'));
                }),
            CreateAction::make(),
        ];
    }
}