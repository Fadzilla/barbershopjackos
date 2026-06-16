<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Penjualan;

class PenjualanChartWidget extends ChartWidget
{
    // Judul utama yang tampil di atas box grafik panel admin
    protected static ?string $heading = '📊 Analisis Tren Produk Terlaris (Gemini AI)';

    // Memaksa widget agar memenuhi lebar layar (100% Horizontal)
    protected int | string | array $columnSpan = 'full';

    // Tinggi area grafik agar 10 bar produk memiliki ruang yang cukup dan rapi
    protected static ?string $maxHeight = '450px';

    /**
     * Menentukan tipe grafik. Kita gunakan tipe 'bar' (batang).
     */
    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Memproses data penjualan riil, meminta analisis Gemini AI, dan menyajikannya ke grafik.
     */
    protected function getData(): array
    {
        $apiKey = trim(env('GEMINI_API_KEY', ''));

        // Menggunakan cache 'barbershop_ai_sales_chart' agar tersinkronisasi dengan tombol Refresh manual
        $dataGrafik = Cache::remember('barbershop_ai_sales_chart', 600, function () use ($apiKey) {
            
            // 1. Ambil sampel penjualan teratas untuk dianalisis oleh AI
            try {
                $riwayatPenjualan = Penjualan::selectRaw('nama_produk, SUM(jumlah_terjual) as total')
                    ->groupBy('nama_produk')
                    ->orderByDesc('total')
                    ->take(15)
                    ->get()
                    ->map(function ($item) {
                        return "Produk: {$item->nama_produk}, Total Terjual: {$item->total}";
                    })
                    ->implode('; ');
            } catch (\Exception $e) {
                $riwayatPenjualan = '';
            }

            // Data cadangan berisi 10 produk jika database barbershop Anda masih kosong
            if (empty($riwayatPenjualan)) {
                $riwayatPenjualan = "Pomade Heavy: 12; Hair Powder: 8; Hair Tonic: 5; Beard Serum: 3; Hair Clay: 2";
            }

            // 2. Prompt ketat untuk memaksa Gemini membalas dalam format JSON Array murni berisi 10 produk
            // Dibuat aman tanpa penulisan backtick markdown agar tidak memicu bug pemotongan teks lagi
            $promptTeks = "Bertindaklah sebagai analis bisnis barbershop profesional. Berdasarkan data penjualan terakhir ini: [" . $riwayatPenjualan . "]. " .
                          "Tentukan peringkat produk terlaris atau best seller saat ini. " .
                          "Kamu WAJIB merespons HANYA berupa JSON array objek murni (DILARANG menggunakan format markdown, tag pembungkus kode, atau penjelasan teks tambahan apa pun) dengan format contoh berikut: " .
                          '[{"produk": "Pomade Premium", "skor": 95}, {"produk": "Hair Powder", "skor": 80}]. ' .
                          "Batasi analisis maksimal 10 produk teratas saja.";

            // Format data default (10 item) jika API Key kosong atau terjadi kegagalan sistem
            $fallbackData = [
                ['produk' => 'Pomade Matte (Default)', 'skor' => 95],
                ['produk' => 'Hair Powder (Default)', 'skor' => 85],
                ['produk' => 'Hair Tonic (Default)', 'skor' => 75],
                ['produk' => 'Beard Oil (Default)', 'skor' => 65],
                ['produk' => 'Shampoo Premium (Default)', 'skor' => 55],
                ['produk' => 'Hair Clay (Default)', 'skor' => 45],
                ['produk' => 'Conditioner (Default)', 'skor' => 35],
                ['produk' => 'Face Wash (Default)', 'skor' => 25],
                ['produk' => 'Hair Serum (Default)', 'skor' => 15],
                ['produk' => 'Pomade Waterbased (Default)', 'skor' => 5],
            ];

            if (empty($apiKey)) {
                return $fallbackData;
            }

            try {
                // 3. Menghubungi API Gemini 2.5 Flash Preview dengan URL bersih
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-preview-09-2025:generateContent?key=" . $apiKey;

                $response = Http::post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $promptTeks]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $hasilAi = $data['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                    
                    // Bersihkan sisa tag markdown jika AI menyertakannya secara tidak sengaja
                    $jsonBersih = trim($hasilAi);
                    
                    if (str_starts_with($jsonBersih, '```json')) {
                        $jsonBersih = substr($jsonBersih, 7);
                    } elseif (str_starts_with($jsonBersih, '```')) {
                        $jsonBersih = substr($jsonBersih, 3);
                    }
                    
                    if (str_ends_with($jsonBersih, '```')) {
                        $jsonBersih = substr($jsonBersih, 0, -3);
                    }
                    
                    $jsonBersih = trim($jsonBersih);
                    $jsonParsed = json_decode($jsonBersih, true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($jsonParsed)) {
                        return $jsonParsed;
                    }
                }
            } catch (\Exception $e) {
                // Mengembalikan data fallback jika terjadi gangguan koneksi
            }

            return $fallbackData;
        });

        // 4. Memisahkan data produk dan skor hasil AI untuk sumbu grafik (Chart.js)
        $labels = array_column($dataGrafik, 'produk');
        $skorData = array_column($dataGrafik, 'skor');

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Rekomendasi Tingkat Keterlarisan (%)',
                    'data' => $skorData,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)', // Warna amber khas AI
                    'borderColor' => 'rgb(245, 158, 11)',         // Garis luar amber solid
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    /**
     * Mengatur konfigurasi Chart.js agar grafik batangnya memanjang horizontal ke samping.
     */
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Memutar grafik batang menjadi horizontal ke samping
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                ],
                'y' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}