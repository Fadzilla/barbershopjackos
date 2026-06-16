<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PembelianInsightWidget extends BaseWidget
{
    // Mengatur agar widget memenuhi lebar layar secara horizontal (lebar penuh)
    protected int | string | array $columnSpan = 'full';

    /**
     * Menyusun data statistik murni menggunakan PHP & komponen Stat Filament
     */
    protected function getStats(): array
    {
        $data = $this->getInsightData();
        $stats = [];

        // 1. Tampilkan 3 Produk Teratas Penyerap Anggaran Terbesar
        $items = $data['items'] ?? [];
        foreach ($items as $index => $item) {
            $peringkat = $index + 1;
            $stats[] = Stat::make(
                label: "Peringkat {$peringkat}: " . ($item['produk'] ?? 'Produk'),
                value: $item['keterangan'] ?? 'N/A'
            )
            ->description($item['analisis'] ?? '')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('warning');
        }

        // 2. Tambahkan Kartu Analisis Keuangan
        $stats[] = Stat::make(
            label: "💰 Keuangan & Anggaran",
            value: "Rekomendasi AI"
        )
        ->description($data['saran_keuangan'] ?? '')
        ->color('success');

        // 3. Tambahkan Kartu Strategi Stok
        $stats[] = Stat::make(
            label: "🚀 Stok & Logistik",
            value: "Panduan Suplai"
        )
        ->description($data['saran_stok'] ?? '')
        ->color('info');

        return $stats;
    }

    /**
     * Mengambil dan mengolah data analisis belanja modal dari Gemini AI (Murni PHP Array)
     */
    protected function getInsightData(): array
    {
        $apiKey = trim(env('GEMINI_API_KEY', ''));

        return Cache::remember('barbershop_ai_purchase_chart', 600, function () use ($apiKey) {
            
            // 1. Ambil data aktual transaksi restock dari database secara aman
            try {
                $riwayatPembelian = DB::table('pembelian_items')
                    ->join('produks', 'pembelian_items.produk_id', '=', 'produks.id')
                    ->select('produks.nama_produk', DB::raw('SUM(pembelian_items.jumlah) as total_qty'), DB::raw('SUM(pembelian_items.subtotal) as total_modal'))
                    ->groupBy('produks.nama_produk')
                    ->orderByDesc('total_qty')
                    ->take(10)
                    ->get()
                    ->map(function ($item) {
                        return "Produk: {$item->nama_produk}, Qty Restock: {$item->total_qty}, Total Modal: Rp " . number_format($item->total_modal, 0, ',', '.');
                    })
                    ->implode('; ');
            } catch (\Exception $e) {
                $riwayatPembelian = '';
            }

            // Data fallback terstruktur jika transaksi masih kosong atau API mati
            $fallbackData = [
                'items' => [
                    ['produk' => 'Pomade Matte', 'keterangan' => '25 Pcs - Rp 1.250.000', 'analisis' => 'Perputaran sangat cepat, amankan stok.'],
                    ['produk' => 'Hair Powder', 'keterangan' => '15 Pcs - Rp 750.000', 'analisis' => 'Margin tinggi, pertahankan pasokan.'],
                ],
                'saran_keuangan' => 'Fokuskan sisa anggaran belanja pada produk best-seller dengan keuntungan bersih di atas 30%.',
                'saran_stok' => 'Jaga batas stok minimal sebanyak 5 pcs untuk mencegah kekosongan suplai saat jam sibuk.'
            ];

            if (empty($apiKey)) {
                return $fallbackData;
            }

            // 2. Prompt instruksi murni terstruktur dalam format JSON dengan pembatasan panjang kalimat agar muat di kartu metrik
            $promptTeks = "Bertindaklah sebagai penasihat keuangan dan stok opname barbershop profesional. Berdasarkan data pengeluaran stok produk berikut: [" . $riwayatPembelian . "]. " .
                          "Buatlah peringkat maksimal 3 produk yang paling banyak menyerap anggaran belanja modal. " .
                          "Kamu WAJIB mengembalikan respons HANYA berupa JSON objek murni tanpa penanda markdown (seperti tag pembungkus kode json) dengan struktur persis seperti contoh ini: " .
                          "{" .
                          "  \"items\": [" .
                          "    {\"produk\": \"Nama Produk\", \"keterangan\": \"Deskripsi singkat modal & qty\", \"analisis\": \"Analisis super singkat maksimal 10 kata\"}" .
                          "  ]," .
                          "  \"saran_keuangan\": \"Kalimat saran keuangan super singkat maksimal 12 kata\"," .
                          "  \"saran_stok\": \"Kalimat saran manajemen stok super singkat maksimal 12 kata\"" .
                          "}";

            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-preview-09-2025:generateContent?key=" . $apiKey;

                $response = Http::post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $promptTeks]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $hasilAi = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                    
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
                // Gunakan data fallback jika koneksi API terganggu
            }

            return $fallbackData;
        });
    }
}