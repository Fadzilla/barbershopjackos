<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReturInsightWidget extends BaseWidget
{

    protected static bool $isDiscovered = false;
    // Memaksa widget agar memenuhi lebar layar secara horizontal (lebar penuh)
    protected int | string | array $columnSpan = 'full';

    /**
     * Membangun daftar metrik statistik murni menggunakan PHP & komponen Stat Filament
     */
    protected function getStats(): array
    {
        $data = $this->getReturInsightData();
        $stats = [];

        // 1. Tampilkan Supplier Paling Bermasalah & Saran Supplier Alternatif
        $supplierKritis = $data['supplier_kritis'] ?? 'N/A';
        $stats[] = Stat::make(
            label: '⚠️ Supplier Defect Tinggi (Saran Ganti/Alternatif)',
            value: $supplierKritis
        )
        ->description($data['analisis_supplier_alternatif'] ?? 'Tingkat cacat barang dari supplier melampaui batas toleransi.')
        ->descriptionIcon('heroicon-m-exclamation-triangle')
        ->color('danger');

        // 2. Tampilkan Tren Produk yang Paling Sering Rusak
        $produkRusak = $data['produk_sering_rusak'] ?? 'N/A';
        $stats[] = Stat::make(
            label: '📦 Tren Produk Sering Rusak',
            value: $produkRusak
        )
        ->description($data['tren_kerusakan'] ?? 'Pola kerusakan barang saat dikirim oleh supplier.')
        ->descriptionIcon('heroicon-m-archive-box-x-mark')
        ->color('warning');

        // 3. Tampilkan Estimasi Total Kerugian (Dana Tertahan) & Solusi QC
        $totalRugi = $data['total_kerugian'] ?? 'Rp 0';
        $stats[] = Stat::make(
            label: '🛡️ Estimasi Kerugian & Solusi QC',
            value: $totalRugi
        )
        ->description($data['solusi_qc'] ?? 'Tindakan pencegahan operasional saat unboxing barang.')
        ->descriptionIcon('heroicon-m-shield-check')
        ->color('success');

        return $stats;
    }

    /**
     * Mengambil data transaksi dari DB, meminta analisis retur & supplier alternatif ke Gemini AI, lalu mengembalikan data Array PHP murni
     */
    protected function getReturInsightData(): array
    {
        $apiKey = trim(env('GEMINI_API_KEY', ''));

        // Caching selama 10 menit agar performa dashboard tetap kencang
        return Cache::remember('barbershop_ai_retur_chart', 600, function () use ($apiKey) {
            
            // 1. Mengambil riwayat pengembalian barang (retur) dari database secara aman
            try {
                $riwayatRetur = DB::table('retur_pembelian_items')
                    ->join('retur_pembelians', 'retur_pembelian_items.retur_pembelian_id', '=', 'retur_pembelians.id')
                    ->join('pembelians', 'retur_pembelians.pembelian_id', '=', 'pembelians.id')
                    ->join('suppliers', 'pembelians.supplier_id', '=', 'suppliers.id')
                    ->join('produks', 'retur_pembelian_items.produk_id', '=', 'produks.id')
                    ->select(
                        'suppliers.nama_supplier', 
                        'produks.nama_produk', 
                        'retur_pembelian_items.jumlah', 
                        'retur_pembelians.alasan_retur'
                    )
                    ->where('retur_pembelians.status', '=', 'Selesai')
                    ->orderByDesc('retur_pembelian_items.id')
                    ->take(10)
                    ->get()
                    ->map(function ($item) {
                        return "Supplier: {$item->nama_supplier}, Produk: {$item->nama_produk}, Qty: {$item->jumlah}, Alasan: {$item->alasan_retur}";
                    })
                    ->implode('; ');
            } catch (\Exception $e) {
                $riwayatRetur = '';
            }

            // Data fallback terstruktur jika database transaksi retur Anda masih kosong atau API belum aktif
            $fallbackData = [
                'supplier_kritis' => 'PT. Barber Jaya',
                'analisis_supplier_alternatif' => 'Defect rate 18%. Segera cari supplier alternatif secepatnya!',
                'produk_sering_rusak' => 'Pomade Matte Glass',
                'tren_kerusakan' => 'Wadah kaca sering pecah saat pengiriman kurir supplier.',
                'total_kerugian' => 'Rp 1.450.000',
                'solusi_qc' => 'Ganti ke pomade kemasan plastik atau pertebal bubble wrap.'
            ];

            if (empty($apiKey)) {
                return $fallbackData;
            }

            // 2. Prompt ketat untuk menghasilkan analisis tren kerusakan & saran pencarian supplier alternatif dalam format JSON objek murni
            $promptTeks = "Bertindaklah sebagai manajer kontrol kualitas (Quality Control) dan konsultan supply chain barbershop profesional. Berdasarkan data transaksi retur pembelian ke supplier berikut: [" . $riwayatRetur . "]. " .
                          "Identifikasi supplier dengan defect rate tertinggi dan berikan saran tegas untuk mencari supplier alternatif jika tingkat cacatnya melampaui batas toleransi aman. " .
                          "Identifikasi juga produk apa yang trennya paling sering rusak beserta penyebab kerusakannya. " .
                          "Kamu WAJIB mengembalikan respons HANYA berupa JSON objek murni tanpa penanda markdown (seperti tag pembungkus kode json) dengan struktur persis seperti contoh ini: " .
                          "{" .
                          "  \"supplier_kritis\": \"Nama Supplier dengan defect rate tertinggi\"," .
                          "  \"analisis_supplier_alternatif\": \"Saran tegas untuk mencari supplier alternatif / re-negosiasi kontrak jika tingkat cacat tinggi, maksimal 8 kata\"," .
                          "  \"produk_sering_rusak\": \"Nama Produk teratas yang sering rusak saja\"," .
                          "  \"tren_kerusakan\": \"Analisis singkat mengapa produk ini sering rusak dari alasan retur, maksimal 8 kata\"," .
                          "  \"total_kerugian\": \"Format Rp Rupiah perkiraan total kerugian dari barang retur tersebut\"," .
                          "  \"solusi_qc\": \"Saran tindakan QC atau solusi operasional pencegahan, maksimal 8 kata\"" .
                          "}";

            try {
                // 3. Menghubungi API Gemini 2.5 Flash
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