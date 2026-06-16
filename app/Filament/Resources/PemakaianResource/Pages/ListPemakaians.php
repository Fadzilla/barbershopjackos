<?php

namespace App\Filament\Resources\PemakaianResource\Pages;

use App\Filament\Resources\PemakaianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use App\Models\Pemakaian;
use App\Models\PemakaianInsight;

class ListPemakaians extends ListRecords
{
    protected static string $resource = PemakaianResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\PemakaianInsightWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generatePemakaianInsight')
                ->label('Refresh AI Insights')
                ->icon('heroicon-m-sparkles')
                ->color('warning') 
                ->requiresConfirmation()
                ->modalHeading('Update Analisis Pemakaian')
                ->modalDescription('Sistem akan menghubungi Gemini AI untuk menganalisis transaksi pemakaian terbaru. Lanjutkan?')
                ->action(function () {
                    $apiKey = env('GEMINI_API_KEY');

                    if (!$apiKey) {
                        Notification::make()
                            ->title('API Key Gemini belum diatur di file .env')
                            ->danger()
                            ->send();
                        return;
                    }

                    $transaksiTerakhir = Pemakaian::latest()->first();

                    if (!$transaksiTerakhir) {
                        Notification::make()
                            ->title('Belum ada data transaksi untuk dianalisis.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $sudahAda = PemakaianInsight::where('pemakaian_id', $transaksiTerakhir->id)->exists();
                    if ($sudahAda) {
                        Notification::make()
                            ->title('Transaksi terbaru sudah dianalisis. Buat transaksi baru dulu.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $keterangan = $transaksiTerakhir->Keterangan ?? 'Tanpa keterangan';
                    $nomorPemakaian = $transaksiTerakhir->nomer_pemakaian ?? 'Terbaru';
                    $jumlahPemakaian = $transaksiTerakhir->total_pemakaian ?? 1;
                    
                    $promptTeks = "Bertindaklah sebagai Analis Stok. Berikan analisis tren dan rekomendasi stok yang SANGAT SINGKAT maksimal 2-3 kalimat untuk pemakaian nomor {$nomorPemakaian} sejumlah {$jumlahPemakaian} unit dengan catatan '{$keterangan}'. " .
                                  "Wajib ditulis menyambung dalam satu paragraf polos tanpa judul, tanpa poin-poin, dan DILARANG MENGGUNAKAN format cetak tebal asteris (**), tanda pagar (#), atau strip (-).";

                    try {
                        $response = Http::post("https://generativelanguage.googleapis.com/v1/models/gemini-3.5-flash:generateContent?key=" . $apiKey, [
                            'contents' => [
                                ['parts' => [['text' => $promptTeks]]]
                            ]
                        ]);

                        $data = $response->json();

                        if ($response->successful() && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                            $hasilAi = $data['candidates'][0]['content']['parts'][0]['text'];

                            PemakaianInsight::create([
                                'pemakaian_id' => $transaksiTerakhir->id,
                                'nama_produk' => 'Produk Terpakai', 
                                'jumlah_pemakaian' => $jumlahPemakaian,
                                'analisis_ai' => trim($hasilAi),
                            ]);

                            Notification::make()
                                ->title('AI Insight Berhasil Ditambahkan!')
                                ->success()
                                ->send();
                            
                            $this->redirect(static::$resource::getUrl('index'));
                        } else {
                            $pesanError = $data['error']['message'] ?? 'Respons tidak sesuai format API.';
                            
                            Notification::make()
                                ->title('Gagal API Gemini: ' . $pesanError)
                                ->danger()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error Sistem: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}