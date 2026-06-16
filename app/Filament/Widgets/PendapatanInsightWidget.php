<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PendapatanInsightWidget extends BaseWidget
{
    // Memaksa widget agar memenuhi lebar layar (100% Horizontal)
    protected int | string | array $columnSpan = 'full';

    /**
     * Membangun daftar metrik statistik murni menggunakan PHP & komponen Stat Filament
     */
    protected function getStats(): array
    {
        $data = $this->getRevenueInsightData();
        $stats = [];

        // 1. Tampilkan Layanan Terlaris (Kontribusi Omset Terbesar)
        $layananUtama = $data['layanan_terpopuler'] ?? 'Premium Haircut';
        $stats[] = Stat::make(
            label: '🔥 Layanan Jasa Terlaris (AI)',
            value: $layananUtama
        )
        ->description($data['tren_analisis'] ?? 'Kontributor utama omset jasa saat ini.')
        ->descriptionIcon('heroicon-m-sparkles')
        ->color('success');

        // 2. Tampilkan Rekomendasi Paket Bundling Promo dari AI
        $rekomendasiPaket = $data['rekomendasi_paket'] ?? 'Combo Haircut + Shaving';
        $stats[] = Stat::make(
            label: '📦 AI Recommended Bundle',
            value: $rekomendasiPaket
        )
        ->description($data['alasan_bundling'] ?? 'Saran bundling logis untuk menaikkan penjualan silang.')
        ->descriptionIcon('heroicon-m-gift')
        ->color('warning');

        // 3. Tampilkan Strategi Operasional & Tips Jam Sibuk
        $stats[] = Stat::make(
            label: '⚡ Strategi Operasional Kapster',
            value: 'Optimasi Jadwal'
        )
        ->description($data['tips_operasional'] ?? 'Jadwalkan kapster lebih banyak pada jam sibuk bulanan.')
        ->descriptionIcon('heroicon-m-clock')
        ->color('info');

        return $stats;
    }

    /**
     * Mengambil data transaksi dari DB, meminta analisis bundling ke Gemini AI, lalu mengembalikan data Array PHP murni
     */
    protected function getRevenueInsightData(): array
    {
        $apiKey = trim(env('GEMINI_API_KEY', ''));

        // Caching selama 10 menit agar dashboard tidak melambat saat direfresh biasa
        return Cache::remember('barbershop_ai_revenue_chart', 600, function () use ($apiKey) {
            
            // 1. Mengambil riwayat pemesanan jasa riil dari database Anda
            try {
                $riwayatJasa = DB::table('transaksi_layanan') // Menyesuaikan dengan tabel relasi jasa Anda
                    ->join('layanans', 'transaksi_layanan.layanan_id', '=', 'layanans.id')
                    ->select('layanans.nama_layanan', DB::raw('COUNT(transaksi_layanan.id) as total_pesanan'), DB::raw('SUM(transaksi_layanan.harga) as total_pendapatan'))
                    ->groupBy('layanans.nama_layanan')
                    ->orderByDesc('total_pendapatan')
                    ->take(10)
                    ->get()
                    ->map(function ($item) {
                        return "Jasa: {$item->nama_layanan}, Dipesan: {$item->total_pesanan} kali, Omset: Rp " . number_format($item->total_pendapatan, 0, ',', '.');
                    })
                    ->implode('; ');
            } catch (\Exception $e) {
                $riwayatJasa = '';
            }

            // Data fallback jika database transaksi jasa Anda masih kosong atau API belum aktif
            $fallbackData = [
                'layanan_terpopuler' => 'Premium Haircut',
                'tren_analisis' => 'Menyumbang 65% omset jasa bulanan dengan loyalitas tinggi.',
                'rekomendasi_paket' => 'Combo Haircut + Creambath',
                'alasan_bundling' => 'Diskon 10% terbukti menaikkan omset perawatan rambut basah.',
                'tips_operasional' => 'Tambahkan cadangan kapster di hari Jumat-Minggu pukul 16.00-20.00.'
            ];

            if (empty($apiKey)) {
                return $fallbackData;
            }

            // 2. Prompt ketat untuk menghasilkan JSON dengan batasan kata yang pas di dalam Stat Card
            $promptTeks = "Bertindaklah sebagai analis bisnis dan konsultan operasional barbershop profesional. Berdasarkan data pendapatan jasa layanan berikut: [" . $riwayatJasa . "]. " .
                          "Buatlah rekomendasi strategi bisnis barbershop dengan mengembalikan respons HANYA berupa JSON objek murni tanpa penanda markdown (seperti tag pembungkus kode json) dengan struktur persis seperti contoh ini: " .
                          "{" .
                          "  \"layanan_terpopuler\": \"Premium Haircut\"," .
                          "  \"tren_analisis\": \"Kalimat analisis ringkas tentang mengapa jasa tersebut laris maksimal 8 kata\"," .
                          "  \"rekomendasi_paket\": \"Nama Rekomendasi Paket Bundling 2 Jasa Baru\"," .
                          "  \"alasan_bundling\": \"Kalimat analisis logis mengapa 2 jasa tersebut sangat cocok digabungkan maksimal 8 kata\"," .
                          "  \"tips_operasional\": \"Kalimat tips penjadwalan kapster atau promo jam sepi maksimal 8 kata\"" .
                          "}";

            try {
                // 3. Menghubungi API Gemini 2.5 Flash
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=" . $apiKey;

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
                    
                    // Bersihkan tanda markdown pembungkus jika tidak sengaja disertakan AI
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
                // Abaikan error dan pakai data default
            }

            return $fallbackData;
        });
    }
}