<?php

namespace App\Filament\Resources\PenjualanResource\Pages;

use App\Filament\Resources\PenjualanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Penjualan; // Sesuaikan dengan model penjualanmu

class ListPenjualans extends ListRecords
{
    protected static string $resource = PenjualanResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\PenjualanChartWidget::class, // Daftarkan widget grafik di sini
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generatePenjualanInsight')
                ->label('Refresh AI Chart Insights')
                ->icon('heroicon-m-sparkles')
                ->color('danger') // Warna merah/pink kontras untuk penjualan
                ->requiresConfirmation()
                ->action(function () {
                    $apiKey = env('GEMINI_API_KEY');

                    if (!$apiKey) {
                        Notification::make()->title('API Key Gemini belum diatur di .env!')->danger()->send();
                        return;
                    }

                    // Ambil 10 data penjualan produk terakhir sebagai sampel tren barbershop
                    $sampelData = Penjualan::latest()->take(10)->get()->map(function($item) {
                        return "Produk: {$item->nama_produk}, Jumlah: {$item->jumlah_terjual}";
                    })->implode('; ');

                    if (empty($sampelData)) {
                        $sampelData = "Belum ada data transaksi penjualan.";
                    }

                    // PROMPT KETAT: Memaksa Gemini mengembalikan format JSON murni isi nama produk dan skor trennya
                    $promptTeks = "Bertindaklah sebagai Analis Sistem Barbershop. Berdasarkan sampel tren penjualan ini: [{$sampelData}]. " .
                                  "Tentukan 5 produk barbershop yang paling banyak diminati saat ini. " .
                                  "Kamu WAJIB merespons HANYA berupa JSON array objek murni dengan key 'produk' (nama produk singkat) dan 'skor' (angka perkiraan tren 10-100 berdasarkan data). " .
                                  "Contoh format: [{\"produk\": \"Pomade Heavy\", \"skor\": 85}, {\"produk\": \"Hair Powder\", \"skor\": 70}]. " .
                                  "DILARANG memberikan teks pembuka, teks penutup, atau format markdown blok ```json.";

                    try {
                        $response = Http::post("[https://generativelanguage.googleapis.com/v1/models/gemini-3.5-flash:generateContent?key=](https://generativelanguage.googleapis.com/v1/models/gemini-3.5-flash:generateContent?key=)" . $apiKey, [
                            'contents' => [
                                ['parts' => [['text' => $promptTeks]]]
                            ]
                        ]);

                        $data = $response->json();
                        $hasilAi = $data['candidates'][0]['content']['parts'][0]['text'] ?? '[]';

                        // Bersihkan string jika Gemini nakal memberikan tag markdown json
                        $jsonBersih = trim(str_replace(['```json', '```'], '', $hasilAi));
                        $dataGrafik = json_decode($jsonBersih, true);

                        if (json_last_error() === JSON_ERROR_NONE && is_array($dataGrafik)) {
                            // Simpan hasil analisis tren ke dalam Cache selama 24 jam agar tidak boros API
                            Cache::put('barbershop_best_seller_chart', $dataGrafik, 1440);

                            Notification::make()->title('Grafik Tren Produk Sukses Diperbarui oleh AI!')->success()->send();
                        } else {
                            Notification::make()->title('Gagal membaca format JSON dari AI. Coba klik lagi.')->danger()->send();
                        }

                    } catch (\Exception $e) {
                        Notification::make()->title('Error Sistem: ' . $e->getMessage())->danger()->send();
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}